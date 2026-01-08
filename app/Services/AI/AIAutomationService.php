<?php

namespace App\Services\AI;

use App\Models\AI\AIDecision;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AIAutomationService
{
    protected $decisionEngine;
    protected $guardrailService;

    public function __construct(
        AIDecisionEngine $decisionEngine,
        AIGuardrailService $guardrailService
    ) {
        $this->decisionEngine = $decisionEngine;
        $this->guardrailService = $guardrailService;
    }

    /**
     * Execute automated AI analysis based on triggers
     */
    public function runAutomatedAnalysis(): array
    {
        $results = [
            'tasks_analyzed' => 0,
            'decisions_created' => 0,
            'auto_executed' => 0,
            'errors' => [],
        ];

        try {
            // 1. Check overdue tasks
            $overdueTasks = $this->checkOverdueTasks();
            $results['tasks_analyzed'] += count($overdueTasks);
            
            // 2. Check task priority
            $priorityTasks = $this->checkPriorityAdjustments();
            $results['tasks_analyzed'] += count($priorityTasks);
            
            // 3. Check resource allocation
            $resourceTasks = $this->checkResourceAllocation();
            $results['tasks_analyzed'] += count($resourceTasks);
            
            // 4. Check project health
            $projects = $this->checkProjectHealth();
            $results['tasks_analyzed'] += count($projects);
            
            Log::info('AI Automation completed', $results);
            
        } catch (\Exception $e) {
            $results['errors'][] = $e->getMessage();
            Log::error('AI Automation failed', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    /**
     * Check and analyze overdue tasks
     */
    protected function checkOverdueTasks(): array
    {
        $tasks = Task::where('status', '!=', 'completed')
            ->where('due_date', '<', now())
            ->whereDoesntHave('aiDecisions', function ($query) {
                $query->where('decision_type', 'overdue_task_analysis')
                    ->where('created_at', '>=', now()->subDay());
            })
            ->limit(10)
            ->get();

        foreach ($tasks as $task) {
            try {
                $decision = $this->decisionEngine->analyzeTask($task, 'overdue_task_analysis');
                
                if ($decision && $decision->confidence_score >= 0.9) {
                    // High confidence - can auto-execute with guardrails
                    $this->attemptAutoExecution($decision);
                }
            } catch (\Exception $e) {
                Log::error('Failed to analyze overdue task', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $tasks->toArray();
    }

    /**
     * Check tasks that need priority adjustments
     */
    protected function checkPriorityAdjustments(): array
    {
        $tasks = Task::where('status', 'in_progress')
            ->where(function ($query) {
                $query->where('due_date', '<=', now()->addDays(3))
                    ->orWhereHas('dependencies', function ($q) {
                        $q->where('status', 'completed');
                    });
            })
            ->limit(10)
            ->get();

        foreach ($tasks as $task) {
            try {
                $decision = $this->decisionEngine->analyzeTask($task, 'priority_adjustment');
                
                if ($decision && $decision->confidence_score >= 0.85) {
                    $this->attemptAutoExecution($decision);
                }
            } catch (\Exception $e) {
                Log::error('Failed to check priority', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $tasks->toArray();
    }

    /**
     * Check resource allocation
     */
    protected function checkResourceAllocation(): array
    {
        $tasks = Task::whereNull('assigned_to')
            ->where('status', 'pending')
            ->where('priority', 'high')
            ->limit(5)
            ->get();

        foreach ($tasks as $task) {
            try {
                $decision = $this->decisionEngine->analyzeTask($task, 'resource_allocation');
            } catch (\Exception $e) {
                Log::error('Failed to check resources', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $tasks->toArray();
    }

    /**
     * Check project health
     */
    protected function checkProjectHealth(): array
    {
        $projects = Project::where('status', 'active')
            ->whereHas('tasks', function ($query) {
                $query->where('due_date', '<', now()->addWeek());
            })
            ->limit(5)
            ->get();

        foreach ($projects as $project) {
            try {
                $decision = $this->decisionEngine->analyzeProject($project);
            } catch (\Exception $e) {
                Log::error('Failed to check project health', [
                    'project_id' => $project->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $projects->toArray();
    }

    /**
     * Attempt to auto-execute decision with guardrails
     */
    protected function attemptAutoExecution(AIDecision $decision): bool
    {
        // Check guardrails
        $guardrailCheck = $this->guardrailService->checkDecision($decision);
        
        if (!$guardrailCheck['passed']) {
            Log::warning('Auto-execution blocked by guardrails', [
                'decision_id' => $decision->id,
                'violations' => $guardrailCheck['violations'],
            ]);
            return false;
        }

        // Only auto-execute safe decisions
        $safeTypes = ['priority_adjustment', 'resource_suggestion'];
        
        if (!in_array($decision->decision_type, $safeTypes)) {
            return false;
        }

        try {
            // Execute the decision
            $decision->update([
                'user_action' => 'auto_accepted',
                'executed_at' => now(),
                'execution_result' => ['status' => 'success', 'method' => 'automated'],
            ]);

            Log::info('Decision auto-executed', [
                'decision_id' => $decision->id,
                'type' => $decision->decision_type,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Auto-execution failed', [
                'decision_id' => $decision->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Schedule AI analysis for specific time
     */
    public function scheduleAnalysis(string $type, array $params, Carbon $runAt): void
    {
        // Store in cache or database
        $scheduleKey = "ai_schedule_{$type}_" . $runAt->timestamp;
        
        Cache::put($scheduleKey, [
            'type' => $type,
            'params' => $params,
            'run_at' => $runAt->toIso8601String(),
            'status' => 'pending',
        ], $runAt->diffInMinutes(now()) + 60);

        Log::info('AI analysis scheduled', [
            'type' => $type,
            'run_at' => $runAt->toDateTimeString(),
        ]);
    }

    /**
     * Check and run scheduled analyses
     */
    public function runScheduledAnalyses(): array
    {
        $results = [];
        
        // Get all scheduled analyses (from cache or database)
        // This is simplified - in production, use database
        
        return $results;
    }

    /**
     * Create automation rule
     */
    public function createAutomationRule(array $rule): array
    {
        $validatedRule = $this->validateRule($rule);
        
        // Store rule (cache or database)
        $ruleId = 'rule_' . time();
        
        Cache::put("automation_rule_{$ruleId}", $validatedRule, now()->addYear());
        
        Log::info('Automation rule created', ['rule_id' => $ruleId]);
        
        return [
            'id' => $ruleId,
            'rule' => $validatedRule,
        ];
    }

    /**
     * Validate automation rule
     */
    protected function validateRule(array $rule): array
    {
        $required = ['trigger', 'conditions', 'action'];
        
        foreach ($required as $field) {
            if (!isset($rule[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        return [
            'trigger' => $rule['trigger'], // 'task_created', 'deadline_approaching', etc.
            'conditions' => $rule['conditions'], // Array of conditions
            'action' => $rule['action'], // 'analyze', 'notify', 'auto_execute'
            'enabled' => $rule['enabled'] ?? true,
            'created_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Get active automation rules
     */
    public function getActiveRules(): array
    {
        // In production, fetch from database
        return [];
    }

    /**
     * Smart workload balancing
     */
    public function balanceWorkload(): array
    {
        $users = \App\Models\User::whereHas('tasks', function ($query) {
            $query->where('status', '!=', 'completed');
        })->get();

        $recommendations = [];

        foreach ($users as $user) {
            $activeTasks = $user->tasks()->where('status', '!=', 'completed')->count();
            
            if ($activeTasks > 5) {
                $recommendations[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'active_tasks' => $activeTasks,
                    'recommendation' => 'Consider redistributing tasks',
                ];
            }
        }

        return $recommendations;
    }
}
