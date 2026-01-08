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
        // Feedback logs table for learning
        Schema::create('ai_feedback_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('decision_id')->constrained('ai_decisions')->onDelete('cascade');
            $table->string('decision_type', 100);
            $table->decimal('confidence_score', 5, 4);
            $table->string('user_action', 50); // accepted, rejected, modified
            $table->boolean('was_correct')->default(false);
            $table->json('context')->nullable();
            $table->timestamp('created_at');
            
            $table->index(['decision_type', 'created_at']);
            $table->index('was_correct');
        });

        // Calibration data table
        Schema::create('ai_calibration_data', function (Blueprint $table) {
            $table->id();
            $table->string('decision_type', 100)->unique();
            $table->json('data'); // stores calibration metrics
            $table->timestamp('updated_at');
            
            $table->index('decision_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_calibration_data');
        Schema::dropIfExists('ai_feedback_logs');
    }
};
