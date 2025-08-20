<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ParkingSpot;
use App\Models\Reservation;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ApiDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@parking.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'balance' => 1000.00,
                'email_verified_at' => now(),
            ]
        );

        // Create test client users
        $client1 = User::firstOrCreate(
            ['email' => 'client1@example.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password123'),
                'role' => 'client',
                'balance' => 100.00,
                'email_verified_at' => now(),
            ]
        );

        $client2 = User::firstOrCreate(
            ['email' => 'client2@example.com'],
            [
                'name' => 'Jane Smith',
                'password' => Hash::make('password123'),
                'role' => 'client',
                'balance' => 150.00,
                'email_verified_at' => now(),
            ]
        );



        // Create parking spots
        $parkingSpots = [
            [
                'spot_number' => 'A001',
                'name' => 'Spot A001',
                'location' => 'Building A - Level 1',
                'hourly_rate' => 5.00,
                'status' => 'available',
            ],
            [
                'spot_number' => 'A002',
                'name' => 'Spot A002',
                'location' => 'Building A - Level 1',
                'hourly_rate' => 5.00,
                'status' => 'available',
            ],
            [
                'spot_number' => 'A003',
                'name' => 'Spot A003',
                'location' => 'Building A - Level 1',
                'hourly_rate' => 5.00,
                'status' => 'occupied',
            ],
            [
                'spot_number' => 'B001',
                'name' => 'Spot B001',
                'location' => 'Building B - Level 1',
                'hourly_rate' => 7.50,
                'status' => 'available',
            ],
            [
                'spot_number' => 'B002',
                'name' => 'Spot B002',
                'location' => 'Building B - Level 1',
                'hourly_rate' => 7.50,
                'status' => 'available',
            ],
            [
                'spot_number' => 'B003',
                'name' => 'Spot B003',
                'location' => 'Building B - Level 1',
                'hourly_rate' => 7.50,
                'status' => 'maintenance',
            ],
            [
                'spot_number' => 'C001',
                'name' => 'Spot C001',
                'location' => 'Building C - Level 2',
                'hourly_rate' => 10.00,
                'status' => 'available',
            ],
            [
                'spot_number' => 'C002',
                'name' => 'Spot C002',
                'location' => 'Building C - Level 2',
                'hourly_rate' => 10.00,
                'status' => 'available',
            ],
            [
                'spot_number' => 'VIP001',
                'name' => 'VIP Spot 001',
                'location' => 'VIP Section - Ground Floor',
                'hourly_rate' => 20.00,
                'status' => 'available',
            ],
            [
                'spot_number' => 'VIP002',
                'name' => 'VIP Spot 002',
                'location' => 'VIP Section - Ground Floor',
                'hourly_rate' => 20.00,
                'status' => 'available',
            ],
        ];

        foreach ($parkingSpots as $spotData) {
            ParkingSpot::firstOrCreate(
                ['spot_number' => $spotData['spot_number']],
                $spotData
            );
        }

        // Create some sample reservations
        $spot1 = ParkingSpot::where('spot_number', 'A001')->first();
        $spot2 = ParkingSpot::where('spot_number', 'B001')->first();
        $spot3 = ParkingSpot::where('spot_number', 'A003')->first();

        if ($spot1 && $client1) {
            // Future reservation
            Reservation::firstOrCreate([
                'user_id' => $client1->id,
                'parking_spot_id' => $spot1->id,
                'start_time' => now()->addHours(2),
                'end_time' => now()->addHours(4),
            ], [
                'total_cost' => 10.00,
                'status' => 'active',
            ]);
        }

        if ($spot2 && $client2) {
            // Past completed reservation
            Reservation::firstOrCreate([
                'user_id' => $client2->id,
                'parking_spot_id' => $spot2->id,
                'start_time' => now()->subDays(1),
                'end_time' => now()->subDays(1)->addHours(3),
            ], [
                'total_cost' => 22.50,
                'status' => 'completed',
                'parked_at' => now()->subDays(1),
                'left_at' => now()->subDays(1)->addHours(3),
                'total_price' => 22.50,
                'is_paid' => true,
            ]);
        }

        if ($spot3 && $client1) {
            // Current ongoing reservation
            Reservation::firstOrCreate([
                'user_id' => $client1->id,
                'parking_spot_id' => $spot3->id,
                'start_time' => now()->subHour(),
                'end_time' => now()->addHour(),
            ], [
                'total_cost' => 10.00,
                'status' => 'active',
                'parked_at' => now()->subHour(),
            ]);
        }

        $this->command->info('API test data seeded successfully!');
        $this->command->info('Admin credentials: admin@parking.com / admin123');
        $this->command->info('Client credentials: client1@example.com / password123');
        $this->command->info('Client credentials: client2@example.com / password123');
    }
}