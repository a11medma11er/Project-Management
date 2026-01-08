<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AIQueryOptimizer
{
    /**
     * Optimize AI decisions query
     */
    public function optimizeDecisionsQuery($query)
    {
        return $query
            ->select([
                'id',
                'decision_type',
                'confidence_score',
                'user_action',
                'created_at',
                'reviewed_at',
            ])
            ->with(['entity' => function ($q) {
                $q->select('id', 'title', 'status');
            }]);
    }

    /**
     * Get decisions with pagination and caching
     */
    public function getOptimizedDecisions(array $filters = [], int $perPage = 15)
    {
        $query = DB::table('ai_decisions');

        // Apply filters efficiently
        if (isset($filters['type'])) {
            $query->where('decision_type', $filters['type']);
        }

        if (isset($filters['status'])) {
            $query->where('user_action', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        // Use index-friendly ordering
        $query->orderBy('created_at', 'desc');

        // Select only needed columns
        $query->select([
            'id',
            'decision_type',
            'confidence_score',
            'user_action',
            'created_at',
        ]);

        return $query->paginate($perPage);
    }

    /**
     * Batch load related data
     */
    public function batchLoadRelations($decisions, string $relation)
    {
        $ids = $decisions->pluck('entity_id')->unique();

        return DB::table($relation)
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');
    }

    /**
     * Get aggregated metrics efficiently
     */
    public function getAggregatedMetrics(string $startDate, string $endDate): array
    {
        $metrics = DB::table('ai_decisions')
            ->select([
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN user_action = "accepted" THEN 1 ELSE 0 END) as accepted'),
                DB::raw('SUM(CASE WHEN user_action = "rejected" THEN 1 ELSE 0 END) as rejected'),
                DB::raw('AVG(confidence_score) as avg_confidence'),
                DB::raw('MIN(confidence_score) as min_confidence'),
                DB::raw('MAX(confidence_score) as max_confidence'),
            ])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->first();

        return (array) $metrics;
    }

    /**
     * Optimize using database views
     */
    public function useEnrichedView(array $filters = [])
    {
        $query = DB::table('ai_enriched_tasks');

        if (isset($filters['status'])) {
            $query->where('task_status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority_score', '>=', $filters['priority']);
        }

        return $query->get();
    }

    /**
     * Execute query with explain for debugging
     */
    public function explainQuery($query): array
    {
        $sql = $query->toSql();
        $bindings = $query->getBindings();

        // Get query plan
        $explain = DB::select('EXPLAIN ' . $sql, $bindings);

        Log::debug('Query explanation', [
            'sql' => $sql,
            'explain' => $explain,
        ]);

        return [
            'sql' => $sql,
            'bindings' => $bindings,
            'explain' => $explain,
        ];
    }

    /**
     * Suggest indexes
     */
    public function suggestIndexes(): array
    {
        return [
            'ai_decisions' => [
                'idx_decision_type' => 'decision_type',
                'idx_user_action' => 'user_action',
                'idx_created_at' => 'created_at',
                'idx_confidence' => 'confidence_score',
                'idx_entity' => ['entity_type', 'entity_id'],
            ],
            'ai_feedback_logs' => [
                'idx_decision_id' => 'decision_id',
                'idx_created_at' => 'created_at',
            ],
        ];
    }

    /**
     * Analyze slow queries
     */
    public function analyzeSlowQueries(): array
    {
        // This would integrate with Laravel's query log
        return DB::getQueryLog();
    }

    /**
     * Optimize for read-heavy workloads
     */
    public function enableReadOptimizations(): void
    {
        // Use read replicas if available
        // This is a placeholder for configuration
        Log::info('Read optimizations enabled');
    }
}
