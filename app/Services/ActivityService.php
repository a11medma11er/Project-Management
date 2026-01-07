<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class ActivityService
{
    /**
     * Get user behavior patterns for AI training
     */
    public function getUserPatterns(int $userId): array
    {
        $cacheDuration = config('ai.context.cache_duration', 3600);
        $limit = config('ai.context.activity_limit', 100);
        
        return Cache::remember("user_patterns:{$userId}", $cacheDuration, function() use ($userId, $limit) {
            try {
                $activities = Activity::causedBy($userId)
                    ->where('log_name', 'tasks')
                    ->latest()
                    ->limit($limit)
                    ->get();
                    
                return [
                    'total_actions' => $activities->count(),
                    'status_changes' => $this->analyzeStatusChanges($activities),
                    'avg_response_time' => $this->calculateAvgResponseTime($activities),
                    'preferred_priorities' => $this->getPreferredPriorities($activities),
                    'work_hours' => $this->getWorkHours($activities),
                ];
            } catch (\Exception $e) {
                \Log::warning('getUserPatterns failed', ['error' => $e->getMessage()]);
                return [
                    'total_actions' => 0,
                    'status_changes' => [],
                    'avg_response_time' => 0,
                    'preferred_priorities' => [],
                    'work_hours' => [],
                ];
            }
        });
    }
    
    /**
     * Get similar tasks based on activity patterns
     */
    public function getSimilarTaskActivities(Task $task): array
    {
        return Activity::where('subject_type', Task::class)
            ->whereJsonContains('properties->context->project_id', $task->project_id)
            ->latest()
            ->limit(50)
            ->get()
            ->toArray();
    }
    
    /**
     * Track AI suggestion outcomes
     */
    public function trackAISuggestion(
        Task $task, 
        string $suggestionType, 
        mixed $suggestion, 
        bool $accepted
    ): void {
        activity('ai')
            ->performedOn($task)
            ->causedBy(auth()->user())
            ->withProperties([
                'type' => $suggestionType,
                'suggestion' => $suggestion,
                'accepted' => $accepted,
                'task_context' => [
                    'status' => $task->status->value,
                    'priority' => $task->priority->value,
                    'overdue' => $task->isOverdue(),
                ],
            ])
            ->log('ai_suggestion_' . ($accepted ? 'accepted' : 'rejected'));
    }
    
    /**
     * Get AI learning data
     */
    public function getAITrainingData(int $limit = 10000): array
    {
        $acceptedSuggestions = Activity::where('log_name', 'ai')
            ->where('description', 'ai_suggestion_accepted')
            ->latest()
            ->limit($limit)
            ->get();
            
        $rejectedSuggestions = Activity::where('log_name', 'ai')
            ->where('description', 'ai_suggestion_rejected')
            ->latest()
            ->limit($limit)
            ->get();
            
        return [
            'accepted' => $acceptedSuggestions->toArray(),
            'rejected' => $rejectedSuggestions->toArray(),
            'acceptance_rate' => $this->calculateAcceptanceRate($acceptedSuggestions, $rejectedSuggestions),
        ];
    }
    
    /**
     * Get recent activities
     */
    public function getRecentActivities(string $logName = 'tasks', int $limit = 100): Collection
    {
        return Activity::where('log_name', $logName)
            ->with(['causer', 'subject'])
            ->latest()
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get activities for a specific task
     */
    public function getTaskActivities(Task $task): Collection
    {
        return Activity::forSubject($task)
            ->with('causer')
            ->latest()
            ->get();
    }
    
    private function analyzeStatusChanges(Collection $activities): array
    {
        return $activities
            ->where('description', 'updated')
            ->whereNotNull('properties.attributes.status')
            ->groupBy('properties.attributes.status')
            ->map->count()
            ->toArray();
    }
    
    private function calculateAvgResponseTime(Collection $activities): float
    {
        $times = $activities
            ->where('description', 'status_changed')
            ->pluck('properties.transition_time')
            ->filter();
            
        return $times->isEmpty() ? 0 : $times->avg();
    }
    
    private function getPreferredPriorities(Collection $activities): array
    {
        return $activities
            ->where('description', 'created')
            ->pluck('properties.attributes.priority')
            ->countBy()
            ->sortDesc()
            ->take(3)
            ->toArray();
    }
    
    private function getWorkHours(Collection $activities): array
    {
        return $activities
            ->groupBy(fn($activity) => $activity->created_at->hour)
            ->map->count()
            ->sortDesc()
            ->take(5)
            ->toArray();
    }
    
    private function calculateAcceptanceRate($accepted, $rejected): float
    {
        $total = $accepted->count() + $rejected->count();
        return $total > 0 ? round(($accepted->count() / $total) * 100, 2) : 0;
    }
}
