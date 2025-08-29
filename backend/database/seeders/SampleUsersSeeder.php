<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SampleUsersSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $this->command->info('Creating sample users...');

        // Sample Client Users (Regular users with role = 1)
        $clientUsers = [
            [
                'name' => 'Ahmed Hassan',
                'email' => 'ahmed.hassan@email.com',
                'password' => 'password123',
                'balance' => 250.50,
            ],
            [
                'name' => 'Sara Mohamed',
                'email' => 'sara.mohamed@email.com',
                'password' => 'password123',
                'balance' => 180.75,
            ],
            [
                'name' => 'Michael Johnson',
                'email' => 'michael.johnson@email.com',
                'password' => 'password123',
                'balance' => 320.00,
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@email.com',
                'password' => 'password123',
                'balance' => 95.25,
            ],
            [
                'name' => 'David Wilson',
                'email' => 'david.wilson@email.com',
                'password' => 'password123',
                'balance' => 450.00,
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa.anderson@email.com',
                'password' => 'password123',
                'balance' => 125.80,
            ],
            [
                'name' => 'James Brown',
                'email' => 'james.brown@email.com',
                'password' => 'password123',
                'balance' => 75.50,
            ],
            [
                'name' => 'Maria Garcia',
                'email' => 'maria.garcia@email.com',
                'password' => 'password123',
                'balance' => 200.00,
            ],
        ];

        foreach ($clientUsers as $userData) {
            $existingUser = User::where('email', $userData['email'])->first();

            if (!$existingUser) {
                User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                    'role' => 1, // Regular user role
                    'balance' => $userData['balance'],
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->command->info("Created client user: {$userData['name']} ({$userData['email']})");
            } else {
                $this->command->warn("Client user already exists: {$userData['email']}");
            }
        }

        // Sample Admin Users (Admin users with role = 3)
        $adminUsers = [
            [
                'name' => 'System Administrator',
                'email' => 'sysadmin@parking.com',
                'password' => 'admin123',
            ],
            [
                'name' => 'Parking Manager',
                'email' => 'manager@parking.com',
                'password' => 'admin123',
            ],
            [
                'name' => 'Support Admin',
                'email' => 'support@parking.com',
                'password' => 'admin123',
            ],
        ];

        foreach ($adminUsers as $adminData) {
            $existingAdmin = User::where('email', $adminData['email'])->first();

            if (!$existingAdmin) {
                User::create([
                    'name' => $adminData['name'],
                    'email' => $adminData['email'],
                    'password' => Hash::make($adminData['password']),
                    'role' => 3, // Admin role
                    'balance' => 0.00, // Admins don't need balance
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->command->info("Created admin user: {$adminData['name']} ({$adminData['email']})");
            } else {
                $this->command->warn("Admin user already exists: {$adminData['email']}");
            }
        }

        $this->command->info('Sample users creation completed!');
        $this->command->info('');
        $this->command->info('=== SAMPLE USERS CREATED ===');
        $this->command->info('');
        $this->command->info('CLIENT USERS (Password: password123):');
        foreach ($clientUsers as $user) {
            $this->command->info("• {$user['name']} - {$user['email']} - Balance: {$user['balance']} Birr");
        }
        $this->command->info('');
        $this->command->info('ADMIN USERS (Password: admin123):');
        foreach ($adminUsers as $admin) {
            $this->command->info("• {$admin['name']} - {$admin['email']}");
        }
        $this->command->info('');
        $this->command->info('EXISTING USERS:');
        $this->command->info('• Kidus - kidus@gmail.com (Password: 0968137444)');
        $this->command->info('• Admin User - admin@parking.com (Password: admin123)');
    }
}