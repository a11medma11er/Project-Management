<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // e.g., 'project_health', 'automation_run'
            $table->json('params')->nullable();
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->timestamp('run_at');
            $table->timestamp('completed_at')->nullable();
            $table->json('output')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_schedules');
    }
};
