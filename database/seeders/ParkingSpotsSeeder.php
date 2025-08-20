<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParkingSpot;

class ParkingSpotsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing parking spots first
        ParkingSpot::truncate();
        
        // Create exactly 100 parking spots
        for ($i = 1; $i <= 100; $i++) {
            ParkingSpot::create([
                'spot_number' => 'SPOT-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Parking Spot ' . $i,
                'location' => $this->getLocationForSpot($i),
                'hourly_rate' => $this->getHourlyRateForSpot($i),
                'status' => $this->getRandomStatus(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function getLocationForSpot(int $spotNumber): string
    {
        $locations = [
            'Ground Floor - Section A',
            'Ground Floor - Section B', 
            'Ground Floor - Section C',
            'First Floor - Section A',
            'First Floor - Section B',
            'First Floor - Section C',
            'Second Floor - Section A',
            'Second Floor - Section B',
            'Second Floor - Section C',
            'Basement - Section A',
            'Basement - Section B',
            'Basement - Section C',
            'Outdoor - North',
            'Outdoor - South',
            'Outdoor - East',
            'Outdoor - West'
        ];
        
        // Distribute spots evenly across locations
        $locationIndex = ($spotNumber - 1) % count($locations);
        return $locations[$locationIndex];
    }

    private function getHourlyRateForSpot(int $spotNumber): float
    {
        if ($spotNumber >= 1 && $spotNumber <= 20) {
            return 30.00; // Spots 1-20: 30 birr/hour
        } elseif ($spotNumber >= 21 && $spotNumber <= 40) {
            return 25.00; // Spots 21-40: 25 birr/hour
        } else {
            return 20.00; // Spots 41-100: 20 birr/hour
        }
    }

    private function getRandomStatus(): string
    {
        $statuses = ['available', 'occupied', 'maintenance'];
        $weights = [70, 25, 5]; // 70% available, 25% occupied, 5% maintenance
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($weights as $index => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $statuses[$index];
            }
        }
        
        return 'available';
    }
}
