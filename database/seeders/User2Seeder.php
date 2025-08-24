<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class User2Seeder extends Seeder
{
    public function run(): void
    {
        // Create second user account
        User::create([
            'name' => 'Kidus2',
            'email' => 'kidus2@gmail.com',
            'password' => Hash::make('0968137444'),
            'role' => 1, // Regular user role
            'balance' => 1000.00, // Starting balance
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "User2 created successfully!\n";
        echo "Email: kidus2@gmail.com\n";
        echo "Password: 0968137444\n";
    }
}
