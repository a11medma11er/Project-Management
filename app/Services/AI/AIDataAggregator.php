<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class AIDataAggregator
{
    /**
     * Get enriched task data from AI view
     */
    public function getEnrichedTask(int $taskId): ?array
    {
        $cacheKey = "ai_enriched_task_{$taskId}";
        
        return Cache::remember($cacheKey, 600, function () use ($taskId) {
            $result = DB::table('ai_enriched_tasks')
                ->where('task_id', $taskId)
                ->first();
            
            return $result ? (array) $result : null;
        });
    }

    /**
     * Get enriched project data from AI view
     */
    public function getEnrichedProject(int $projectId): ?array
    {
        $cacheKey = "ai_enriched_project_{$projectId}";
        
        return Cache::remember($cacheKey, 600, function () use ($projectId) {
            $result = DB::table('ai_project_metrics')
                ->where('project_id', $projectId)
                ->first();
            
            return $result ? (array) $result : null;
        });
    }

    /**
     * Get all tasks that need AI attention
     */
    public function getTasksNeedingAttention(int $limit = 10): Collection
    {
        return DB::table('ai_enriched_tasks')
            ->where('needs_attention', true)
            ->orderBy('days_overdue', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get stale tasks (no activity for 7 days)
     */
    public function getStaleTasks(int $limit = 10): Collection
    {
        return DB::table('ai_enriched_tasks')
            ->where('stale_task', true)
            ->orderBy('activity_count_7d', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get low engagement tasks (no comments and created >3 days ago)
     */
    public function getLowEngagementTasks(int $limit = 10): Collection
    {
        return DB::table('ai_enriched_tasks')
            ->where('low_engagement', true)
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get high urgency tasks
     */
    public function getHighUrgencyTasks(int $limit = 10): Collection
    {
        return DB::table('ai_enriched_tasks')
            ->whereIn('urgency_level', ['critical', 'high'])
            ->where('is_completed', false)
            ->orderByRaw("FIELD(urgency_level, 'critical', 'high')")
            ->orderBy('days_overdue', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get projects at risk
     */
    public function getProjectsAtRisk(int $limit = 10): Collection
    {
        return DB::table('ai_project_metrics')
            ->whereIn('health_status', ['overdue', 'at_risk', 'has_blockers'])
            ->orderByRaw("FIELD(health_status, 'overdue', 'at_risk', 'has_blockers')")
            ->limit($limit)
            ->get();
    }

    /**
     * Get stale projects
     */
    public function getStaleProjects(int $limit = 10): Collection
    {
        return DB::table('ai_project_metrics')
            ->where('is_stale', true)
            ->orderBy('activity_count_7d', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get task statistics for AI analysis
     */
    public function getTaskStatistics(): array
    {
        return Cache::remember('ai_task_statistics', 300, function () {
            return [
                'total_tasks' => DB::table('ai_enriched_tasks')->count(),
                'completed_tasks' => DB::table('ai_enriched_tasks')->where('is_completed', true)->count(),
                'overdue_tasks' => DB::table('ai_enriched_tasks')->where('needs_attention', true)->count(),
                'stale_tasks' => DB::table('ai_enriched_tasks')->where('stale_task', true)->count(),
                'low_engagement' => DB::table('ai_enriched_tasks')->where('low_engagement', true)->count(),
                'critical_tasks' => DB::table('ai_enriched_tasks')->where('urgency_level', 'critical')->count(),
                'high_urgency' => DB::table('ai_enriched_tasks')->where('urgency_level', 'high')->count(),
                'avg_comments' => DB::table('ai_enriched_tasks')->avg('comment_count'),
                'avg_hours' => DB::table('ai_enriched_tasks')->avg('total_hours_logged'),
            ];
        });
    }

    /**
     * Get project statistics for AI analysis
     */
    public function getProjectStatistics(): array
    {
        return Cache::remember('ai_project_statistics', 300, function () {
            return [
                'total_projects' => DB::table('ai_project_metrics')->count(),
                'overdue_projects' => DB::table('ai_project_metrics')->where('health_status', 'overdue')->count(),
                'at_risk_projects' => DB::table('ai_project_metrics')->where('health_status', 'at_risk')->count(),
                'stale_projects' => DB::table('ai_project_metrics')->where('is_stale', true)->count(),
                'projects_with_blockers' => DB::table('ai_project_metrics')->where('has_multiple_blockers', true)->count(),
                'avg_completion_rate' => DB::table('ai_project_metrics')->avg('completion_rate'),
                'avg_total_tasks' => DB::table('ai_project_metrics')->avg('total_tasks'),
                'avg_hours' => DB::table('ai_project_metrics')->avg('total_hours_logged'),
            ];
        });
    }

    /**
     * Get comprehensive dashboard data
     */
    public function getDashboardData(): array
    {
        return [
            'tasks' => [
                'statistics' => $this->getTaskStatistics(),
                'needs_attention' => $this->getTasksNeedingAttention(5),
                'stale' => $this->getStaleTasks(5),
                'high_urgency' => $this->getHighUrgencyTasks(5),
            ],
            'projects' => [
                'statistics' => $this->getProjectStatistics(),
                'at_risk' => $this->getProjectsAtRisk(5),
                'stale' => $this->getStaleProjects(5),
            ],
        ];
    }

    /**
     * Clear all AI data caches
     */
    public function clearCaches(): void
    {
        Cache::forget('ai_task_statistics');
        Cache::forget('ai_project_statistics');
        
        // Clear specific task/project caches (note: this is a simple approach)
        // In production, consider using cache tags for better management
    }
}
