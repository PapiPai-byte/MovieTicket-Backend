<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default Admin account
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role' => UserRole::ADMIN,
                'email_verified_at' => now(),
            ]
        );

        // Sample regular customer account
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Jane Doe',
                'password' => Hash::make('password'),
                'role' => UserRole::USER,
                'email_verified_at' => now(),
            ]
        );
    }
}

