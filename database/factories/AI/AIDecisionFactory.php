<?php

namespace Database\Factories\AI;

use App\Models\AI\AIDecision;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AI\AIDecision>
 */
class AIDecisionFactory extends Factory
{
    protected $model = AIDecision::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $decisionTypes = [
            'task_analysis',
            'project_breakdown',
            'priority_suggestion',
            'deadline_prediction',
            'resource_allocation',
        ];

        $decisionType = $this->faker->randomElement($decisionTypes);
        $confidenceScore = $this->faker->randomFloat(2, 0.65, 0.95);

        return [
            'task_id' => $this->faker->boolean(60) ? Task::inRandomOrder()->first()?->id : null,
            'project_id' => $this->faker->boolean(40) ? Project::inRandomOrder()->first()?->id : null,
            'decision_type' => $decisionType,
            'ai_response' => $this->generateAIResponse($decisionType),
            'suggested_actions' => $this->generateSuggestedActions($decisionType),
            'confidence_score' => $confidenceScore,
            'reasoning' => $this->generateReasoning($decisionType, $confidenceScore),
            'user_action' => $this->faker->randomElement(['pending', 'accepted', 'rejected', 'modified']),
            'user_feedback' => $this->faker->boolean(50) ? $this->faker->sentence() : null,
            'reviewed_by' => $this->faker->boolean(60) ? User::inRandomOrder()->first()?->id : null,
            'reviewed_at' => $this->faker->boolean(60) ? $this->faker->dateTimeBetween('-1 month', 'now') : null,
        ];
    }

    protected function generateAIResponse(string $type): array
    {
        return match($type) {
            'task_analysis' => [
                'complexity' => $this->faker->randomElement(['low', 'medium', 'high']),
                'estimated_hours' => $this->faker->numberBetween(2, 40),
                'required_skills' => $this->faker->randomElements(['PHP', 'Laravel', 'Vue.js', 'React'], 2),
            ],
            'project_breakdown' => [
                'total_tasks' => $this->faker->numberBetween(10, 50),
                'phases' => $this->faker->numberBetween(3, 6),
                'estimated_duration_weeks' => $this->faker->numberBetween(4, 24),
            ],
            'priority_suggestion' => [
                'recommended_priority' => $this->faker->randomElement(['urgent', 'high', 'medium', 'low']),
                'factors' => ['deadline proximity', 'business impact', 'dependencies'],
            ],
            'deadline_prediction' => [
                'predicted_completion' => $this->faker->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
                'risk_level' => $this->faker->randomElement(['low', 'medium', 'high']),
            ],
            'resource_allocation' => [
                'recommended_team_size' => $this->faker->numberBetween(2, 8),
                'skills_needed' => $this->faker->randomElements(['Backend', 'Frontend', 'DevOps', 'QA'], 3),
            ],
            default => ['analysis' => 'General AI analysis'],
        };
    }

    protected function generateSuggestedActions(string $type): array
    {
        return match($type) {
            'task_analysis' => [
                'Break down into smaller subtasks',
                'Assign to senior developer',
                'Schedule code review',
            ],
            'project_breakdown' => [
                'Create project timeline',
                'Assign project manager',
                'Schedule kick-off meeting',
            ],
            'priority_suggestion' => [
                'Update task priority',
                'Notify team members',
                'Adjust sprint planning',
            ],
            'deadline_prediction' => [
                'Allocate additional resources',
                'Extend deadline',
                'Re-prioritize tasks',
            ],
            'resource_allocation' => [
                'Recruit additional team members',
                'Assign current team members',
                'Schedule training sessions',
            ],
            default => ['Review and approve'],
        };
    }

    protected function generateReasoning(string $type, float $confidence): array
    {
        $reasons = [
            'task_analysis' => ["Based on the task complexity and historical data, this analysis has a confidence score of {$confidence}.", "The task requires specific technical skills and estimated completion time."],
            'project_breakdown' => ["The project breakdown is based on similar projects and industry standards, resulting in a {$confidence} confidence score."],
            'priority_suggestion' => ["Priority suggestion is calculated based on deadline, business impact, and current workload with {$confidence} confidence."],
            'deadline_prediction' => ["Deadline prediction uses historical velocity and current team capacity, achieving {$confidence} confidence level."],
            'resource_allocation' => ["Resource allocation recommendation is based on project scope and team availability with {$confidence} confidence."],
        ];

        return $reasons[$type] ?? ["AI analysis completed with {$confidence} confidence score."];
    }

    /**
     * Pending decision.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_action' => 'pending',
            'user_feedback' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);
    }

    /**
     * Accepted decision.
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_action' => 'accepted',
            'reviewed_by' => User::inRandomOrder()->first()?->id,
            'reviewed_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Rejected decision.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_action' => 'rejected',
            'user_feedback' => 'Not aligned with current strategy',
            'reviewed_by' => User::inRandomOrder()->first()?->id,
            'reviewed_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * High confidence decision.
     */
    public function highConfidence(): static
    {
        return $this->state(fn (array $attributes) => [
            'confidence_score' => $this->faker->randomFloat(2, 0.85, 0.95),
        ]);
    }
}
