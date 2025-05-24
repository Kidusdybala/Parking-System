<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'verification_code' => ['required', 'digits:6'],
        ]);

        $user = User::where('email', $request->email)
                    ->where('verification_code', $request->verification_code)
                    ->first();

        if (!$user) {
            return back()->withErrors(['verification_code' => 'Invalid verification code.']);
        }

        // Mark user as verified
        $user->update([
            'is_verified' => true,
            'verification_code' => null,
        ]);

        return redirect('/login')->with('message', 'Email verified successfully. Please log in.');
    }
}
