<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@wellametalcorp.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password123'),
            ]
        );
        $admin->assignRole('admin');

        $manager = User::firstOrCreate(
            ['email' => 'manager@wellametalcorp.com'],
            [
                'name' => 'Manager',
                'password' => Hash::make('password123'),
            ]
        );
        $manager->assignRole('manager');

        $secretary = User::firstOrCreate(
            ['email' => 'secretary@wellametalcorp.com'],
            [
                'name' => 'Secretary',
                'password' => Hash::make('password123'),
            ]
        );
        $secretary->assignRole('secretary');
    }
}