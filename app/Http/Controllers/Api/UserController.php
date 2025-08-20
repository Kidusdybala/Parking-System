<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Get all users (Admin only)
     */
    public function index(Request $request)
    {
        // Check if user is admin
        if ($request->user()->role !== 3) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $query = User::query();

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $query->select(['id', 'name', 'email', 'role', 'balance', 'email_verified_at', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Get a specific user (Admin only or own profile)
     */
    public function show(Request $request, $id)
    {
        $currentUser = $request->user();

        // Allow users to view their own profile or admin to view any profile
        if ($currentUser->role !== 3 && $currentUser->id != $id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $user = User::select(['id', 'name', 'email', 'role', 'balance', 'email_verified_at', 'created_at', 'updated_at'])
            ->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Include reservation statistics
        $user->load(['reservations' => function ($query) {
            $query->select(['id', 'user_id', 'status', 'total_cost', 'created_at']);
        }]);

        $reservationStats = [
            'total_reservations' => $user->reservations->count(),
            'active_reservations' => $user->reservations->where('status', 'active')->count(),
            'completed_reservations' => $user->reservations->where('status', 'completed')->count(),
            'cancelled_reservations' => $user->reservations->where('status', 'cancelled')->count(),
            'total_spent' => $user->reservations->whereIn('status', ['active', 'completed'])->sum('total_cost'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'reservation_stats' => $reservationStats
            ]
        ]);
    }

    /**
     * Update user profile
     */
    public function update(Request $request, $id)
    {
        $currentUser = $request->user();

        // Allow users to update their own profile or admin to update any profile
        if ($currentUser->role !== 3 && $currentUser->id != $id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $rules = [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
        ];

        // Only admin can update role and balance
        if ($currentUser->role === 3) {
            $rules['role'] = 'sometimes|integer|in:1,3';
            $rules['balance'] = 'sometimes|numeric|min:0';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->only(['name', 'email']);

        // Only admin can update role and balance
        if ($currentUser->role === 3) {
            if ($request->has('role')) {
                $updateData['role'] = $request->role;
            }
            if ($request->has('balance')) {
                $updateData['balance'] = $request->balance;
            }
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user->only(['id', 'name', 'email', 'role', 'balance', 'email_verified_at', 'created_at', 'updated_at'])
        ]);
    }

    /**
     * Delete a user (Admin only)
     */
    public function destroy(Request $request, $id)
    {
        // Check if user is admin
        if ($request->user()->role !== 3) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Prevent admin from deleting themselves
        if ($user->id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete your own account'
            ], 400);
        }

        // Check if user has active reservations
        $activeReservations = $user->reservations()
            ->where('status', 'active')
            ->where('end_time', '>', now())
            ->count();

        if ($activeReservations > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete user with active reservations'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Add balance to user account
     */
    public function addBalance(Request $request, $id)
    {
        $currentUser = $request->user();

        // Allow users to add to their own balance or admin to add to any balance
        if ($currentUser->role !== 3 && $currentUser->id != $id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01|max:10000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $oldBalance = $user->balance;
        $user->increment('balance', $request->amount);

        return response()->json([
            'success' => true,
            'message' => 'Balance added successfully',
            'data' => [
                'old_balance' => $oldBalance,
                'amount_added' => $request->amount,
                'new_balance' => $user->balance
            ]
        ]);
    }

    /**
     * Get user statistics (Admin only)
     */
    public function statistics(Request $request)
    {
        // Check if user is admin
        if ($request->user()->role !== 3) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $totalUsers = User::count();
        $adminUsers = User::where('role', 3)->count();
        $clientUsers = User::where('role', 1)->count();
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();
        $totalBalance = User::sum('balance');

        // New users this month
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // New users today
        $newUsersToday = User::whereDate('created_at', today())->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => $totalUsers,
                'users_by_role' => [
                    'admin' => $adminUsers,
                    'client' => $clientUsers,
                ],
                'verified_users' => $verifiedUsers,
                'unverified_users' => $totalUsers - $verifiedUsers,
                'total_balance' => $totalBalance,
                'new_users_today' => $newUsersToday,
                'new_users_this_month' => $newUsersThisMonth,
            ]
        ]);
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request, $id)
    {
        $currentUser = $request->user();

        // Allow users to update their own password or admin to update any password
        if ($currentUser->role !== 3 && $currentUser->id != $id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $rules = [
            'new_password' => 'required|string|min:8|confirmed',
        ];

        // If user is updating their own password, require current password
        if ($currentUser->id == $id) {
            $rules['current_password'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify current password if user is updating their own password
        if ($currentUser->id == $id && !Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Revoke all tokens to force re-login
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully. Please login again.'
        ]);
    }
}