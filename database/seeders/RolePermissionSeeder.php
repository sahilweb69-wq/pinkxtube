<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Define base permissions
        $permissions = [
            ['name' => 'View Roles', 'slug' => 'roles.view'],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage'],
            ['name' => 'View Permissions', 'slug' => 'permissions.view'],
            ['name' => 'Manage Permissions', 'slug' => 'permissions.manage'],
            ['name' => 'Manage Users', 'slug' => 'users.manage'],
            ['name' => 'Access Admin', 'slug' => 'admin.access'],
        ];

        $permissionModels = collect($permissions)->map(function ($perm) {
            return Permission::firstOrCreate(['slug' => $perm['slug']], [
                'name' => $perm['name'],
                'description' => $perm['name'],
            ]);
        });

        // Create Admin role
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], [
            'name' => 'Admin',
            'description' => 'Administrator role with full access',
        ]);

        // Attach all permissions to admin
        $adminRole->permissions()->sync($permissionModels->pluck('id'));

        // Create an admin user if none exists
        $adminEmail = 'admin@example.com';
        $adminPassword = '123456789';

        $admin = User::firstOrCreate(['email' => $adminEmail], [
            'name' => 'Administrator',
            'password' => Hash::make($adminPassword),
        ]);

        // Attach Admin role to admin user
        if (!$admin->roles()->where('roles.id', $adminRole->id)->exists()) {
            $admin->roles()->attach($adminRole->id);
        }
    }
}
