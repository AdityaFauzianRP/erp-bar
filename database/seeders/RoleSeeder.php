<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Define roles
        $roles = [
            'super_admin',
            'admin',
            'kasir',
            'staff_gudang',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // NOTE: Permissions for each role should ideally be assigned here or via Shield Panel
        // Since we are using Filament Shield, we can assign them through the UI or manually
        // For simplicity, we just create the roles here.
    }
}
