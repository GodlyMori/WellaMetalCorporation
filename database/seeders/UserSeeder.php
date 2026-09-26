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
            ['email' => 'admin@wellametal.test'],
            [
                'name' => 'Test Admin',
                'password' => Hash::make('password123'),
            ]
        );
        $admin->assignRole('admin');

        $manager = User::firstOrCreate(
            ['email' => 'manager@wellametal.test'],
            [
                'name' => 'Test Manager',
                'password' => Hash::make('password123'),
            ]
        );
        $manager->assignRole('manager');

        $secretary = User::firstOrCreate(
            ['email' => 'secretary@wellametal.test'],
            [
                'name' => 'Test Secretary',
                'password' => Hash::make('password123'),
            ]
        );
        $secretary->assignRole('secretary');
    }
}