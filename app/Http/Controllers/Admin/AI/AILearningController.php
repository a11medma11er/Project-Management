<?php

namespace App\Http\Controllers\Admin\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\AIFeedbackService;
use App\Models\AI\AIDecision;
use Illuminate\Http\Request;

class AILearningController extends Controller
{
    protected $feedbackService;

    public function __construct(AIFeedbackService $feedbackService)
    {
        $this->middleware(['auth', 'can:view-ai-analytics']);
        $this->feedbackService = $feedbackService;
    }

    /**
     * Display AI learning dashboard
     */
    public function index()
    {
        // Get learning metrics
        $metrics = $this->feedbackService->getLearningMetrics();
        
        // Get feedback patterns
        $patterns = $this->feedbackService->getFeedbackPatterns();
        
        // Get calibration suggestions
        $calibrationSuggestions = $this->getCalibrationSuggestions();
        
        // Recent improvements
        $recentImprovements = $this->getRecentImprovements();
        
        return view('admin.ai-learning.index', compact(
            'metrics',
            'patterns',
            'calibrationSuggestions',
            'recentImprovements'
        ));
    }

    /**
     * Get calibration suggestions
     */
    protected function getCalibrationSuggestions(): array
    {
        $decisionTypes = AIDecision::select('decision_type')
            ->distinct()
            ->pluck('decision_type');
        
        $suggestions = [];
        
        foreach ($decisionTypes as $type) {
            $adjustment = $this->feedbackService->suggestConfidenceAdjustment($type);
            
            if ($adjustment !== null) {
                $currentAvg = AIDecision::where('decision_type', $type)
                    ->avg('confidence_score');
                
                $suggestions[] = [
                    'decision_type' => $type,
                    'current_avg_confidence' => round($currentAvg, 2),
                    'suggested_confidence' => $adjustment,
                    'difference' => round($adjustment - $currentAvg, 2),
                ];
            }
        }
        
        return $suggestions;
    }

    /**
     * Get recent improvements
     */
    protected function getRecentImprovements(): array
    {
        $improvements = [];
        
        // Check each decision type for improvements
        $decisionTypes = AIDecision::select('decision_type')
            ->distinct()
            ->pluck('decision_type');
        
        foreach ($decisionTypes as $type) {
            $lastMonth = AIDecision::where('decision_type', $type)
                ->where('created_at', '>=', now()->subDays(30))
                ->where('created_at', '<', now()->subDays(15))
                ->whereIn('user_action', ['accepted', 'rejected'])
                ->get();
            
            $lastTwoWeeks = AIDecision::where('decision_type', $type)
                ->where('created_at', '>=', now()->subDays(14))
                ->whereIn('user_action', ['accepted', 'rejected'])
                ->get();
            
            if ($lastMonth->count() > 5 && $lastTwoWeeks->count() > 5) {
                $oldAccuracy = $lastMonth->where('user_action', 'accepted')->count() / $lastMonth->count();
                $newAccuracy = $lastTwoWeeks->where('user_action', 'accepted')->count() / $lastTwoWeeks->count();
                
                $improvement = ($newAccuracy - $oldAccuracy) * 100;
                
                if (abs($improvement) > 1) {
                    $improvements[] = [
                        'decision_type' => $type,
                        'old_accuracy' => round($oldAccuracy * 100, 2),
                        'new_accuracy' => round($newAccuracy * 100, 2),
                        'improvement' => round($improvement, 2),
                        'is_positive' => $improvement > 0,
                    ];
                }
            }
        }
        
        // Sort by improvement
        usort($improvements, function($a, $b) {
            return $b['improvement'] <=> $a['improvement'];
        });
        
        return array_slice($improvements, 0, 5);
    }

    /**
     * Get learning data (API endpoint)
     */
    public function getData()
    {
        $metrics = $this->feedbackService->getLearningMetrics();
        
        return response()->json([
            'success' => true,
            'metrics' => $metrics,
        ]);
    }
}
