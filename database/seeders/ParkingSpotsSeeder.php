<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParkingSpot;

class ParkingSpotsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $spots = [];

        for ($i = 1; $i <= 100; $i++) {
            $spots[] = [
                'spot_number' => 'P' . str_pad($i, 3, '0', STR_PAD_LEFT), // P001, P002, etc.
                'name' => 'Spot ' . $i,
                'location' => 'Level ' . ceil($i / 20) . ' - Section ' . chr(65 + (($i - 1) % 5)), // Level 1-5, Section A-E
                'hourly_rate' => 30.00, // Same price for all
                'status' => 'available',
            ];
        }

        ParkingSpot::insert($spots);
    }
}
