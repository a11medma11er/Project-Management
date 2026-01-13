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
    protected $settingsService;
    protected $aiGateway;

    public function __construct(
        AIDecisionEngine $decisionEngine,
        AIGuardrailService $guardrailService,
        AISettingsService $settingsService,
        AIGateway $aiGateway
    ) {
        $this->decisionEngine = $decisionEngine;
        $this->guardrailService = $guardrailService;
        $this->settingsService = $settingsService;
        $this->aiGateway = $aiGateway;
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
            
            // 5. Check CUSTOM rules
            $customStats = $this->checkCustomRules();
            $results['tasks_analyzed'] += $customStats['analyzed'];
            $results['decisions_created'] += $customStats['decisions'];
            
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
                // Check if we already analyzed 'deadline_estimation' recently
                $query->where('decision_type', 'deadline_estimation')
                    ->where('created_at', '>=', now()->subDay());
            })
            ->limit(10)
            ->get();

        foreach ($tasks as $task) {
            try {
                // Use 'deadline_estimation' to trigger the specific prompt
                $decision = $this->decisionEngine->analyzeTask($task, 'deadline_estimation');
                
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
                // Use 'priority_suggestion' to trigger specific prompt
                $decision = $this->decisionEngine->analyzeTask($task, 'priority_suggestion');
                
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
        $tasks = Task::doesntHave('assignedUsers')
            ->where('status', 'pending')
            ->where('priority', 'high')
            ->limit(5)
            ->get();

        foreach ($tasks as $task) {
            try {
                // Use 'assignment_suggestion' to trigger specific prompt
                $decision = $this->decisionEngine->analyzeTask($task, 'assignment_suggestion');
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
    /**
     * Schedule AI analysis for specific time (Database Backed)
     */
    public function scheduleAnalysis(string $type, array $params, Carbon $runAt): void
    {
        \App\Models\AI\AISchedule::create([
            'type' => $type,
            'params' => $params,
            'run_at' => $runAt,
            'status' => 'pending'
        ]);

        Log::info('AI analysis scheduled (DB)', [
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
        
        // Get all pending scheduled analyses that are due
        $schedules = \App\Models\AI\AISchedule::where('status', 'pending')
            ->where('run_at', '<=', now())
            ->get();
            
        foreach ($schedules as $schedule) {
            try {
                // Mark as processing
                $schedule->update(['status' => 'processing']);
                
                // Execute Logic based on type
                $output = [];
                
                if ($schedule->type === 'automation_run') {
                    $output = $this->runAutomatedAnalysis();
                } else {
                    // Placeholder for other types (e.g., reports)
                    $output = ['message' => "Executed {$schedule->type}"];
                }
                
                // Mark as completed
                $schedule->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'output' => $output
                ]);
                
                $results[] = "Executed Schedule #{$schedule->id}: {$schedule->type}";
                
            } catch (\Exception $e) {
                // Mark as failed
                $schedule->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
                
                Log::error("Scheduled AI job #{$schedule->id} failed", ['error' => $e->getMessage()]);
            }
        }

        return $results;
    }

    /**
     * Create automation rule
     */
    /**
     * Create automation rule
     */
    public function createAutomationRule(array $rule): array
    {
        $validatedRule = $this->validateRule($rule);
        
        // Store rule in database
        $newRule = \App\Models\AI\AutomationRule::create($validatedRule);
        
        Log::info('Automation rule created', ['rule_id' => $newRule->id]);
        
        return $newRule->toArray();
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
            'name' => $rule['name'] ?? 'Untitled Rule',
            'trigger' => $rule['trigger'], 
            'conditions' => $rule['conditions'],
            'action' => $rule['action'],
            'enabled' => $rule['enabled'] ?? true,
            'is_active' => $rule['enabled'] ?? true,
        ];
    }

    /**
     * Get active automation rules
     */
    public function getActiveRules(): array
    {
        return \App\Models\AI\AutomationRule::where('is_active', true)
            ->get()
            ->toArray();
    }

    /**
     * Evaluate custom rules for a task
     */
    /**
     * Check valid custom rules against active tasks
     */
    protected function checkCustomRules(): array
    {
        $stats = ['analyzed' => 0, 'decisions' => 0];
        
        // Get active tasks (pending/in_progress)
        $tasks = Task::whereIn('status', ['pending', 'in_progress'])
            ->with(['assignedUsers', 'project'])
            ->limit(20) // Batch size for safety
            ->get();
            
        $stats['analyzed'] = $tasks->count();
        
        foreach ($tasks as $task) {
            if ($this->evaluateCustomRules($task)) {
                $stats['decisions']++;
            }
        }
        
        return $stats;
    }

    /**
     * Evaluate custom rules for a task
     */
    /**
     * Evaluate custom rules for a task
     */
    protected function evaluateCustomRules(Task $task): bool
    {
        $rules = $this->getActiveRules();
        $decisionCreated = false;
        
        foreach ($rules as $rule) {
            $conditions = $rule['conditions'];
            $met = false;
            
            // Generic Logic Interpreter
            if (isset($conditions['field'])) {
                $field = $conditions['field'];
                $operator = $conditions['operator'] ?? '=';
                $value = $conditions['value'];
                $actualValue = null;

                // 1. Determine Actual Value
                if ($field === 'assigned_users_count') {
                    $actualValue = $task->assignedUsers()->count();
                } elseif ($field === 'days_until_due') {
                    $actualValue = $task->due_date ? now()->diffInDays($task->due_date, false) : 0;
                } elseif ($field === 'days_overdue') {
                    $actualValue = ($task->due_date && $task->due_date < now()) ? now()->diffInDays($task->due_date) : 0;
                } elseif (str_contains($field, '.')) {
                    // Support Dot Notation for Relations (e.g., 'project.status', 'owner.name')
                    // Support Collection Wildcard (e.g., 'assignedUsers.*.avatar')
                    
                    if (str_contains($field, '.*.')) {
                        // Collection Logic: Check if ANY item in collection matches
                        [$relation, $subField] = explode('.*.', $field, 2);
                        
                        // Resolve relation (e.g., assignedUsers)
                        $collection = $task->$relation ?? null;
                        
                        if ($collection instanceof \Illuminate\Database\Eloquent\Collection) {
                            // Check if ANY item matches the condition directly here
                            // We need to bypass the standard switch below for collections
                            $hasMatch = $collection->contains(function ($item) use ($subField, $operator, $value) {
                                $itemValue = $item->getAttribute($subField);
                                
                                // Handle NULL string input
                                if ($value === 'NULL') $value = null;

                                switch ($operator) {
                                    case '=': return $itemValue == $value;
                                    case '!=': return $itemValue != $value;
                                    default: return false; 
                                }
                            });
                            
                            if ($hasMatch) {
                                $met = true;
                                $actualValue = "Match found in collection";
                                goto skip_standard_comparison;
                            }
                        }
                    } else {
                        // Standard Dot Notation (Single Object)
                        $parts = explode('.', $field);
                        $current = $task;
                        $valid = true;
                        
                        foreach ($parts as $part) {
                            if ($current && (is_array($current) || $current instanceof \ArrayAccess)) {
                                $current = $current[$part] ?? null;
                            } elseif ($current && is_object($current)) {
                                $current = $current->$part ?? null;
                            } else {
                                $valid = false;
                                break;
                            }
                        }
                        $actualValue = $valid ? $current : null;
                    }
                } else {
                    // Start by checking simple attributes
                    $actualValue = $task->getAttribute($field);
                }

                // Handle string "NULL" as actual null
                if ($value === 'NULL') $value = null;

                // 2. Compare (Standard Logic)
                switch ($operator) {
                    case '>':
                        $met = $actualValue > $value;
                        break;
                    case '<':
                        $met = $actualValue < $value;
                        break;
                    case '>=':
                        $met = $actualValue >= $value;
                        break;
                    case '<=':
                        $met = $actualValue <= $value;
                        break;
                    case '=':
                    case '==':
                        $met = $actualValue == $value;
                        break;
                    case '!=':
                        $met = $actualValue != $value;
                        break;
                    case 'IN':
                        $met = in_array($actualValue, (array)$value);
                        break;
                }
                
                skip_standard_comparison:
            }
            
            if ($met) {
                try {
                    // Rule Matched! Create Decision
                    $this->decisionEngine->createDecision(
                        'rule_triggered',
                        $task->id,
                        $task->project_id,
                        "Custom Rule: {$rule['name']}",
                        [
                            "Rule '{$rule['name']}' triggered",
                            "Condition: {$field} {$operator} {$value}",
                            "Actual Value: " . (is_array($actualValue) ? json_encode($actualValue) : $actualValue)
                        ],
                        0.95,
                        [$rule['action']]
                    );
                    
                    // Update rule stats
                    \App\Models\AI\AutomationRule::find($rule['id'])
                        ->update(['last_triggered_at' => now()]);
                        
                    $decisionCreated = true;
                } catch (\Exception $e) {
                    Log::error("Failed to execute custom rule {$rule['id']}", ['error' => $e->getMessage()]);
                }
            }
        }
        
        return $decisionCreated;
    }

    /**
     * Smart workload balancing (Dual Mode: Local vs Cloud)
     * 
     * NOTE: Currently using Local Mode only for Workload Balance
     * due to Cloud API timeout issues with large user datasets.
     * Cloud Mode can be enabled for other features (Priority, Assignment, etc.)
     */
    public function balanceWorkload(): array
    {
        // Get configurable threshold
        $threshold = (int) (\App\Models\AI\AISetting::where('key', 'workload_threshold')->value('value') ?? 5);

        // Force Local Mode for stability
        // Cloud mode causes 120s timeouts with 20+ overloaded users
        return $this->balanceWorkloadLocal($threshold);
        
        /* CLOUD MODE DISABLED FOR WORKLOAD BALANCE
        // Check if Cloud Mode
        if ($this->isCloudMode()) {
            return $this->balanceWorkloadCloud($threshold);
        }
        
        // Local Mode: Fast, rule-based analysis
        return $this->balanceWorkloadLocal($threshold);
        */
    }

    /**
     * Local workload balancing (Rule-based)
     */
    protected function balanceWorkloadLocal(int $threshold): array
    {
        $users = \App\Models\User::whereHas('tasks', function ($query) {
            $query->where('status', '!=', 'completed');
        })->get();

        $recommendations = [];

        foreach ($users as $user) {
            $activeTasks = $user->tasks()->where('status', '!=', 'completed')->count();
            
            if ($activeTasks > $threshold) {
                $recommendations[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'active_tasks' => $activeTasks,
                    'recommendation' => "Consider redistributing tasks (Threshold: >{$threshold})",
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Cloud workload balancing (AI-powered)
     */
    protected function balanceWorkloadCloud(int $threshold): array
    {
        // Quick check: If prompt doesn't exist, fallback immediately
        $promptExists = \App\Models\AI\AIPrompt::where('name', 'ai_workload_balance')->exists();
        if (!$promptExists) {
            Log::warning('ai_workload_balance prompt not found, using local fallback');
            return $this->balanceWorkloadLocal($threshold);
        }

        $users = \App\Models\User::with(['tasks' => function($q) {
            $q->where('status', '!=', 'completed');
        }])->whereHas('tasks', function ($query) {
            $query->where('status', '!=', 'completed');
        })->get();

        if ($users->isEmpty()) {
            return [];
        }

        // Build compact data for AI
        $usersData = $users->map(function($user) use ($threshold) {
            $tasks = $user->tasks;
            $overdueTasks = $tasks->filter(fn($t) => $t->due_date && $t->due_date < now());
            $urgentTasks = $tasks->filter(fn($t) => $t->priority === 'high' || $t->priority === 'urgent');
            
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'active_tasks_count' => $tasks->count(),
                'overdue_tasks_count' => $overdueTasks->count(),
                'urgent_tasks_count' => $urgentTasks->count(),
                'avg_task_progress' => round($tasks->avg('progress') ?? 0, 1),
                'threshold' => $threshold,
            ];
        })->toArray();

        // Filter only overloaded users for AI analysis
        $overloadedUsers = array_filter($usersData, fn($u) => $u['active_tasks_count'] > $threshold);

        if (empty($overloadedUsers)) {
            return [];
        }

        // Limit to max 10 users to avoid huge API calls
        if (count($overloadedUsers) > 10) {
            $overloadedUsers = array_slice($overloadedUsers, 0, 10);
            Log::info('Limited workload analysis to 10 most overloaded users');
        }

        // Call AI for smart recommendations with timeout protection
        try {
            Log::info('Calling Cloud AI for workload analysis', [
                'users_count' => count($overloadedUsers)
            ]);

            $response = $this->aiGateway->suggest('ai_workload_balance', [
                'users_json' => json_encode(array_values($overloadedUsers), JSON_PRETTY_PRINT),
                'users_count' => count($overloadedUsers),
                'threshold' => $threshold,
                'analysis_date' => now()->format('Y-m-d H:i:s')
            ]);

            // Parse AI response
            if ($response && isset($response['recommendations'])) {
                Log::info('Cloud workload analysis completed successfully');
                return $response['recommendations'];
            }

            // Fallback to local if AI returns empty
            Log::warning('Cloud workload balance returned empty response, using local fallback');
            return $this->balanceWorkloadLocal($threshold);

        } catch (\Exception $e) {
            Log::error('Cloud workload balance failed', [
                'error' => $e->getMessage(),
                'type' => get_class($e)
            ]);
            
            // Fallback to local mode
            return $this->balanceWorkloadLocal($threshold);
        }
    }

    // ===================================================================
    // BATCH AI AUTOMATION METHODS (Cloud Mode Only)
    // ===================================================================

    /**
     * Check if AI is in Cloud mode (generative)
     */
    protected function isCloudMode(): bool
    {
        $provider = $this->settingsService->get('ai_provider', 'local');
        return $provider !== 'local';
    }

    /**
     * Batch priority adjustments analysis (Cloud AI)
     */
    public function checkPriorityAdjustmentsBatch(int $limit = 10): array
    {
        // Fallback to legacy for Local mode
        if (!$this->isCloudMode()) {
            return ['message' => 'Using local mode - batch not available'];
        }

        $tasks = Task::where('status', 'in_progress')
            ->where(function ($query) {
                $query->where('due_date', '<=', now()->addDays(3))
                    ->orWhereHas('dependencies', function ($q) {
                        $q->where('status', 'completed');
                    });
            })
            // Deduplication: Exclude tasks analyzed in last 24h
            ->whereDoesntHave('aiDecisions', function ($q) {
                $q->where('decision_type', 'priority_adjustment')
                  ->where('created_at', '>=', now()->subDay());
            })
            ->limit($limit)
            ->get();

        if ($tasks->isEmpty()) {
            return ['decisions_created' => 0, 'message' => 'No new tasks to analyze (all up to date)'];
        }

        // Build formatted text instead of JSON (like AI Features)
        $tasksText = '';
        foreach ($tasks as $index => $task) {
            $daysUntilDue = $task->due_date ? now()->diffInDays($task->due_date, false) : 'N/A';
            
            // Handle Enums - convert to string values
            $priorityValue = $task->priority 
                ? (is_object($task->priority) ? $task->priority->value : $task->priority)
                : 'none';
            
            $statusValue = is_object($task->status) ? $task->status->value : $task->status;
            
            $tasksText .= sprintf(
                "Task %d:\n- ID: %d\n- Title: %s\n- Current Priority: %s\n- Status: %s\n- Due: %s (%s days)\n- Dependencies: %d\n\n",
                $index + 1,
                $task->id,
                $task->title,
                $priorityValue,
                $statusValue,
                $task->due_date?->format('Y-m-d') ?? 'Not set',
                $daysUntilDue,
                $task->dependencies()->count()
            );
        }

        // Send as simple variables (like AI Features, not as JSON)
        try {
            Log::info('Calling Priority Batch AI', [
                'tasks_count' => $tasks->count(),
                'mode' => 'formatted_text'
            ]);

            $response = $this->aiGateway->suggest('ai_automation_priority_batch', [
                'tasks_list' => $tasksText,  // ← Formatted text instead of JSON
                'tasks_count' => (string)$tasks->count(),
                'analysis_date' => now()->format('Y-m-d H:i:s')
            ]);

            Log::info('Priority Batch AI Response', [
                'has_response' => !empty($response),
                'has_results' => isset($response['results']),
                'response_keys' => $response ? array_keys($response) : [],
                'response_sample' => json_encode($response, JSON_UNESCAPED_UNICODE)
            ]);

            // Parse and create decisions
            return $this->parseBatchResponse($response, $tasks, 'priority_adjustment');

        } catch (\Exception $e) {
            Log::error('Priority Batch failed', [
                'error' => $e->getMessage(),
                'type' => get_class($e)
            ]);

            return [
                'decisions_created' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Batch assignment suggestions analysis (Cloud AI)
     */
    public function checkAssignmentSuggestionsBatch(int $limit = 10): array
    {
        // Fallback to legacy for Local mode
        if (!$this->isCloudMode()) {
            return ['message' => 'Using local mode - batch not available'];
        }

        // Deduplication: Exclude tasks analyzed in last 24h
        $tasks = Task::doesntHave('assignedUsers')
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereDoesntHave('aiDecisions', function ($q) {
                $q->where('decision_type', 'assignment_suggestion')
                  ->where('created_at', '>=', now()->subDay());
            })
            ->with(['project', 'creator'])
            ->limit($limit)
            ->get();

        if ($tasks->isEmpty()) {
            return ['decisions_created' => 0, 'message' => 'No unassigned tasks found (all up to date)'];
        }

        // Get available users for assignment
        $availableUsers = \App\Models\User::with(['tasks' => function($q) {
            $q->where('status', '!=', 'completed');
        }])->get();

        $usersInfo = $availableUsers->map(fn($u) => "{$u->name} (ID:{$u->id}, Load:{$u->tasks->count()})")->implode("\n");

        // Build formatted text for robust parsing
        $tasksText = "Users:\n{$usersInfo}\n\nTasks:\n";
        foreach ($tasks as $index => $task) {
            $tasksText .= sprintf(
                "Task %d (ID: %d): %s (Priority: %s, Project: %s)\n",
                $index + 1,
                $task->id,
                $task->title,
                $task->priority->value,
                $task->project?->name ?? 'None'
            );
        }

        try {
            Log::info('Calling Assignment Batch AI', ['tasks_count' => $tasks->count()]);

            $response = $this->aiGateway->suggest('ai_automation_assignment_batch', [
                'tasks_list' => $tasksText,
                'tasks_count' => (string)$tasks->count(),
                'analysis_date' => now()->format('Y-m-d H:i:s')
            ]);

            // Use robust parser
            return $this->parseBatchResponse($response, $tasks, 'assignment_suggestion');

        } catch (\Exception $e) {
            Log::error('Assignment Batch failed', [
                'error' => $e->getMessage()
            ]);
            return ['decisions_created' => 0, 'error' => $e->getMessage()];
        }
    }

    /**
     * Batch deadline extensions analysis (Cloud AI)
     */
    public function checkDeadlineExtensionsBatch(int $limit = 10): array
    {
        // Fallback to legacy for Local mode
        if (!$this->isCloudMode()) {
            return ['message' => 'Using local mode - batch not available'];
        }

        // Deduplication: Exclude tasks analyzed in last 24h
        $tasks = Task::where(function ($query) {
                $query->where('due_date', '<', now())
                    ->orWhere('due_date', '<=', now()->addDays(3));
            })
            ->whereIn('status', ['in_progress', 'pending'])
            ->whereDoesntHave('aiDecisions', function ($q) {
                $q->where('decision_type', 'deadline_extension')
                  ->where('created_at', '>=', now()->subDay());
            })
            ->with(['project', 'assignedUsers'])
            ->limit($limit)
            ->get();

        if ($tasks->isEmpty()) {
            return ['decisions_created' => 0, 'message' => 'No tasks near deadline (all up to date)'];
        }

        // Build formatted text for robust parsing
        $tasksText = "";
        foreach ($tasks as $index => $task) {
            $daysOverdue = $task->due_date && $task->due_date < now() 
                ? now()->diffInDays($task->due_date) 
                : 0;
            
            $tasksText .= sprintf(
                "Task %d (ID: %d): %s\n- Status: %s, Priority: %s\n- Due: %s (%d days overdue)\n- Project: %s\n\n",
                $index + 1,
                $task->id,
                $task->title,
                $task->status->value,
                $task->priority->value,
                $task->due_date?->format('Y-m-d') ?? 'N/A',
                $daysOverdue,
                $task->project?->name ?? 'N/A'
            );
        }

        try {
            Log::info('Calling Deadline Batch AI', ['tasks_count' => $tasks->count()]);

            $response = $this->aiGateway->suggest('ai_automation_deadline_batch', [
                'tasks_list' => $tasksText,
                'tasks_count' => (string)$tasks->count(),
                'analysis_date' => now()->format('Y-m-d H:i:s')
            ]);

            // Use robust parser
            return $this->parseBatchResponse($response, $tasks, 'deadline_extension');

        } catch (\Exception $e) {
            Log::error('Deadline Batch failed', [
                'error' => $e->getMessage()
            ]);
            return ['decisions_created' => 0, 'error' => $e->getMessage()];
        }
    }

    /**
     * Batch project health analysis (Cloud AI)
     */
    public function checkProjectHealthBatch(int $limit = 5): array
    {
        // Fallback to legacy for Local mode
        if (!$this->isCloudMode()) {
            return ['message' => 'Using local mode - batch not available'];
        }

        // Deduplication: Exclude projects analyzed (via tasks) in last 24h is tricky
        // Instead check for project_health_check decision on valid projects
        // But decisions are tied to tasks usually. For project health, we might create a general decision
        // OR we just rely on LIMIT for now as Projects update slowly.
        
        $projects = Project::whereIn('status', ['active', 'in_progress'])
            ->with(['tasks', 'teamLead', 'teamMembers'])
            ->limit($limit)
            ->get();

        if ($projects->isEmpty()) {
            return ['decisions_created' => 0, 'message' => 'No active projects found'];
        }

        // Build formatted text for robust parsers
        $projectsText = "";
        foreach ($projects as $index => $project) {
             $overdue = $project->tasks->filter(fn($t) => $t->due_date && $t->due_date < now() && $t->status !== 'completed')->count();
             
             $projectsText .= sprintf(
                "Project %d (ID: %d): %s\n- Status: %s, Progress: %d%%\n- Tasks: %d total, %d overdue\n- Team: %d members\n\n",
                $index + 1,
                $project->id,
                $project->name,
                $project->status,
                $project->progress ?? 0,
                $project->tasks->count(),
                $overdue,
                $project->teamMembers->count()
             );
        }

        try {
            Log::info('Calling Project Health Batch AI', ['projects_count' => $projects->count()]);
            
            $response = $this->aiGateway->suggest('ai_automation_project_health_batch', [
                'projects_list' => $projectsText,
                'projects_count' => (string)$projects->count(),
                'analysis_date' => now()->format('Y-m-d H:i:s')
            ]);
            
            // Re-use parseBatchResponse logic (it adapts to Tasks/Projects if we map them)
            // But we need to pretend these projects are tasks for the parser's regex (ID: X)
            // The parser supports generic "ID:" so it should work if we pass projects collection
            return $this->parseBatchResponse($response, $projects, 'project_health_check');
            
        } catch (\Exception $e) {
            Log::error('Project Health Batch failed', ['error' => $e->getMessage()]);
             return ['decisions_created' => 0, 'error' => $e->getMessage()];
        }
    }



    /**
     * Parse batch AI response and create decisions
     */
    protected function parseBatchResponse(?array $response, $tasks, string $decisionType): array
    {
        if (!$response) {
            Log::warning('Batch AI returned null response', [
                'decision_type' => $decisionType,
                'tasks_count' => $tasks->count()
            ]);
            
            return [
                'decisions_created' => 0,
                'errors' => ['AI returned empty response']
            ];
        }

        $decisionsCreated = 0;
        $errors = [];

        // Try JSON format first (structured response)
        if (isset($response['results']) && is_array($response['results'])) {
            foreach ($response['results'] as $result) {
                try {
                    if (!isset($result['task_id']) || !($result['requires_action'] ?? false)) {
                        continue;
                    }

                    $task = $tasks->firstWhere('id', $result['task_id']);
                    
                    if (!$task) {
                        continue;
                    }

                    $this->decisionEngine->createDecision(
                        $decisionType,
                        $task->id,
                        $task->project_id,
                        $result['recommendation'] ?? 'AI Batch Recommendation',
                        $result['reasoning'] ?? ['Batch analysis'],
                        $result['confidence'] ?? 0.7,
                        []
                    );

                    $decisionsCreated++;

                } catch (\Exception $e) {
                    $errors[] = "Task {$result['task_id']}: {$e->getMessage()}";
                    Log::error('Failed to create batch decision', [
                        'task_id' => $result['task_id'] ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return [
                'decisions_created' => $decisionsCreated,
                'total_analyzed' => count($response['results']),
                'errors' => $errors
            ];
        }

        // 1. Consolidate Response to Text
        $fullText = '';
        if (is_array($response)) {
            // Recursive function to flatten array to string
            array_walk_recursive($response, function($item) use (&$fullText) {
                if (is_string($item) || is_numeric($item)) {
                    $fullText .= $item . "\n";
                }
            });
        } else {
            $fullText = (string)$response;
        }

        // Limit text length to avoid memory issues with huge logs
        $fullText = mb_substr($fullText, 0, 10000);

        // 2. Task-Centric Parsing (Reverse Lookup)
        // Iterate through tasks we sent to check if they are mentioned in the response
        $tasksFound = 0;
        
        foreach ($tasks as $index => $task) {
            $taskId = $task->id;
            $taskSeq = $index + 1; // e.g. Task 1
            
            // Define robust patterns to find this specific task
            $patterns = [
                "/ID:?\s*{$taskId}\b/i",                   // "ID: 625"
                "/Task\s*{$taskId}\b/i",                 // "Task 625"
                "/Task\s*\(?ID:?\s*{$taskId}\)?/i",      // "Task (ID: 625)"
                "/Task\s*{$taskSeq}\b(?![\d])/i"         // "Task 1" (but not "Task 10")
            ];

            $offset = -1;
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $fullText, $matches, PREG_OFFSET_CAPTURE)) {
                    $offset = $matches[0][1];
                    break; 
                }
            }

            if ($offset !== -1) {
                // Task found in text! Now extracting context around it (250 chars)
                $contextArgs = mb_substr($fullText, $offset, 400); // 400 chars ahead
                
                // 3. Action Sentiment Analysis
                // Does this context suggest an action?
                $actionKeywords = [
                    // Priority keywords
                    'escalate', 'increase', 'raise', 'urgent', 'critical', 'high', 'change priority', 'recommend priority', 'change', 'keep',
                    // Assignment keywords
                    'assign', 'assign to', 'reassign', 'allocate', 'delegate',
                    // Deadline keywords  
                    'extend', 'extension', 'deadline', 'postpone', 'delay', 'reschedule',
                    // Project Health keywords
                    'healthy', 'at_risk', 'at risk', 'investigate', 'review', 'action needed'
                ];
                $hasAction = false;
                
                foreach ($actionKeywords as $keyword) {
                    if (stripos($contextArgs, $keyword) !== false) {
                        $hasAction = true;
                        break;
                    }
                }

                if ($hasAction) {
                    try {
                        $recommendation = match($decisionType) {
                            'priority_adjustment' => "AI suggested priority adjustment for Task {$taskId}",
                            'assignment_suggestion' => "AI suggested assignment for Task {$taskId}",
                            'deadline_extension' => "AI recommended deadline extension for Task {$taskId}",
                            'project_health_batch' => "AI project health alert for Project {$taskId}",
                            default => "AI Recommendation for Task {$taskId}"
                        };

                        $this->decisionEngine->createDecision(
                            $decisionType,
                            $task->id,
                            $task->project_id,
                            $recommendation,
                            ["AI analysis detected requirement to {$keyword}"],
                            0.85,
                            ['ai_snippet' => mb_substr($contextArgs, 0, 150) . '...']
                        );
                        $decisionsCreated++;
                        $tasksFound++;
                    } catch (\Exception $e) {
                        $errors[] = "Task {$taskId}: " . $e->getMessage();
                    }
                }
            }
        }

        return [
            'decisions_created' => $decisionsCreated,
            'total_analyzed' => $tasks->count(),
            'tasks_found_in_response' => $tasksFound,
            'errors' => $errors,
            // 'full_response_preview' => mb_substr($fullText, 0, 200) . '...'
        ];

        // No recognizable format
        Log::warning('Batch AI returned unrecognized format', [
            'decision_type' => $decisionType,
            'response_keys' => array_keys($response)
        ]);

        return [
            'decisions_created' => 0,
            'errors' => ['AI response format not recognized']
        ];
    }
}
