<?php

namespace App\DTO;

use App\Models\Task;

/**
 * Data Transfer Object for Task Decision Context
 * Contains all information needed for AI decision making
 */
class TaskDecisionContext
{
    public function __construct(
        // Core Task Data
        public readonly Task $task,
        
        // Historical Data
        public readonly array $similarTasks,
        public readonly array $userHistory,
        public readonly array $projectHistory,
        
        // Current State
        public readonly int $userActiveTasksCount,
        public readonly int $userOverdueTasksCount,
        public readonly float $userCompletionRate,
        
        // Team Context
        public readonly array $teamWorkload,
        public readonly array $teamSkills,
        
        // Time Context
        public readonly int $businessDaysUntilDue,
        public readonly bool $isUrgent,
        
        // Metadata
        public readonly array $metadata = []
    ) {}
    
    /**
     * Convert context to array for AI consumption
     */
    public function toArray(): array
    {
        return [
            'task' => [
                'id' => $this->task->id,
                'title' => $this->task->title,
                'description' => $this->task->description,
                'priority' => $this->task->priority->value,
                'priority_label' => $this->task->priority->label(),
                'status' => $this->task->status->value,
                'status_label' => $this->task->status->label(),
                'due_date' => $this->task->due_date?->format('Y-m-d'),
                'is_overdue' => $this->task->isOverdue(),
                'days_overdue' => $this->task->getDaysOverdue(),
                'urgency_level' => $this->task->getUrgencyLevel(),
            ],
            'historical' => [
                'similar_tasks_count' => count($this->similarTasks),
                'similar_tasks_avg_duration' => $this->calculateAvgDuration(),
                'user_total_tasks' => $this->userHistory['total_tasks'] ?? 0,
                'user_completed_tasks' => $this->userHistory['completed_tasks'] ?? 0,
                'user_avg_completion_days' => $this->userHistory['avg_completion_days'] ?? 0,
            ],
            'user' => [
                'active_tasks' => $this->userActiveTasksCount,
                'overdue_tasks' => $this->userOverdueTasksCount,
                'completion_rate' => $this->userCompletionRate,
            ],
            'team' => [
                'total_workload' => array_sum($this->teamWorkload),
                'available_members' => $this->getAvailableMembers(),
                'average_workload' => count($this->teamWorkload) > 0 
                    ? round(array_sum($this->teamWorkload) / count($this->teamWorkload), 2)
                    : 0,
            ],
            'time' => [
                'days_until_due' => $this->businessDaysUntilDue,
                'is_urgent' => $this->isUrgent,
            ],
            'metadata' => $this->metadata,
        ];
    }
    
    /**
     * Convert to JSON
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }
    
    /**
     * Calculate average duration from similar tasks
     */
    private function calculateAvgDuration(): float
    {
        if (empty($this->similarTasks)) {
            return 0;
        }
        
        $durations = array_map(
            fn($task) => $task['duration_days'] ?? 0,
            $this->similarTasks
        );
        
        return round(array_sum($durations) / count($durations), 1);
    }
    
    /**
     * Get count of available team members (workload < 10)
     */
    private function getAvailableMembers(): int
    {
        return count(array_filter(
            $this->teamWorkload,
            fn($load) => $load < 10
        ));
    }
    
    /**
     * Check if context is ready for AI
     */
    public function isReady(): bool
    {
        return !empty($this->task->id) && 
               !empty($this->task->title) &&
               $this->task->priority !== null &&
               $this->task->status !== null;
    }
    
    /**
     * Get context summary
     */
    public function getSummary(): string
    {
        return sprintf(
            "Task #%d (%s) - Priority: %s, Status: %s, Overdue: %s, Team Size: %d",
            $this->task->id,
            $this->task->title,
            $this->task->priority->label(),
            $this->task->status->label(),
            $this->task->isOverdue() ? 'Yes' : 'No',
            count($this->teamWorkload)
        );
    }
}
