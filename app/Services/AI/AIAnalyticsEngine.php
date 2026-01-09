<?php

namespace App\Services\AI;

use App\Models\AI\AIDecision;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AIAnalyticsEngine
{
    /**
     * Generate comprehensive analytics report
     */
    public function generateReport(array $filters = []): array
    {
        $startDate = $filters['start_date'] ?? now()->subDays(30);
        $endDate = $filters['end_date'] ?? now();
        $decisionTypes = $filters['decision_types'] ?? null;

        return [
            'summary' => $this->getSummaryMetrics($startDate, $endDate, $decisionTypes),
            'performance' => $this->getPerformanceMetrics($startDate, $endDate),
            'accuracy' => $this->getAccuracyMetrics($startDate, $endDate, $decisionTypes),
            'user_engagement' => $this->getUserEngagementMetrics($startDate, $endDate),
            'decision_breakdown' => $this->getDecisionBreakdown($startDate, $endDate, $decisionTypes),
            'trends' => $this->getTrendAnalysis($startDate, $endDate),
            'impact' => $this->getImpactAnalysis($startDate, $endDate),
        ];
    }

    /**
     * Get summary metrics
     */
    public function getSummaryMetrics($startDate, $endDate, $decisionTypes): array
    {
        $query = AIDecision::whereBetween('created_at', [$startDate, $endDate]);
        
        if ($decisionTypes) {
            $query->whereIn('decision_type', $decisionTypes);
        }

        $total = $query->count();
        $accepted = (clone $query)->where('user_action', 'accepted')->count();
        $rejected = (clone $query)->where('user_action', 'rejected')->count();
        $modified = (clone $query)->where('user_action', 'modified')->count();
        $pending = (clone $query)->where('user_action', 'pending')->count();

        $avgConfidence = $query->avg('confidence_score');
        
        return [
            'total_decisions' => $total,
            'accepted' => $accepted,
            'rejected' => $rejected,
            'modified' => $modified,
            'pending' => $pending,
            'acceptance_rate' => $total > 0 ? round(($accepted / $total) * 100, 2) : 0,
            'rejection_rate' => $total > 0 ? round(($rejected / $total) * 100, 2) : 0,
            'modification_rate' => $total > 0 ? round(($modified / $total) * 100, 2) : 0,
            'avg_confidence' => round($avgConfidence ?? 0, 4),
        ];
    }

    /**
     * Get performance metrics
     */
    public function getPerformanceMetrics($startDate, $endDate): array
    {
        $decisions = AIDecision::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('reviewed_at')
            ->get();

        $avgResponseTime = $decisions->avg(function ($decision) {
            return $decision->reviewed_at->diffInMinutes($decision->created_at);
        });

        return [
            'avg_response_time_minutes' => round($avgResponseTime ?? 0, 2),
            'decisions_per_day' => $decisions->count() / max(1, $startDate->diffInDays($endDate)),
            'high_confidence_count' => $decisions->where('confidence_score', '>=', 0.8)->count(),
            'low_confidence_count' => $decisions->where('confidence_score', '<', 0.6)->count(),
        ];
    }

    /**
     * Get accuracy metrics
     */
    public function getAccuracyMetrics($startDate, $endDate, $decisionTypes): array
    {
        $query = AIDecision::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('user_action', ['accepted', 'rejected', 'modified']);
        
        if ($decisionTypes) {
            $query->whereIn('decision_type', $decisionTypes);
        }

        $total = $query->count();
        $correct = $query->whereIn('user_action', ['accepted', 'modified'])->count();

        // Accuracy by confidence level
        $highConfidence = $query->where('confidence_score', '>=', 0.8)->get();
        $mediumConfidence = $query->whereBetween('confidence_score', [0.6, 0.8])->get();
        $lowConfidence = $query->where('confidence_score', '<', 0.6)->get();

        return [
            'overall_accuracy' => $total > 0 ? round(($correct / $total) * 100, 2) : 0,
            'high_confidence_accuracy' => $this->calculateAccuracy($highConfidence),
            'medium_confidence_accuracy' => $this->calculateAccuracy($mediumConfidence),
            'low_confidence_accuracy' => $this->calculateAccuracy($lowConfidence),
        ];
    }

    /**
     * Calculate accuracy for collection
     */
    protected function calculateAccuracy($collection): float
    {
        if ($collection->count() === 0) {
            return 0;
        }

        $correct = $collection->whereIn('user_action', ['accepted', 'modified'])->count();
        return round(($correct / $collection->count()) * 100, 2);
    }

    /**
     * Get user engagement metrics
     */
    protected function getUserEngagementMetrics($startDate, $endDate): array
    {
        $decisions = AIDecision::whereBetween('created_at', [$startDate, $endDate])->get();

        $reviewed = $decisions->whereNotNull('reviewed_at')->count();
        $withComments = $decisions->whereNotNull('user_comment')->count();
        
        $uniqueReviewers = $decisions->whereNotNull('reviewed_by')->pluck('reviewed_by')->unique()->count();

        return [
            'review_rate' => $decisions->count() > 0 
                ? round(($reviewed / $decisions->count()) * 100, 2) 
                : 0,
            'comment_rate' => $decisions->count() > 0 
                ? round(($withComments / $decisions->count()) * 100, 2) 
                : 0,
            'active_reviewers' => $uniqueReviewers,
        ];
    }

    /**
     * Get decision breakdown by type
     */
    protected function getDecisionBreakdown($startDate, $endDate, $decisionTypes): array
    {
        $query = AIDecision::select('decision_type', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('decision_type');

        if ($decisionTypes) {
            $query->whereIn('decision_type', $decisionTypes);
        }

        return $query->get()->mapWithKeys(function ($item) {
            return [$item->decision_type => $item->count];
        })->toArray();
    }

    /**
     * Get trend analysis
     */
    public function getTrendAnalysis($startDate, $endDate): array
    {
        $dailyStats = AIDecision::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN user_action = "accepted" THEN 1 ELSE 0 END) as accepted'),
                DB::raw('AVG(confidence_score) as avg_confidence')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $dailyStats->map(function ($item) {
            return [
                'date' => $item->date,
                'total' => $item->total,
                'accepted' => $item->accepted,
                'acceptance_rate' => $item->total > 0 ? round(($item->accepted / $item->total) * 100, 2) : 0,
                'avg_confidence' => round($item->avg_confidence ?? 0, 4),
            ];
        })->toArray();
    }

    /**
     * Get impact analysis
     */
    protected function getImpactAnalysis($startDate, $endDate): array
    {
        $acceptedDecisions = AIDecision::where('user_action', 'accepted')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $tasksAffected = Task::whereHas('aiDecisions', function ($query) use ($startDate, $endDate) {
            $query->where('user_action', 'accepted')
                ->whereBetween('created_at', [$startDate, $endDate]);
        })->count();

        $projectsAffected = Project::whereHas('aiDecisions', function ($query) use ($startDate, $endDate) {
            $query->where('user_action', 'accepted')
                ->whereBetween('created_at', [$startDate, $endDate]);
        })->count();

        return [
            'accepted_decisions' => $acceptedDecisions,
            'tasks_affected' => $tasksAffected,
            'projects_affected' => $projectsAffected,
            'avg_decisions_per_task' => $tasksAffected > 0 
                ? round($acceptedDecisions / $tasksAffected, 2) 
                : 0,
        ];
    }

    /**
     * Compare AI vs Human performance
     */
    public function compareAIvsHuman($startDate, $endDate): array
    {
        $aiDecisions = AIDecision::whereBetween('created_at', [$startDate, $endDate])->get();

        $aiAccepted = $aiDecisions->where('user_action', 'accepted')->count();
        $aiRejected = $aiDecisions->where('user_action', 'rejected')->count();
        
        $avgConfidence = $aiDecisions->avg('confidence_score');

        return [
            'ai_suggestions' => $aiDecisions->count(),
            'ai_accepted' => $aiAccepted,
            'ai_rejected' => $aiRejected,
            'ai_acceptance_rate' => $aiDecisions->count() > 0 
                ? round(($aiAccepted / $aiDecisions->count()) * 100, 2) 
                : 0,
            'avg_ai_confidence' => round($avgConfidence ?? 0, 4),
        ];
    }

    /**
     * Get decision type performance
     */
    public function getDecisionTypePerformance(): array
    {
        $types = AIDecision::select('decision_type')
            ->distinct()
            ->pluck('decision_type');

        return $types->mapWithKeys(function ($type) {
            $decisions = AIDecision::where('decision_type', $type)->get();
            $reviewed = $decisions->whereNotNull('reviewed_at');
            
            return [$type => [
                'total' => $decisions->count(),
                'acceptance_rate' => $reviewed->count() > 0 
                    ? round(($reviewed->where('user_action', 'accepted')->count() / $reviewed->count()) * 100, 2) 
                    : 0,
                'avg_confidence' => round($decisions->avg('confidence_score') ?? 0, 4),
            ]];
        })->toArray();
    }
}
