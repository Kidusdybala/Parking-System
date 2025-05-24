<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Only create user if it doesn't already exist
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'), // you can change this to your preferred password
                'email_verified_at' => now(),
            ]
        );

        // Seed 100 parking spots
        $this->call(ParkingSpotsSeeder::class);
    }
}
