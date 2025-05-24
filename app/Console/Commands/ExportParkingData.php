<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class ExportParkingData extends Command
{
    protected $signature = 'export:parking-data';
    protected $description = 'Export parking history data to a CSV file for ML training';

    public function handle()
    {
        $filePath = storage_path('app/parking_data.csv');
        $file = fopen($filePath, 'w');

        // Add CSV headers
        fputcsv($file, ['user_id', 'parking_spot_id', 'time_spent']);

        // Fetch reservation data
        $reservations = DB::table('reservations')->get();

        foreach ($reservations as $reservation) {
            $timeSpent = strtotime($reservation->left_at) - strtotime($reservation->parked_at);
            fputcsv($file, [$reservation->user_id, $reservation->parking_spot_id, $timeSpent]);
        }

        fclose($file);
        $this->info("Parking data exported to $filePath");
    }
}
