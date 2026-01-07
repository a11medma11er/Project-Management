<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Read SQL files
        $enrichedTasksSQL = file_get_contents(database_path('sql/ai_enriched_tasks.sql'));
        $projectMetricsSQL = file_get_contents(database_path('sql/ai_project_metrics.sql'));

        // Create AI Enriched Tasks View
        DB::statement($enrichedTasksSQL);

        // Create AI Project Metrics View
        DB::statement($projectMetricsSQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS ai_enriched_tasks');
        DB::statement('DROP VIEW IF EXISTS ai_project_metrics');
    }
};
