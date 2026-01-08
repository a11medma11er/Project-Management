<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Run seeders in order
        $this->call([
            RolesAndPermissionsSeeder::class,
            AIPermissionsSeeder::class,
            DefaultUserSeeder::class,
            DemoDataSeeder::class, // Demo data with projects, tasks, etc.
        ]);

        $this->command->info('All seeders completed successfully!');
    }
}
