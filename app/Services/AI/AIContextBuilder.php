<?php

namespace App\Services\AI;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Support\Facades\Cache;

class AIContextBuilder
{
    protected $dataAggregator;

    public function __construct(AIDataAggregator $dataAggregator)
    {
        $this->dataAggregator = $dataAggregator;
    }

    /**
     * Build comprehensive context for a task
     */
    public function buildTaskContext(int $taskId): array
    {
        $enrichedTask = $this->dataAggregator->getEnrichedTask($taskId);
        
        if (!$enrichedTask) {
            return [];
        }

        $context = [
            // Core Task Info
            'task' => [
                'id' => $enrichedTask['task_id'],
                'title' => $enrichedTask['title'],
                'description' => $enrichedTask['description'],
                'status' => $enrichedTask['status'],
                'priority' => $enrichedTask['priority'],
                'client_name' => $enrichedTask['client_name'],
            ],

            // Temporal Context
            'timeline' => [
                'created_at' => $enrichedTask['created_at'],
                'due_date' => $enrichedTask['due_date'],
                'days_until_due' => $enrichedTask['days_until_due'],
                'days_overdue' => $enrichedTask['days_overdue'],
                'urgency_level' => $enrichedTask['urgency_level'],
            ],

            // Project Context
            'project' => $enrichedTask['project_id'] ? [
                'id' => $enrichedTask['project_id'],
                'title' => $enrichedTask['project_title'],
                'status' => $enrichedTask['project_status'],
                'deadline' => $enrichedTask['project_deadline'],
            ] : null,

            // Activity Metrics
            'engagement' => [
                'comment_count' => $enrichedTask['comment_count'],
                'attachment_count' => $enrichedTask['attachment_count'],
                'time_entry_count' => $enrichedTask['time_entry_count'],
                'total_hours_logged' => $enrichedTask['total_hours_logged'],
                'activity_count_7d' => $enrichedTask['activity_count_7d'],
                'last_activity_at' => $enrichedTask['last_activity_at'],
            ],

            // AI Flags
            'ai_signals' => [
                'needs_attention' => (bool) $enrichedTask['needs_attention'],
                'low_engagement' => (bool) $enrichedTask['low_engagement'],
                'stale_task' => (bool) $enrichedTask['stale_task'],
                'is_blocked' => (bool) $enrichedTask['is_blocked'],
                'is_completed' => (bool) $enrichedTask['is_completed'],
            ],

            // Creator Context
            'creator' => [
                'id' => $enrichedTask['created_by'],
                'name' => $enrichedTask['creator_name'],
                'email' => $enrichedTask['creator_email'],
            ],
        ];

        return $context;
    }

    /**
     * Build comprehensive context for a project
     */
    public function buildProjectContext(int $projectId): array
    {
        $enrichedProject = $this->dataAggregator->getEnrichedProject($projectId);
        
        if (!$enrichedProject) {
            return [];
        }

        $context = [
            // Core Project Info
            'project' => [
                'id' => $enrichedProject['project_id'],
                'title' => $enrichedProject['title'],
                'description' => $enrichedProject['description'],
                'status' => $enrichedProject['status'],
                'priority' => $enrichedProject['priority'],
                'category' => $enrichedProject['category'],
                'privacy' => $enrichedProject['privacy'],
            ],

            // Timeline
            'timeline' => [
                'created_at' => $enrichedProject['created_at'],
                'start_date' => $enrichedProject['start_date'],
                'deadline' => $enrichedProject['deadline'],
            ],

            // Team Context
            'team' => [
                'team_lead_id' => $enrichedProject['team_lead_id'],
                'team_lead_name' => $enrichedProject['team_lead_name'],
                'creator_id' => $enrichedProject['created_by'],
                'creator_name' => $enrichedProject['creator_name'],
            ],

            // Task Statistics
            'tasks' => [
                'total' => $enrichedProject['total_tasks'],
                'completed' => $enrichedProject['completed_tasks'],
                'in_progress' => $enrichedProject['in_progress_tasks'],
                'pending' => $enrichedProject['pending_tasks'],
                'blocked' => $enrichedProject['blocked_tasks'],
                'overdue' => $enrichedProject['overdue_tasks'],
            ],

            // Progress Metrics
            'progress' => [
                'manual_progress' => $enrichedProject['progress'],
                'calculated_progress' => $enrichedProject['calculated_progress'],
                'completion_rate' => $enrichedProject['completion_rate'],
                'total_hours_logged' => $enrichedProject['total_hours_logged'],
            ],

            // Activity Metrics
            'activity' => [
                'activity_count_7d' => $enrichedProject['activity_count_7d'],
                'activity_count_30d' => $enrichedProject['activity_count_30d'],
                'last_activity_at' => $enrichedProject['last_activity_at'],
            ],

            // Health & AI Signals
            'health' => [
                'status' => $enrichedProject['health_status'],
                'is_stale' => (bool) $enrichedProject['is_stale'],
                'has_multiple_blockers' => (bool) $enrichedProject['has_multiple_blockers'],
                'needs_tasks' => (bool) $enrichedProject['needs_tasks'],
            ],
        ];

        return $context;
    }

