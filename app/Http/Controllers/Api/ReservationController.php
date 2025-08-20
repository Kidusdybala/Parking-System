<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\ParkingSpot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * Get user's reservations
     */
    public function index(Request $request)
    {
        $query = Reservation::where('user_id', $request->user()->id)
            ->with(['parkingSpot', 'user']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereDate('start_time', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('end_time', '<=', $request->end_date);
        }

        $reservations = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reservations
        ]);
    }

    /**
     * Get all reservations (Admin only)
     */
    public function all(Request $request)
    {
        // Check if user is admin
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $query = Reservation::with(['parkingSpot', 'user']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by parking spot
        if ($request->has('parking_spot_id')) {
            $query->where('parking_spot_id', $request->parking_spot_id);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereDate('start_time', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('end_time', '<=', $request->end_date);
        }

        $reservations = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reservations
        ]);
    }

    /**
     * Get a specific reservation
     */
    public function show(Request $request, $id)
    {
        $query = Reservation::with(['parkingSpot', 'user']);

        // If not admin, only show user's own reservations
        if ($request->user()->role !== 'admin') {
            $query->where('user_id', $request->user()->id);
        }

        $reservation = $query->find($id);

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $reservation
        ]);
    }

    /**
     * Create a new reservation
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parking_spot_id' => 'required|exists:parking_spots,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $parkingSpot = ParkingSpot::find($request->parking_spot_id);

        if ($parkingSpot->status !== 'available') {
            return response()->json([
                'success' => false,
                'message' => 'Parking spot is not available'
            ], 400);
        }

        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);

        // Check for conflicting reservations
        $conflictingReservation = Reservation::where('parking_spot_id', $request->parking_spot_id)
            ->where('status', 'active')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                      ->orWhereBetween('end_time', [$startTime, $endTime])
                      ->orWhere(function ($q) use ($startTime, $endTime) {
                          $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                      });
            })
            ->exists();

        if ($conflictingReservation) {
            return response()->json([
                'success' => false,
                'message' => 'Parking spot is already reserved for the selected time period'
            ], 400);
        }

        // Calculate total cost
        $hours = $startTime->diffInHours($endTime);
        $totalCost = $hours * $parkingSpot->hourly_rate;

        // Check if user has sufficient balance
        $user = $request->user();
        if ($user->balance < $totalCost) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance. Required: $' . $totalCost . ', Available: $' . $user->balance
            ], 400);
        }

        // Create reservation
        $reservation = Reservation::create([
            'user_id' => $user->id,
            'parking_spot_id' => $request->parking_spot_id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_cost' => $totalCost,
            'status' => 'active',
        ]);

        // Deduct cost from user balance
        $user->decrement('balance', $totalCost);

        // Load relationships
        $reservation->load(['parkingSpot', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Reservation created successfully',
            'data' => $reservation
        ], 201);
    }

    /**
     * Update a reservation
     */
    public function update(Request $request, $id)
    {
        $query = Reservation::query();

        // If not admin, only allow updating user's own reservations
        if ($request->user()->role !== 'admin') {
            $query->where('user_id', $request->user()->id);
        }

        $reservation = $query->find($id);

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found'
            ], 404);
        }

        // Only allow updating active reservations that haven't started yet
        if ($reservation->status !== 'active' || $reservation->start_time <= now()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update this reservation'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'start_time' => 'sometimes|date|after:now',
            'end_time' => 'sometimes|date|after:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $startTime = $request->start_time ? Carbon::parse($request->start_time) : $reservation->start_time;
        $endTime = $request->end_time ? Carbon::parse($request->end_time) : $reservation->end_time;

        // Check for conflicting reservations (excluding current reservation)
        $conflictingReservation = Reservation::where('parking_spot_id', $reservation->parking_spot_id)
            ->where('id', '!=', $reservation->id)
            ->where('status', 'active')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                      ->orWhereBetween('end_time', [$startTime, $endTime])
                      ->orWhere(function ($q) use ($startTime, $endTime) {
                          $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                      });
            })
            ->exists();

        if ($conflictingReservation) {
            return response()->json([
                'success' => false,
                'message' => 'Parking spot is already reserved for the selected time period'
            ], 400);
        }

        // Calculate new total cost
        $hours = $startTime->diffInHours($endTime);
        $newTotalCost = $hours * $reservation->parkingSpot->hourly_rate;
        $costDifference = $newTotalCost - $reservation->total_cost;

        $user = User::find($reservation->user_id);

        // Check if user has sufficient balance for additional cost
        if ($costDifference > 0 && $user->balance < $costDifference) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance for the updated reservation. Additional cost: $' . $costDifference
            ], 400);
        }

        // Update reservation
        $reservation->update([
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_cost' => $newTotalCost,
        ]);

        // Adjust user balance
        if ($costDifference > 0) {
            $user->decrement('balance', $costDifference);
        } elseif ($costDifference < 0) {
            $user->increment('balance', abs($costDifference));
        }

        $reservation->load(['parkingSpot', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Reservation updated successfully',
            'data' => $reservation
        ]);
    }

    /**
     * Cancel a reservation
     */
    public function cancel(Request $request, $id)
    {
        $query = Reservation::query();

        // If not admin, only allow canceling user's own reservations
        if ($request->user()->role !== 'admin') {
            $query->where('user_id', $request->user()->id);
        }

        $reservation = $query->find($id);

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found'
            ], 404);
        }

        if ($reservation->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel this reservation'
            ], 400);
        }

        // Calculate refund amount (full refund if canceled before start time)
        $refundAmount = 0;
        if ($reservation->start_time > now()) {
            $refundAmount = $reservation->total_cost;
        }

        // Update reservation status
        $reservation->update(['status' => 'cancelled']);

        // Refund to user balance
        if ($refundAmount > 0) {
            $user = User::find($reservation->user_id);
            $user->increment('balance', $refundAmount);
        }

        $reservation->load(['parkingSpot', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Reservation cancelled successfully',
            'data' => [
                'reservation' => $reservation,
                'refund_amount' => $refundAmount
            ]
        ]);
    }

    /**
     * Complete a reservation (Admin only)
     */
    public function complete(Request $request, $id)
    {
        // Check if user is admin
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $reservation = Reservation::find($id);

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found'
            ], 404);
        }

        if ($reservation->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot complete this reservation'
            ], 400);
        }

        $reservation->update(['status' => 'completed']);
        $reservation->load(['parkingSpot', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Reservation completed successfully',
            'data' => $reservation
        ]);
    }

    /**
     * Get reservation statistics (Admin only)
     */
    public function statistics(Request $request)
    {
        // Check if user is admin
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $totalReservations = Reservation::count();
        $activeReservations = Reservation::where('status', 'active')->count();
        $completedReservations = Reservation::where('status', 'completed')->count();
        $cancelledReservations = Reservation::where('status', 'cancelled')->count();
        $totalRevenue = Reservation::whereIn('status', ['active', 'completed'])->sum('total_cost');

        // Today's statistics
        $todayReservations = Reservation::whereDate('created_at', today())->count();
        $todayRevenue = Reservation::whereDate('created_at', today())
            ->whereIn('status', ['active', 'completed'])
            ->sum('total_cost');

        // This month's statistics
        $monthReservations = Reservation::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $monthRevenue = Reservation::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereIn('status', ['active', 'completed'])
            ->sum('total_cost');

        return response()->json([
            'success' => true,
            'data' => [
                'total_reservations' => $totalReservations,
                'active_reservations' => $activeReservations,
                'completed_reservations' => $completedReservations,
                'cancelled_reservations' => $cancelledReservations,
                'total_revenue' => $totalRevenue,
                'today' => [
                    'reservations' => $todayReservations,
                    'revenue' => $todayRevenue
                ],
                'this_month' => [
                    'reservations' => $monthReservations,
                    'revenue' => $monthRevenue
                ]
            ]
        ]);
    }
}