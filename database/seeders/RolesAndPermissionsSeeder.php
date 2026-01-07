<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // Users Management
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            
            // Roles Management
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            
            // Permissions Management
            'view-permissions',
            'create-permissions',
            'edit-permissions',
            'delete-permissions',
            
            // Projects Management
            'view-projects',
            'create-projects',
            'edit-projects',
            'delete-projects',
            
            // Tasks Management
            'view-tasks',
            'create-tasks',
            'edit-tasks',
            'delete-tasks',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles and assign permissions
        
        // Super Admin - has all permissions
        $superAdmin = Role::create(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - can manage users and view everything
        $admin = Role::create(['name' => 'Admin']);
        $admin->givePermissionTo([
            'view-users', 'create-users', 'edit-users',
            'view-roles',
            'view-permissions',
            'view-projects', 'create-projects', 'edit-projects',
            'view-tasks', 'create-tasks', 'edit-tasks',
        ]);

        // Manager - can manage projects and tasks
        $manager = Role::create(['name' => 'Manager']);
        $manager->givePermissionTo([
            'view-users',
            'view-projects', 'create-projects', 'edit-projects',
            'view-tasks', 'create-tasks', 'edit-tasks', 'delete-tasks',
        ]);

        // User - basic permissions
        $user = Role::create(['name' => 'User']);
        $user->givePermissionTo([
            'view-projects',
            'view-tasks', 'create-tasks', 'edit-tasks',
        ]);

        $this->command->info('Roles and Permissions created successfully!');
    }
}
