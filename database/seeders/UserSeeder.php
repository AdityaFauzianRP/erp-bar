<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Data Branch (Pusat & Cabang)
        $branchPusat = Branch::create([
            'name' => 'Kantor Pusat',
            'code' => 'KCP001',
        ]);

        $branchBandung = Branch::create([
            'name' => 'Cabang Bandung',
            'code' => 'BDG001',
        ]);

        // 2. Buat Role (Spatie)
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $kasirRole = Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);
        $staffGudangRole = Role::firstOrCreate(['name' => 'staff_gudang', 'guard_name' => 'web']);

        // 3. Buat Users
        // Super Admin
        $superadmin = User::firstOrCreate([
            'email' => 'superadmin@example.com',
        ], [
            'name' => 'Super Admin System',
            'password' => Hash::make('password'),
            'active_branch_id' => $branchPusat->id,
        ]);
        $superadmin->branches()->syncWithoutDetaching([$branchPusat->id, $branchBandung->id]);
        $superadmin->assignRole($superAdminRole);

        // Admin
        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin System',
            'password' => Hash::make('password'),
            'active_branch_id' => $branchPusat->id,
        ]);
        $admin->branches()->syncWithoutDetaching([$branchPusat->id]);
        $admin->assignRole($adminRole);

        // Kasir
        $kasir = User::firstOrCreate([
            'email' => 'kasir@example.com',
        ], [
            'name' => 'Kasir Bandung',
            'password' => Hash::make('password'),
            'active_branch_id' => $branchBandung->id,
        ]);
        $kasir->branches()->syncWithoutDetaching([$branchBandung->id]);
        $kasir->assignRole($kasirRole);

        // Staff Gudang
        $staffGudang = User::firstOrCreate([
            'email' => 'gudang@example.com',
        ], [
            'name' => 'Staff Gudang Pusat',
            'password' => Hash::make('password'),
            'active_branch_id' => $branchPusat->id,
        ]);
        $staffGudang->branches()->syncWithoutDetaching([$branchPusat->id]);
        $staffGudang->assignRole($staffGudangRole);

        $this->command->info('Users berhasil dibuat.');
        $this->command->info('superadmin@example.com | password');
        $this->command->info('admin@example.com | password');
        $this->command->info('kasir@example.com | password');
        $this->command->info('gudang@example.com | password');
    }
}