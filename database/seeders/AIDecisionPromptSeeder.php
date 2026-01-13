<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIDecisionPromptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminId = DB::table('users')->value('id') ?? 1;

        DB::table('ai_prompts')->updateOrInsert(
            ['name' => 'ai_decision_task_analysis'],
            [
                'type' => 'system',
                'template' => 'You are an AI Project Manager Assistant. Analyze the following task context and determining if any action is required.
                
Context:
{{ context_json }}

Instructions:
1. Analyze the task status, timeline, and signals.
2. Determine if the task needs attention (overdue, stale, blocked, low engagement).
3. If action is required, provide a concrete recommendation.
4. Return the response in strict JSON format.

JSON Structure:
{
    "requires_action": boolean,
    "recommendation": "string (Actionable advice)",
    "reasoning": ["string", "string"],
    "confidence": float (0.0 to 1.0),
    "alternatives": [
        {
            "action": "string",
            "impact": "Low|Medium|High",
            "description": "string"
        }
    ]
}',
                'description' => 'Analyzes task context to provide management recommendations',
                'variables' => json_encode(['context_json']),
                'version' => '1.0.0',
                'is_active' => true,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
