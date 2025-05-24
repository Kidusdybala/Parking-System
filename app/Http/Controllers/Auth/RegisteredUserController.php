<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Validation\Rules\Password;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailVerification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    
                    ->letters()
                    ->numbers(),
            ],
        ]);

        // Generate a random 6-digit verification code
        $verificationCode = mt_rand(100000, 999999);

        // Store email and verification code
        EmailVerification::updateOrCreate(
            ['email' => $request->email],
            ['code' => $verificationCode]
        );

        // Store user details in session before verification
        session([
            'name' => $request->name,
            'password' => Hash::make($request->password)
        ]);

        // Send verification email
        Mail::raw("Your verification code is: $verificationCode", function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Email Verification Code');
        });

        // Redirect to code verification page with email
        return redirect()->route('verification.code')->with([
            'email' => $request->email
        ]);
    }
    }
