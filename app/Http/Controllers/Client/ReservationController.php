<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\ParkingSpot;
use Carbon\Carbon;
use App\Models\User;

class ReservationController extends Controller
{
    public function reserve($spotId)
    {
        $user = auth()->user();
        $spot = ParkingSpot::findOrFail($spotId);

        if ($spot->is_reserved) {
            return redirect()->back()->with('error', 'This spot is already reserved.');
        }

        // Reserve the spot
        $reservation = Reservation::create([
            'user_id' => $user->id,
            'parking_spot_id' => $spot->id,
            'reserved_at' => Carbon::now(),
        ]);

        $spot->update([
            'is_reserved' => true,
        ]);

        return redirect()->back()->with('success', 'Parking reserved for the next 30 minutes.');
    }

}
