<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Essential seeders (always run)
        $this->call([
            CoreSystemPromptsSeeder::class, // Crucial: Prompts must exist first
            RolesAndPermissionsSeeder::class,
            AIPermissionsSeeder::class,
            DefaultUserSeeder::class,
        ]);

        // Demo data seeder (only run if enabled via environment variable)
        if (env('SEED_DEMO_DATA', false)) {
            $this->command->info('🎭 Seeding demo data...');
            $this->call(DemoDataSeeder::class);
        } else {
            $this->command->warn('⚠️  Demo data seeding skipped (SEED_DEMO_DATA=false)');
        }

        $this->command->info('✅ All seeders completed successfully!');
    }
}
