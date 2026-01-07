<?php

namespace App\Services\AI;

use App\Models\AI\AIDecision;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AIAnalysisService
{
    protected $dataAggregator;

    public function __construct(AIDataAggregator $dataAggregator)
    {
        $this->dataAggregator = $dataAggregator;
    }

    /**
     * Get comprehensive system insights
     */
    public function getSystemInsights(): array
    {
        return Cache::remember('ai_system_insights', 600, function () {
            $taskStats = $this->dataAggregator->getTaskStatistics();
            $projectStats = $this->dataAggregator->getProjectStatistics();

            return [
                'summary' => [
                    'total_tasks' => $taskStats['total_tasks'],
                    'total_projects' => $projectStats['total_projects'],
                    'tasks_needing_attention' => $taskStats['overdue_tasks'],
                    'projects_at_risk' => $projectStats['at_risk_projects'],
                ],
                'health_score' => $this->calculateSystemHealthScore($taskStats, $projectStats),
                'trends' => $this->analyzeTrends(),
                'recommendations_summary' => $this->getRecommendationsSummary(),
            ];
        });
    }

    /**
     * Calculate overall system health score (0-100)
     */
    protected function calculateSystemHealthScore(array $taskStats, array $projectStats): float
    {
        $score = 100;

        // Deduct for overdue tasks (max -30 points)
        if ($taskStats['total_tasks'] > 0) {
            $overdueRatio = $taskStats['overdue_tasks'] / $taskStats['total_tasks'];
            $score -= min(30, $overdueRatio * 100);
        }

        // Deduct for stale tasks (max -20 points)
        if ($taskStats['total_tasks'] > 0) {
            $staleRatio = $taskStats['stale_tasks'] / $taskStats['total_tasks'];
            $score -= min(20, $staleRatio * 60);
        }

        // Deduct for at-risk projects (max -25 points)
        if ($projectStats['total_projects'] > 0) {
            $riskRatio = $projectStats['at_risk_projects'] / $projectStats['total_projects'];
            $score -= min(25, $riskRatio * 100);
        }

        // Deduct for low engagement (max -15 points)
        if ($taskStats['total_tasks'] > 0) {
            $lowEngagementRatio = $taskStats['low_engagement'] / $taskStats['total_tasks'];
            $score -= min(15, $lowEngagementRatio * 50);
        }

        // Deduct for critical tasks (max -10 points)
        if ($taskStats['total_tasks'] > 0) {
            $criticalRatio = $taskStats['critical_tasks'] / $taskStats['total_tasks'];
            $score -= min(10, $criticalRatio * 40);
        }

        return max(0, round($score, 2));
    }

    /**
     * Analyze trends over time
     */
    protected function analyzeTrends(): array
    {
        // Get decision trends for last 30 days
        $decisionTrends = AIDecision::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN user_action = "accepted" THEN 1 ELSE 0 END) as accepted'),
            DB::raw('AVG(confidence_score) as avg_confidence')
        )
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->limit(7)
        ->get();

        return [
            'last_7_days' => $decisionTrends,
            'acceptance_trend' => $this->calculateAcceptanceTrend($decisionTrends),
            'confidence_trend' => $this->calculateConfidenceTrend($decisionTrends),
        ];
    }

    /**
     * Calculate acceptance trend (increasing/decreasing/stable)
     */
    protected function calculateAcceptanceTrend($decisions): string
    {
        if ($decisions->count() < 2) {
            return 'insufficient_data';
        }

        $recent = $decisions->take(3);
        $older = $decisions->skip(3)->take(3);

        if ($recent->isEmpty() || $older->isEmpty()) {
            return 'insufficient_data';
        }

        $recentRate = $recent->where('total', '>', 0)->avg(function ($item) {
            return $item->total > 0 ? ($item->accepted / $item->total) * 100 : 0;
        });

        $olderRate = $older->where('total', '>', 0)->avg(function ($item) {
            return $item->total > 0 ? ($item->accepted / $item->total) * 100 : 0;
        });

        if ($recentRate > $olderRate + 5) {
            return 'increasing';
        } elseif ($recentRate < $olderRate - 5) {
            return 'decreasing';
        }

        return 'stable';
    }

    /**
     * Calculate confidence trend
     */
    protected function calculateConfidenceTrend($decisions): string
    {
        if ($decisions->count() < 2) {
            return 'insufficient_data';
        }

        $recent = $decisions->take(3)->avg('avg_confidence');
        $older = $decisions->skip(3)->take(3)->avg('avg_confidence');

        if ($recent === null || $older === null) {
            return 'insufficient_data';
        }

        if ($recent > $older + 0.05) {
            return 'improving';
        } elseif ($recent < $older - 0.05) {
            return 'declining';
        }

        return 'stable';
    }

    /**
     * Get recommendations summary
     */
    protected function getRecommendationsSummary(): array
    {
        $pending = AIDecision::where('user_action', 'pending')->count();
        $accepted = AIDecision::where('user_action', 'accepted')->count();
        $rejected = AIDecision::where('user_action', 'rejected')->count();
        $total = $pending + $accepted + $rejected;

        return [
            'total_generated' => $total,
            'pending_review' => $pending,
            'accepted' => $accepted,
            'rejected' => $rejected,
            'acceptance_rate' => $total > 0 ? round(($accepted / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get task priority recommendations
     */
    public function getTaskPriorityRecommendations(int $limit = 10): array
    {
        $highUrgencyTasks = $this->dataAggregator->getHighUrgencyTasks($limit);

        return $highUrgencyTasks->map(function ($task) {
            return [
                'task_id' => $task->task_id,
                'title' => $task->title,
                'current_priority' => $task->priority,
                'urgency_level' => $task->urgency_level,
                'days_overdue' => $task->days_overdue,
                'recommended_priority' => $this->calculateRecommendedPriority($task),
                'reasoning' => $this->getPriorityReasoning($task),
            ];
        })->toArray();
    }

    /**
     * Calculate recommended priority based on urgency
     */
    protected function calculateRecommendedPriority($task): string
    {
        if ($task->urgency_level === 'critical') {
            return 'urgent';
        }

        if ($task->urgency_level === 'high' || $task->days_overdue > 3) {
            return 'high';
        }

        if ($task->urgency_level === 'medium') {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Get priority reasoning
     */
    protected function getPriorityReasoning($task): string
    {
        if ($task->days_overdue > 0) {
            return "Task is {$task->days_overdue} days overdue - requires immediate attention";
        }

        if ($task->days_until_due <= 2) {
            return "Deadline in {$task->days_until_due} days - high urgency";
        }

        if ($task->urgency_level === 'high') {
            return "High urgency based on deadline proximity";
        }

        return "Standard priority based on current status";
    }

    /**
     * Get engagement analysis
     */
    public function getEngagementAnalysis(): array
    {
        $lowEngagementTasks = $this->dataAggregator->getLowEngagementTasks(20);

        return [
            'total_low_engagement' => $lowEngagementTasks->count(),
            'average_age_days' => $lowEngagementTasks->avg(function ($task) {
                return now()->diffInDays($task->created_at);
            }),
            'recommendations' => [
                'add_collaborators' => $lowEngagementTasks->where('comment_count', 0)->count(),
                'request_updates' => $lowEngagementTasks->where('activity_count_7d', 0)->count(),
                'clarify_requirements' => $lowEngagementTasks->where('low_engagement', true)->count(),
            ],
        ];
    }

    /**
     * Get risk assessment for projects
     */
    public function getProjectRiskAssessment(): array
    {
        $atRiskProjects = $this->dataAggregator->getProjectsAtRisk(10);

        return $atRiskProjects->map(function ($project) {
            $riskScore = $this->calculateProjectRiskScore($project);

            return [
                'project_id' => $project->project_id,
                'title' => $project->title,
                'health_status' => $project->health_status,
                'risk_score' => $riskScore,
                'risk_level' => $this->getRiskLevel($riskScore),
                'risk_factors' => $this->identifyRiskFactors($project),
                'mitigation_suggestions' => $this->getMitigationSuggestions($project),
            ];
        })->toArray();
    }

    /**
     * Calculate project risk score (0-100)
     */
    protected function calculateProjectRiskScore($project): float
    {
        $score = 0;

        // Health status impact
        if ($project->health_status === 'overdue') {
            $score += 40;
        } elseif ($project->health_status === 'at_risk') {
            $score += 30;
        } elseif ($project->health_status === 'has_blockers') {
            $score += 25;
        }

        // Blocked tasks impact
        if ($project->blocked_tasks > 0) {
            $score += min(20, $project->blocked_tasks * 5);
        }

        // Overdue tasks impact
        if ($project->overdue_tasks > 0) {
            $score += min(20, $project->overdue_tasks * 3);
        }

        // Low completion rate
        if ($project->completion_rate < 50) {
            $score += 15;
        }

        // Stale project
        if ($project->is_stale) {
            $score += 10;
        }

        return min(100, round($score, 2));
    }

    /**
     * Get risk level classification
     */
    protected function getRiskLevel(float $score): string
    {
        if ($score >= 70) {
            return 'critical';
        } elseif ($score >= 50) {
            return 'high';
        } elseif ($score >= 30) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Identify risk factors
     */
    protected function identifyRiskFactors($project): array
    {
        $factors = [];

        if ($project->overdue_tasks > 0) {
            $factors[] = "{$project->overdue_tasks} overdue tasks";
        }

        if ($project->blocked_tasks > 0) {
            $factors[] = "{$project->blocked_tasks} blocked tasks";
        }

        if ($project->completion_rate < 50) {
            $factors[] = "Low completion rate ({$project->completion_rate}%)";
        }

        if ($project->is_stale) {
            $factors[] = "No recent activity";
        }

        if ($project->has_multiple_blockers) {
            $factors[] = "Multiple blocking issues";
        }

        return $factors;
    }

    /**
     * Get mitigation suggestions
     */
    protected function getMitigationSuggestions($project): array
    {
        $suggestions = [];

        if ($project->overdue_tasks > 0) {
            $suggestions[] = "Review and reschedule overdue tasks";
        }

        if ($project->blocked_tasks > 0) {
            $suggestions[] = "Organize blocker resolution session";
        }

        if ($project->completion_rate < 30) {
            $suggestions[] = "Consider scope reduction or deadline extension";
        }

        if ($project->is_stale) {
            $suggestions[] = "Schedule status review meeting";
        }

        return $suggestions;
    }

    /**
     * Get performance metrics
     */
    public function getPerformanceMetrics(): array
    {
        return [
            'decision_metrics' => [
                'total_decisions' => AIDecision::count(),
                'avg_confidence' => AIDecision::avg('confidence_score'),
                'avg_response_time' => $this->getAverageResponseTime(),
            ],
            'accuracy_metrics' => [
                'acceptance_rate' => $this->calculateAcceptanceRate(),
                'execution_success_rate' => $this->calculateExecutionSuccessRate(),
            ],
            'impact_metrics' => [
                'tasks_improved' => $this->getTasksImprovedCount(),
                'projects_helped' => $this->getProjectsHelpedCount(),
            ],
        ];
    }

    /**
     * Get average response time (in hours)
     */
    protected function getAverageResponseTime(): float
    {
        $decisions = AIDecision::whereNotNull('executed_at')
            ->get(['created_at', 'executed_at']);

        if ($decisions->isEmpty()) {
            return 0;
        }

        $totalHours = $decisions->sum(function ($decision) {
            return $decision->created_at->diffInHours($decision->executed_at);
        });

        return round($totalHours / $decisions->count(), 2);
    }

    /**
     * Calculate acceptance rate
     */
    protected function calculateAcceptanceRate(): float
    {
        $total = AIDecision::count();
        $accepted = AIDecision::where('user_action', 'accepted')->count();

        return $total > 0 ? round(($accepted / $total) * 100, 2) : 0;
    }

    /**
     * Calculate execution success rate
     */
    protected function calculateExecutionSuccessRate(): float
    {
        $executed = AIDecision::whereNotNull('executed_at')->count();
        
        $successful = AIDecision::whereNotNull('executed_at')
            ->whereRaw("JSON_EXTRACT(execution_result, '$.status') != 'failed'")
            ->count();

        return $executed > 0 ? round(($successful / $executed) * 100, 2) : 0;
    }

    /**
     * Get tasks improved count (last 30 days)
     */
    protected function getTasksImprovedCount(): int
    {
        return AIDecision::where('decision_type', 'task_analysis')
            ->where('user_action', 'accepted')
            ->where('created_at', '>=', now()->subDays(30))
            ->distinct('task_id')
            ->count('task_id');
    }

    /**
     * Get projects helped count (last 30 days)
     */
    protected function getProjectsHelpedCount(): int
    {
        return AIDecision::where('decision_type', 'project_analysis')
            ->where('user_action', 'accepted')
            ->where('created_at', '>=', now()->subDays(30))
            ->distinct('project_id')
            ->count('project_id');
    }
}
