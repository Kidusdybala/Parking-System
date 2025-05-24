<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->is_verified) {
            return back()->withErrors(['email' => 'Please check you email or password again.']);
        }

        $request->authenticate();
        $request->session()->regenerate();

        return match ($user->role) {
            0 => redirect()->route('dashboard'),
            1 => redirect()->route('client.parking.manage'),
            2 => redirect()->route('department'),
            3 => redirect()->route('admin.history.manage'),
            default => redirect('/login')->withErrors(['role' => 'Unauthorized role.']),
        };
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

