<?php

namespace App\Http\Controllers\Admin\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\AIAnalyticsEngine;
use Illuminate\Http\Request;

class AIAnalyticsController extends Controller
{
    protected $analyticsEngine;

    public function __construct(AIAnalyticsEngine $analyticsEngine)
    {
        $this->middleware(['auth', 'can:view-ai-analytics']);
        $this->analyticsEngine = $analyticsEngine;
    }

    /**
     * Display analytics
     */
    public function index()
    {
        $analytics = $this->analyticsEngine->generateReport();

        return view('admin.ai-analytics.index', compact('analytics'));
    }

    /**
     * Get metrics data
     */
    public function metrics(Request $request)
    {
        $type = $request->input('type', 'summary');
        $range = $request->input('range', 30);
        $startDate = now()->subDays($range);
        $endDate = now();
        
        $data = match($type) {
            'summary' => $this->analyticsEngine->getSummaryMetrics($startDate, $endDate, null),
            'performance' => $this->analyticsEngine->getPerformanceMetrics($startDate, $endDate),
            'accuracy' => $this->analyticsEngine->getAccuracyMetrics($startDate, $endDate, null),
            'trends' => $this->analyticsEngine->getTrendAnalysis($startDate, $endDate),
            default => [],
        };

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
