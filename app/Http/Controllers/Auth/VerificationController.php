<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class VerificationController extends Controller
{
    public function show()
{
    return view('verification.code');
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
