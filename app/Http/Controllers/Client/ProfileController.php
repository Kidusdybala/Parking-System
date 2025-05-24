<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ParkingSpot;
use App\Models\Reservation;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('client.profile.edit');
    }

  public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => ['required'],
        'new_password' => [
            'required',
            'string',
            'min:8',
            'regex:/[a-zA-Z]/', // Must contain at least one letter
            'regex:/[0-9]/',    // Must contain at least one number
            'confirmed'
        ],
    ], [
        'new_password.min' => 'The new password must be at least 8 characters.',
        'new_password.regex' => 'The new password must contain both letters and numbers.',
        'new_password.confirmed' => 'The new password confirmation does not match.',
    ]);

    $user = auth()->user();

    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Current password does not match.']);
    }

    $user->password = Hash::make($request->new_password);
    $user->save();

    return redirect()->route('client.profile.edit')->with('success', 'Password updated successfully.');
}

}
