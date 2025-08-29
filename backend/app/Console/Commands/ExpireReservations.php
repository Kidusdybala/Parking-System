<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use App\Models\ParkingSpot;
use Carbon\Carbon;

class ExpireReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire reservations that have been reserved for more than 30 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredReservations = Reservation::where('status', 'reserved')
            ->where('reservation_expires_at', '<', now())
            ->with('parkingSpot')
            ->get();

        $count = 0;
        foreach ($expiredReservations as $reservation) {
            // Cancel the reservation
            $reservation->update([
                'status' => 'cancelled',
                'left_at' => now(),
                'actual_end_time' => now()
            ]);

            // Free up the parking spot
            if ($reservation->parkingSpot) {
                $reservation->parkingSpot->update([
                    'status' => 'available',
                    'reserved_by' => null,
                    'reserved_at' => null
                ]);
            }

            $count++;
        }

        $this->info("Expired {$count} reservations.");
        return 0;
    }
}