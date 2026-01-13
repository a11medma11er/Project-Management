<?php

namespace App\Services\AI;

use App\Models\AI\AIDecision;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Notifications\NewAIDecisionNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AIDecisionEngine
{
    protected $dataAggregator;
    protected $contextBuilder;
    protected $guardrailService;
    protected $strategyFactory;

    public function __construct(
        AIDataAggregator $dataAggregator,
        AIContextBuilder $contextBuilder,
        AIGuardrailService $guardrailService,
        AIDecisionStrategyFactory $strategyFactory
    ) {
        $this->dataAggregator = $dataAggregator;
        $this->contextBuilder = $contextBuilder;
        $this->guardrailService = $guardrailService;
        $this->strategyFactory = $strategyFactory;
    }

    /**
     * Analyze task and generate decision
     */
    public function analyzeTask(Task $task, ?string $decisionType = null): ?AIDecision
    {
        try {
            $taskId = $task->id;
            
            // Get enriched task context
            $context = $this->contextBuilder->buildDecisionContext($taskId);
            
            if (empty($context)) {
                Log::warning("No context available for task {$taskId}");
                return null;
            }

            // Get appropriate strategy
            $strategy = $this->strategyFactory->getStrategy();

            // Analyze based on strategy
            $analysis = $strategy->analyzeTask($context, $decisionType);

            // Create decision if action needed
            if ($analysis['requires_action']) {
                return $this->createDecision(
                    'task_analysis',
                    $taskId,
                    null,
                    $analysis['recommendation'],
                    $analysis['reasoning'],
                    $analysis['confidence'],
                    $analysis['alternatives']
                );
            }

            return null;

        } catch (\Exception $e) {
            Log::error("Task analysis failed for task {$taskId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Analyze project and generate decision
     */
    public function analyzeProject(int $projectId): ?AIDecision
    {
        try {
            // Get enriched project context
            $context = $this->contextBuilder->buildProjectDecisionContext($projectId);
            
            if (empty($context)) {
                Log::warning("No context available for project {$projectId}");
                return null;
            }

            // Get appropriate strategy
            $strategy = $this->strategyFactory->getStrategy();

            // Analyze based on strategy
            $analysis = $strategy->analyzeProject($context);

            // Create decision if action needed
            if ($analysis['requires_action']) {
                return $this->createDecision(
                    'project_analysis',
                    null,
                    $projectId,
                    $analysis['recommendation'],
                    $analysis['reasoning'],
                    $analysis['confidence'],
                    $analysis['alternatives']
                );
            }

            return null;

        } catch (\Exception $e) {
            Log::error("Project analysis failed for project {$projectId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create AI decision record
     */
    public function createDecision(
        string $type,
        ?int $taskId,
        ?int $projectId,
        string $recommendation,
        array $reasoning,
        float $confidence,
        array $alternatives
    ): AIDecision {
        $decision = AIDecision::create([
            'decision_type' => $type,
            'task_id' => $taskId,
            'project_id' => $projectId,
            'recommendation' => $recommendation,
            'reasoning' => $reasoning,
            'confidence_score' => $confidence,
            'alternatives' => $alternatives,
            'suggested_actions' => [], // Default for legacy
            'ai_response' => [], // Default empty for legacy/local mode
            'user_action' => 'pending',
            'executed_at' => null,
        ]);

        // Notify users with permission to approve AI actions
        try {
            $usersToNotify = User::permission('approve-ai-actions')->get();
            
            if ($usersToNotify->isNotEmpty()) {
                Notification::send($usersToNotify, new NewAIDecisionNotification($decision));
                Log::info("Notified {$usersToNotify->count()} users about decision #{$decision->id}");
            }
        } catch (\Exception $e) {
            // Don't fail decision creation if notification fails
            Log::warning("Failed to send notifications for decision #{$decision->id}: " . $e->getMessage());
        }

        return $decision;
    }

    /**
     * Execute approved decision
     */
    public function executeDecision(AIDecision $decision, ?string $modifiedAction = null): bool
    {
        try {
            $action = $modifiedAction ?? $decision->recommendation;
            
            // Check guardrails before execution
            $guardrailCheck = $this->guardrailService->checkDecision($decision);
            
            if (!$guardrailCheck['passed']) {
                Log::warning("Guardrail violations detected for decision #{$decision->id}", [
                    'violations' => $guardrailCheck['violations'],
                    'severity' => $guardrailCheck['highest_severity'],
                ]);
                
                // Update decision with violation info
                $decision->update([
                    'guardrail_violations' => $guardrailCheck['total_violations'],
                    'guardrail_check' => $guardrailCheck,
                    'execution_result' => [
                        'status' => 'blocked',
                        'reason' => 'Guardrail violations detected',
                        'violations' => $guardrailCheck['violations'],
                        'severity' => $guardrailCheck['highest_severity'],
                        'timestamp' => now()->toIso8601String(),
                    ]
                ]);
                
                // If critical or high severity, block execution
                if (in_array($guardrailCheck['highest_severity'], ['critical', 'high'])) {
                    Log::error("Execution blocked for decision #{$decision->id} due to {$guardrailCheck['highest_severity']} severity violations");
                    
                    // Notify admins about critical violation
                    try {
                        $admins = User::permission('manage-ai-settings')->get();
                        if ($admins->isNotEmpty()) {
                            Notification::send($admins, new \App\Notifications\GuardrailViolationNotification($decision, $guardrailCheck));
                        }
                    } catch (\Exception $e) {
                        Log::warning("Failed to send guardrail violation notification: " . $e->getMessage());
                    }
                    
                    return false;
                }
                
                // Medium severity: log warning but allow execution
                Log::warning("Proceeding with execution despite medium severity violations");
            }
            
            // Log execution attempt
            Log::info("Executing AI decision #{$decision->id}: {$action}");

            // TODO: Implement actual execution logic based on decision type
            // For now, just mark as executed
            
            $decision->update([
                'executed_at' => now(),
                'execution_result' => [
                    'status' => 'simulated',
                    'action_taken' => $action,
                    'guardrail_check' => $guardrailCheck['passed'] ? 'passed' : 'warning',
                    'timestamp' => now()->toIso8601String(),
                ]
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error("Failed to execute decision #{$decision->id}: " . $e->getMessage());
            
            // Fallback: Try safe alternative if available
            $safeFallback = $this->findSafeFallback($decision);
            
            if ($safeFallback) {
                Log::info("Attempting fallback for decision #{$decision->id}: {$safeFallback}");
                
                $decision->update([
                    'execution_result' => [
                        'status' => 'fallback_applied',
                        'error' => $e->getMessage(),
                        'fallback_action' => $safeFallback,
                        'timestamp' => now()->toIso8601String(),
                    ]
                ]);
                
                return true; // Fallback successful
            }
            
            // No fallback available
            $decision->update([
                'execution_result' => [
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                    'fallback_attempted' => false,
                ]
            ]);

            return false;
        }
    }
    
    /**
     * Find safe fallback alternative for failed decision
     */
    protected function findSafeFallback(AIDecision $decision): ?string
    {
        // If decision has alternatives, return the first one with low impact
        if (!empty($decision->alternatives)) {
            foreach ($decision->alternatives as $alternative) {
                if (isset($alternative['impact']) && $alternative['impact'] === 'Low') {
                    return $alternative['action'];
                }
            }
        }
        
        // Default safe fallback: log for manual review
        return 'Mark for manual review';
    }
}
