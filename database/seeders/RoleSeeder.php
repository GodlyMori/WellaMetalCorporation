<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Define every permission the system will check for
        $permissions = [
            'manage inventory',
            'record sales',
            'view reports',
            'manage users',
            'archive records',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Create the three roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $secretary = Role::firstOrCreate(['name' => 'secretary']);

        // 3. Assign permissions per role, matching what we agreed on earlier
        $admin->givePermissionTo($permissions); // admin gets everything
        $manager->givePermissionTo($permissions); // manager starts identical to admin

        $secretary->givePermissionTo([
            'manage inventory',
            'record sales',
            'view reports',
        ]); // secretary can't manage users or archive records
    }
}