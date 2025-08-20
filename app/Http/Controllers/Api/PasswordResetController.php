<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordReset;
use App\Mail\PasswordResetCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
    /**
     * Send password reset code
     */
    public function sendResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found with this email address.',
            ], 404);
        }

        // Generate 6-digit reset code
        $code = (string) random_int(100000, 999999);
        
        // Store or update the reset code
        PasswordReset::updateOrCreate(
            ['email' => $request->email],
            ['code' => $code, 'created_at' => now()]
        );

        try {
            Mail::to($request->email)->send(new PasswordResetCodeMail($code));
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send password reset email. Please try again later.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset code sent to your email.',
        ]);
    }

    /**
     * Verify reset code
     */
    public function verifyResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $reset = PasswordReset::where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        if (!$reset) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reset code.',
            ], 400);
        }

        // Check if code is expired (valid for 30 minutes)
        if ($reset->created_at->addMinutes(30)->isPast()) {
            $reset->delete();
            return response()->json([
                'success' => false,
                'message' => 'Reset code has expired. Please request a new one.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reset code verified successfully.',
            'reset_token' => $reset->id . ':' . $request->code, // Simple token for next step
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|digits:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $reset = PasswordReset::where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        if (!$reset) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reset code.',
            ], 400);
        }

        // Check if code is expired
        if ($reset->created_at->addMinutes(30)->isPast()) {
            $reset->delete();
            return response()->json([
                'success' => false,
                'message' => 'Reset code has expired. Please request a new one.',
            ], 400);
        }

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Update user password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the reset record
        $reset->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. You can now login with your new password.',
        ]);
    }
}