    /**
     * Build context for AI decision making on a task
     */
    public function buildDecisionContext(int $taskId): array
    {
        $cacheKey = "ai_decision_context_task_{$taskId}";
        
        return Cache::remember($cacheKey, 300, function () use ($taskId) {
            $taskContext = $this->buildTaskContext($taskId);
            
            if (empty($taskContext)) {
                return [];
            }

            // Add related data
            $projectId = $taskContext['project']['id'] ?? null;
            $projectContext = $projectId ? $this->buildProjectContext($projectId) : null;

            return [
                'task_context' => $taskContext,
                'project_context' => $projectContext,
                'system_context' => [
                    'task_statistics' => $this->dataAggregator->getTaskStatistics(),
                    'project_statistics' => $this->dataAggregator->getProjectStatistics(),
                ],
                'recommendations' => $this->generateRecommendations($taskContext, $projectContext),
            ];
        });
    }

    /**
     * Generate AI recommendations based on context
     */
    protected function generateRecommendations(array $taskContext, ?array $projectContext): array
    {
        $recommendations = [];

        // Check for overdue tasks
        if ($taskContext['ai_signals']['needs_attention']) {
            $recommendations[] = [
                'type' => 'priority_escalation',
                'severity' => 'high',
                'message' => 'Task is overdue and may need priority escalation',
                'suggested_action' => 'increase_priority',
            ];
        }

        // Check for stale tasks
        if ($taskContext['ai_signals']['stale_task']) {
            $recommendations[] = [
                'type' => 'engagement_boost',
                'severity' => 'medium',
                'message' => 'Task has no recent activity - may need attention',
                'suggested_action' => 'request_update',
            ];
        }

        // Check for low engagement
        if ($taskContext['ai_signals']['low_engagement']) {
            $recommendations[] = [
                'type' => 'collaboration_needed',
                'severity' => 'low',
                'message' => 'Task has low engagement - consider adding collaborators',
                'suggested_action' => 'add_team_members',
            ];
        }

        // Check for blocked tasks
        if ($taskContext['ai_signals']['is_blocked']) {
            $recommendations[] = [
                'type' => 'blocker_resolution',
                'severity' => 'high',
                'message' => 'Task is blocked - immediate action required',
                'suggested_action' => 'resolve_blocker',
            ];
        }

        return $recommendations;
    }

    /**
     * Build context for project-level AI decisions
     */
    public function buildProjectDecisionContext(int $projectId): array
    {
        $cacheKey = "ai_decision_context_project_{$projectId}";
        
        return Cache::remember($cacheKey, 300, function () use ($projectId) {
            $projectContext = $this->buildProjectContext($projectId);
            
            if (empty($projectContext)) {
                return [];
            }

            return [
                'project_context' => $projectContext,
                'system_context' => [
                    'project_statistics' => $this->dataAggregator->getProjectStatistics(),
                ],
                'recommendations' => $this->generateProjectRecommendations($projectContext),
            ];
        });
    }

    /**
     * Generate project-level recommendations
     */
    protected function generateProjectRecommendations(array $projectContext): array
    {
        $recommendations = [];

        // Check health status
        if ($projectContext['health']['status'] === 'overdue') {
            $recommendations[] = [
                'type' => 'deadline_extension',
                'severity' => 'critical',
                'message' => 'Project is overdue - needs immediate review',
                'suggested_action' => 'review_deadline',
            ];
        }

        if ($projectContext['health']['status'] === 'at_risk') {
            $recommendations[] = [
                'type' => 'risk_mitigation',
                'severity' => 'high',
                'message' => 'Project is at risk - deadline approaching',
                'suggested_action' => 'resource_allocation',
            ];
        }

        // Check for stale project
        if ($projectContext['health']['is_stale']) {
            $recommendations[] = [
                'type' => 'activity_boost',
                'severity' => 'medium',
                'message' => 'Project has low activity - may need attention',
                'suggested_action' => 'schedule_review',
            ];
        }

        // Check for blockers
        if ($projectContext['health']['has_multiple_blockers']) {
            $recommendations[] = [
                'type' => 'blocker_review',
                'severity' => 'high',
                'message' => 'Multiple blocked tasks - requires intervention',
                'suggested_action' => 'unblock_tasks',
            ];
        }

        return $recommendations;
    }
    /**
     * Build compact context for Generative AI to save tokens
     */
    public function buildCompactContext(int $taskId): array
    {
        $taskContext = $this->buildTaskContext($taskId);

        if (empty($taskContext)) {
            return [];
        }

        return [
            'context_type' => 'decision_analysis',
            'task' => [
                'id' => $taskContext['task']['id'],
                'title' => $taskContext['task']['title'],
                'status' => $taskContext['task']['status'],
                'priority' => $taskContext['task']['priority'],
                'time_logged' => $taskContext['engagement']['total_hours_logged'] . 'h',
                'due_date' => $taskContext['timeline']['due_date'] ? \Carbon\Carbon::parse($taskContext['timeline']['due_date'])->format('Y-m-d') . ($taskContext['timeline']['days_overdue'] > 0 ? ' (overdue)' : '') : 'N/A',
            ],
            'signals' => [
                'is_blocked' => $taskContext['ai_signals']['is_blocked'],
                'needs_attention' => $taskContext['ai_signals']['needs_attention'],
                'days_stale' => $taskContext['ai_signals']['stale_task'] ? 7 : 0, // Simplified signal
                'engagement_level' => $taskContext['ai_signals']['low_engagement'] ? 'low' : 'normal',
            ]
        ];
    }
}
