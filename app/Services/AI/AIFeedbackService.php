<?php

namespace App\Services\AI;

use App\Models\AI\AIDecision;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AIFeedbackService
{
    /**
     * Track user feedback on AI decision
     */
    public function recordFeedback(AIDecision $decision, string $action, ?string $comment = null): void
    {
        // Update decision with user action
        $decision->update([
            'user_action' => $action,
            'user_comment' => $comment,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        // Log feedback for learning
        $this->logFeedbackForLearning($decision, $action);

        // Update confidence calibration
        $this->updateConfidenceCalibration($decision, $action);

        // Clear caches
        Cache::forget('ai_learning_metrics');
        Cache::forget('ai_feedback_patterns');
    }

    /**
     * Log feedback for machine learning
     */
    protected function logFeedbackForLearning(AIDecision $decision, string $action): void
    {
        DB::table('ai_feedback_logs')->insert([
            'decision_id' => $decision->id,
            'decision_type' => $decision->decision_type,
            'confidence_score' => $decision->confidence_score,
            'user_action' => $action,
            'was_correct' => in_array($action, ['accepted', 'modified']),
            'context' => json_encode([
                'entity_type' => $decision->entity_type,
                'entity_id' => $decision->entity_id,
                'reasoning_length' => strlen($decision->reasoning),
                'alternatives_count' => count($decision->alternatives ?? []),
            ]),
            'created_at' => now(),
        ]);

        Log::info('AI Feedback logged', [
            'decision_id' => $decision->id,
            'action' => $action,
            'confidence' => $decision->confidence_score,
        ]);
    }

    /**
     * Update confidence score calibration based on feedback
     */
    protected function updateConfidenceCalibration(AIDecision $decision, string $action): void
    {
        $decisionType = $decision->decision_type;
        $confidence = $decision->confidence_score;
        $wasAccepted = in_array($action, ['accepted', 'modified']);

        // Get current calibration data
        $calibration = Cache::remember("ai_calibration_{$decisionType}", 3600, function () use ($decisionType) {
            return $this->getCalibrationData($decisionType);
        });

        // Update calibration
        if ($wasAccepted) {
            $calibration['successful_predictions']++;
            if ($confidence >= 0.8) {
                $calibration['high_confidence_successes']++;
            }
        } else {
            $calibration['failed_predictions']++;
            if ($confidence >= 0.8) {
                $calibration['high_confidence_failures']++;
            }
        }

        $calibration['total_predictions']++;

        // Save updated calibration
        DB::table('ai_calibration_data')->updateOrInsert(
            ['decision_type' => $decisionType],
            [
                'data' => json_encode($calibration),
                'updated_at' => now(),
            ]
        );

        Cache::forget("ai_calibration_{$decisionType}");
    }

    /**
     * Get calibration data for decision type
     */
    protected function getCalibrationData(string $decisionType): array
    {
        $data = DB::table('ai_calibration_data')
            ->where('decision_type', $decisionType)
            ->first();

        if ($data) {
            return json_decode($data->data, true);
        }

        return [
            'successful_predictions' => 0,
            'failed_predictions' => 0,
            'total_predictions' => 0,
            'high_confidence_successes' => 0,
            'high_confidence_failures' => 0,
        ];
    }

    /**
     * Get learning metrics
     */
    public function getLearningMetrics(): array
    {
        return Cache::remember('ai_learning_metrics', 1800, function () {
            $totalDecisions = AIDecision::count();
            $reviewedDecisions = AIDecision::whereNotNull('reviewed_at')->count();
            
            $acceptedCount = AIDecision::where('user_action', 'accepted')->count();
            $rejectedCount = AIDecision::where('user_action', 'rejected')->count();
            $modifiedCount = AIDecision::where('user_action', 'modified')->count();

            $acceptanceRate = $reviewedDecisions > 0 
                ? ($acceptedCount / $reviewedDecisions) * 100 
                : 0;

            $modificationRate = $reviewedDecisions > 0
                ? ($modifiedCount / $reviewedDecisions) * 100
                : 0;

            // Accuracy trend (last 30 days)
            $accuracyTrend = $this->calculateAccuracyTrend();

            // Confidence calibration
            $calibrationAccuracy = $this->calculateCalibrationAccuracy();

            return [
                'total_decisions' => $totalDecisions,
                'reviewed_decisions' => $reviewedDecisions,
                'acceptance_rate' => round($acceptanceRate, 2),
                'rejection_rate' => round(($rejectedCount / max($reviewedDecisions, 1)) * 100, 2),
                'modification_rate' => round($modificationRate, 2),
                'accuracy_trend' => $accuracyTrend,
                'calibration_accuracy' => $calibrationAccuracy,
                'learning_progress' => $this->calculateLearningProgress(),
            ];
        });
    }

    /**
     * Calculate accuracy trend over time
     */
    protected function calculateAccuracyTrend(): array
    {
        $trend = DB::table('ai_feedback_logs')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN was_correct = 1 THEN 1 ELSE 0 END) as correct')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $trend->map(function ($item) {
            return [
                'date' => $item->date,
                'accuracy' => $item->total > 0 ? round(($item->correct / $item->total) * 100, 2) : 0,
            ];
        })->toArray();
    }

    /**
     * Calculate calibration accuracy
     */
    protected function calculateCalibrationAccuracy(): array
    {
        $calibrations = DB::table('ai_calibration_data')->get();

        $overall = [
            'high_confidence_accuracy' => 0,
            'medium_confidence_accuracy' => 0,
            'low_confidence_accuracy' => 0,
        ];

        foreach ($calibrations as $cal) {
            $data = json_decode($cal->data, true);
            
            if ($data['total_predictions'] > 0) {
                $accuracy = ($data['successful_predictions'] / $data['total_predictions']) * 100;
                
                if ($data['high_confidence_successes'] + $data['high_confidence_failures'] > 0) {
                    $overall['high_confidence_accuracy'] += 
                        ($data['high_confidence_successes'] / 
                        ($data['high_confidence_successes'] + $data['high_confidence_failures'])) * 100;
                }
            }
        }

        $count = $calibrations->count();
        if ($count > 0) {
            $overall['high_confidence_accuracy'] = round($overall['high_confidence_accuracy'] / $count, 2);
        }

        return $overall;
    }

    /**
     * Calculate learning progress
     */
    protected function calculateLearningProgress(): array
    {
        // Compare first week vs last week accuracy
        $firstWeek = DB::table('ai_feedback_logs')
            ->select(DB::raw('SUM(CASE WHEN was_correct = 1 THEN 1 ELSE 0 END) as correct, COUNT(*) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->where('created_at', '<', now()->subDays(23))
            ->first();

        $lastWeek = DB::table('ai_feedback_logs')
            ->select(DB::raw('SUM(CASE WHEN was_correct = 1 THEN 1 ELSE 0 END) as correct, COUNT(*) as total'))
            ->where('created_at', '>=', now()->subDays(7))
            ->first();

        $firstWeekAccuracy = $firstWeek && $firstWeek->total > 0 
            ? ($firstWeek->correct / $firstWeek->total) * 100 
            : 0;

        $lastWeekAccuracy = $lastWeek && $lastWeek->total > 0
            ? ($lastWeek->correct / $lastWeek->total) * 100
            : 0;

        $improvement = $lastWeekAccuracy - $firstWeekAccuracy;

        return [
            'first_week_accuracy' => round($firstWeekAccuracy, 2),
            'last_week_accuracy' => round($lastWeekAccuracy, 2),
            'improvement' => round($improvement, 2),
            'is_improving' => $improvement > 0,
        ];
    }

    /**
     * Get feedback patterns
     */
    public function getFeedbackPatterns(): array
    {
        return Cache::remember('ai_feedback_patterns', 1800, function () {
            // Most rejected decision types
            $mostRejected = DB::table('ai_decisions')
                ->select('decision_type', DB::raw('COUNT(*) as count'))
                ->where('user_action', 'rejected')
                ->groupBy('decision_type')
                ->orderByDesc('count')
                ->limit(5)
                ->get()
                ->toArray();

            // Most accepted decision types
            $mostAccepted = DB::table('ai_decisions')
                ->select('decision_type', DB::raw('COUNT(*) as count'))
                ->where('user_action', 'accepted')
                ->groupBy('decision_type')
                ->orderByDesc('count')
                ->limit(5)
                ->get()
                ->toArray();

            // Common reasons for rejection
            $rejectionReasons = DB::table('ai_decisions')
                ->select('user_comment')
                ->where('user_action', 'rejected')
                ->whereNotNull('user_comment')
                ->limit(10)
                ->get()
                ->pluck('user_comment')
                ->toArray();

            return [
                'most_rejected_types' => $mostRejected,
                'most_accepted_types' => $mostAccepted,
                'rejection_reasons' => $rejectionReasons,
            ];
        });
    }

    /**
     * Suggest confidence adjustment for decision type
     */
    public function suggestConfidenceAdjustment(string $decisionType): ?float
    {
        $calibration = $this->getCalibrationData($decisionType);

        if ($calibration['total_predictions'] < 10) {
            return null; // Not enough data
        }

        $actualAccuracy = $calibration['successful_predictions'] / $calibration['total_predictions'];
        $currentAvgConfidence = AIDecision::where('decision_type', $decisionType)
            ->avg('confidence_score');

        // If actual accuracy is lower than confidence, suggest decrease
        $difference = $actualAccuracy - $currentAvgConfidence;

        if (abs($difference) > 0.1) {
            return round($currentAvgConfidence + ($difference * 0.5), 2);
        }

        return null;
    }
}
