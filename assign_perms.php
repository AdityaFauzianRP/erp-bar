<?php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$roles = ['super_admin', 'admin', 'user admin'];

$allPermissions = Permission::all();

foreach ($roles as $roleName) {
    $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
    $role->syncPermissions($allPermissions);
    echo "Assigned all permissions to role: {$roleName}\n";
}
