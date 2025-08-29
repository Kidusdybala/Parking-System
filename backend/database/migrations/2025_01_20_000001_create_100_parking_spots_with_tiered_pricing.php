<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, clear existing parking spots only if table exists
        if (Schema::hasTable('parking_spots')) {
            DB::table('parking_spots')->truncate();
        }
        
        // Create 100 parking spots with tiered pricing
        $spots = [];
        $locations = [
            'Section A',
            'Section B', 
            'Section C',
            'Section D',
            'Section E'
        ];
        
        for ($i = 1; $i <= 100; $i++) {
            // Determine pricing tier
            if ($i <= 20) {
                $hourlyRate = 30.00; // Premium spots
            } elseif ($i <= 40) {
                $hourlyRate = 25.00; // Standard spots
            } else {
                $hourlyRate = 20.00; // Economy spots
            }
            
            // Assign location based on spot number
            $locationIndex = ($i - 1) % count($locations);
            $location = $locations[$locationIndex];
            
            $spots[] = [
                'spot_number' => sprintf('SPOT-%03d', $i),
                'name' => "Parking Spot $i",
                'location' => $location,
                'hourly_rate' => $hourlyRate,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        // Insert all spots in batches only if table exists
        if (Schema::hasTable('parking_spots')) {
            $chunks = array_chunk($spots, 50);
            foreach ($chunks as $chunk) {
                DB::table('parking_spots')->insert($chunk);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('parking_spots')) {
            DB::table('parking_spots')->truncate();
        }
    }
};