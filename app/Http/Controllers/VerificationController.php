<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
class VerificationController extends Controller
{
    public function show(Request $request)
    {
        // Get email from session or request
        $email = session('email') ?? $request->query('email');

        if ($email) {
            // Generate a new verification code
            $verificationCode = mt_rand(100000, 999999);

            // Update the verification code in the database
            EmailVerification::updateOrCreate(
                ['email' => $email],
                ['code' => $verificationCode]
            );

            // Send the new verification code via email
            Mail::raw("Your new verification code is: $verificationCode", function ($message) use ($email) {
                $message->to($email)
                        ->subject('New Email Verification Code');
            });

            // Flash a message about the new code
            session()->flash('message', 'A new verification code has been sent to your email.');
        }

        return view('verify-code');
    }
    public function verify(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'code' => 'required|numeric'
    ]);

    $verification = EmailVerification::where('email', $request->email)
                                     ->where('code', $request->code)
                                     ->first();

    if (!$verification) {
        session()->put('email', $request->email);
        session()->put('name', session('name'));
        session()->put('password', session('password'));

        return redirect()->route('verification.code')
            ->with('error', 'Invalid verification code.')
            ->withInput();
    }

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        $user = User::create([
            'name' => session('name'),
            'email' => $request->email,
            'password' => session('password'),
            'email_verified_at' => now(), // OR use 'is_verified' => 1
        ]);
    } else {
        $user->update(['email_verified_at' => now()]);
    }

    $verification->delete();

    return redirect()->route('login')->with('success', 'Registration successful! Please log in.');
}

}




