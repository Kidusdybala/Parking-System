<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Check if admin already exists
        $existingAdmin = User::where('email', 'admin@parking.com')->first();
        
        if (!$existingAdmin) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@parking.com',
                'password' => Hash::make('admin123'),
                'role' => 3, // Admin role
                'balance' => 10000.00, // Give admin some balance
                'email_verified_at' => now(), // Mark as verified
            ]);
            
            $this->command->info('Admin user created successfully!');
            $this->command->info('Email: admin@parking.com');
            $this->command->info('Password: admin123');
        } else {
            $this->command->info('Admin user already exists!');
        }
    }
}