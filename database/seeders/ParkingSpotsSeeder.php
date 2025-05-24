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
                'name' => 'Spot ' . $i,
                'price_per_hour' => 30, // Same price for all
                'is_reserved' => false,
            ];
        }

        ParkingSpot::insert($spots);
    }
}
