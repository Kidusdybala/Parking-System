<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use Carbon\Carbon;

class CancelExpiredReservations extends Command
{
    protected $signature = 'reservations:cancel-expired';
    protected $description = 'Cancel reservations that have not been parked within 1 minutes';

    public function handle()
    {
        $expiredReservations = Reservation::whereNull('parked_at')
            ->where('reserved_at', '<', Carbon::now()->subMinutes(1))
            ->get();
        foreach ($expiredReservations as $reservation) {
            $reservation->delete();
            $reservation->parkingSpot->update(['is_reserved' => false]);
        }
        $this->info('Expired reservations canceled.');
    }
}
