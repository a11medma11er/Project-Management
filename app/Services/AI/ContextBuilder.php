<?php

namespace App\Services\AI;

use App\Models\Task;
use App\Models\User;
use App\DTO\TaskDecisionContext;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

/**
 * Builds comprehensive context for AI decision making
 */
class ContextBuilder
{
    public function __construct(
        private ActivityService $activityService
    ) {}
    
    /**
     * Build complete task context for AI
     */
    public function buildTaskContext(Task $task): TaskDecisionContext
    {
        try {
            return new TaskDecisionContext(
                task: $task,
                
                // Historical data from activity logs
                similarTasks: $this->getSimilarTasks($task),
                userHistory: $this->getUserHistory($task->created_by),
                projectHistory: $this->getProjectHistory($task->project_id),
                
                // Current user state
                userActiveTasksCount: $this->getUserActiveTasksCount($task->created_by),
                userOverdueTasksCount: $this->getUserOverdueTasksCount($task->created_by),
                userCompletionRate: $this->getUserCompletionRate($task->created_by),
                
                // Team context
                teamWorkload: $this->getTeamWorkload($task->project_id),
                teamSkills: $this->getTeamSkills($task->project_id),
                
                // Time analysis
                businessDaysUntilDue: $this->calculateBusinessDays($task->due_date),
                isUrgent: $task->priority->isUrgent() || $task->isDueSoon(2),
                
                // Additional metadata
                metadata: [
                    'context_generated_at' => now()->toIso8601String(),
                    'has_similar_tasks' => false,
                    'confidence_level' => 'medium',
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Context build failed', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * Get similar tasks based on priority and project
     */
    private function getSimilarTasks(Task $task): array
    {
        $cacheKey = "similar_tasks:{$task->id}";
        $cacheDuration = config('ai.context.cache_duration', 3600);
        $maxTasks = config('ai.context.max_similar_tasks', 10);
        
        return Cache::remember($cacheKey, $cacheDuration, function() use ($task, $maxTasks) {
            return Task::where('project_id', $task->project_id)
                ->where('priority', $task->priority->value)
                ->where('id', '!=', $task->id)
                ->whereIn('status', ['completed'])
                ->limit($maxTasks)
                ->get()
                ->map(fn($t) => [
                    'id' => $t->id,
                    'title' => $t->title,
                    'duration_days' => $t->created_at->diffInDays($t->updated_at),
                    'time_logged' => 0, // TODO: Add timeEntries relationship to Task model
                    'priority' => $t->priority->value,
                ])
                ->toArray();
        });
    }
    
    /**
     * Get user history and patterns
     */
    private function getUserHistory(int $userId): array
    {
        return Cache::remember("user_history:{$userId}", config('ai.context.cache_duration', 3600), function() use ($userId) {
            $user = User::find($userId);
            
            if (!$user) {
                return [];
            }
            
            $totalTasks = Task::where('created_by', $userId)->count();
            $completedTasks = Task::where('created_by', $userId)->where('status', 'completed')->count();
            
            return [
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'avg_completion_days' => $this->calculateAvgCompletionDays($userId),
                'preferred_statuses' => $this->getPreferredStatuses($userId),
                'activity_patterns' => $this->activityService->getUserPatterns($userId),
            ];
        });
    }
    
    /**
     * Get project history and statistics
     */
    private function getProjectHistory(?int $projectId): array
    {
        if (!$projectId) {
            return [];
        }
        
        return Cache::remember("project_history:{$projectId}", config('ai.context.cache_duration', 3600), function() use ($projectId) {
            $totalTasks = Task::where('project_id', $projectId)->count();
            $completedTasks = Task::where('project_id', $projectId)
                ->where('status', 'completed')
                ->count();
                
            return [
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'completion_rate' => $totalTasks > 0 
                    ? round(($completedTasks / $totalTasks) * 100, 2) 
                    : 0,
                'avg_task_duration' => $this->calculateProjectAvgDuration($projectId),
            ];
        });
    }
    
    /**
     * Get active tasks count for user
     */
    private function getUserActiveTasksCount(int $userId): int
    {
        return Task::where('created_by', $userId)
            ->whereIn('status', ['new', 'pending', 'in_progress'])
            ->count();
    }
    
    /**
     * Get overdue tasks count for user
     */
    private function getUserOverdueTasksCount(int $userId): int
    {
        return Task::where('created_by', $userId)
            ->where('due_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();
    }
    
    /**
     * Calculate user completion rate
     */
    private function getUserCompletionRate(int $userId): float
    {
        $total = Task::where('created_by', $userId)->count();
        
        if ($total === 0) {
            return 0;
        }
        
        $completed = Task::where('created_by', $userId)
            ->where('status', 'completed')
            ->count();
            
        return round(($completed / $total) * 100, 2);
    }
    
    /**
     * Get team workload distribution
     */
    private function getTeamWorkload(?int $projectId): array
    {
        if (!$projectId) {
            return [];
        }
        
        // Get all users assigned to tasks in this project
        return Task::where('project_id', $projectId)
            ->whereIn('status', ['new', 'pending', 'in_progress'])
            ->selectRaw('created_by, COUNT(*) as task_count')
            ->groupBy('created_by')
            ->pluck('task_count', 'created_by')
            ->toArray();
    }
    
    /**
     * Get team skills (placeholder for future implementation)
     */
    private function getTeamSkills(?int $projectId): array
    {
        // This would be extracted from user profiles or task history
        // Placeholder for now
        return [];
    }
    
    /**
     * Calculate business days until due date
     */
    private function calculateBusinessDays($dueDate): int
    {
        if (!$dueDate) {
            return 999; // No due date
        }
        
        $now = Carbon::now();
        $due = Carbon::parse($dueDate);
        
        if ($due->isPast()) {
            return 0;
        }
        
        $businessDays = 0;
        $current = $now->copy();
        
        while ($current->lt($due)) {
            if ($current->isWeekday()) {
                $businessDays++;
            }
            $current->addDay();
        }
        
        return $businessDays;
    }
    
    /**
     * Calculate average completion days for user
     */
    private function calculateAvgCompletionDays(int $userId): float
    {
        $tasks = Task::where('created_by', $userId)
            ->where('status', 'completed')
            ->get();
            
        if ($tasks->isEmpty()) {
            return 0;
        }
        
        $totalDays = $tasks->sum(fn($task) => 
            $task->created_at->diffInDays($task->updated_at)
        );
        
        return round($totalDays / $tasks->count(), 1);
    }
    
    /**
     * Get preferred statuses for user
     */
    private function getPreferredStatuses(int $userId): array
    {
        return Task::where('created_by', $userId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->orderByDesc('count')
            ->limit(3)
            ->pluck('count', 'status')
            ->toArray();
    }
    
    /**
     * Calculate average task duration for project
     */
    private function calculateProjectAvgDuration(int $projectId): float
    {
        $tasks = Task::where('project_id', $projectId)
            ->where('status', 'completed')
            ->get();
            
        if ($tasks->isEmpty()) {
            return 0;
        }
        
        $totalDays = $tasks->sum(fn($task) => 
            $task->created_at->diffInDays($task->updated_at)
        );
        
        return round($totalDays / $tasks->count(), 1);
    }
}
