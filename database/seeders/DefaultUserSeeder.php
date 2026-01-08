<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin User
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Assign Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdmin->assignRole($superAdminRole);
            $this->command->info('Super Admin user created and assigned Super Admin role!');
        } else {
            $this->command->error('Super Admin role not found! Please run RolesAndPermissionsSeeder first.');
        }

        // Create additional demo users (optional)
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin.user@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $admin->assignRole($adminRole);
        }

        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $managerRole = Role::where('name', 'Manager')->first();
        if ($managerRole) {
            $manager->assignRole($managerRole);
        }

        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $userRole = Role::where('name', 'User')->first();
        if ($userRole) {
            $user->assignRole($userRole);
        }

        $this->command->info('All demo users created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Super Admin - Email: admin@example.com, Password: password');
        $this->command->info('Admin - Email: admin.user@example.com, Password: password');
        $this->command->info('Manager - Email: manager@example.com, Password: password');
        $this->command->info('User - Email: user@example.com, Password: password');
    }
}
