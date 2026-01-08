<?php

namespace App\Http\Controllers\Admin\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\AICacheService;
use App\Services\AI\AIQueryOptimizer;
use App\Services\AI\AIPerformanceMonitor;
use Illuminate\Http\Request;

class AIPerformanceController extends Controller
{
    protected $cacheService;
    protected $queryOptimizer;
    protected $performanceMonitor;

    public function __construct(
        AICacheService $cacheService,
        AIQueryOptimizer $queryOptimizer,
        AIPerformanceMonitor $performanceMonitor
    ) {
        $this->middleware(['auth', 'can:manage-ai-settings']);
        $this->cacheService = $cacheService;
        $this->queryOptimizer = $queryOptimizer;
        $this->performanceMonitor = $performanceMonitor;
    }

    /**
     * Display performance dashboard
     */
    public function index()
    {
        $cacheStats = $this->cacheService->getStats();
        $perfStats = $this->performanceMonitor->getStats(24);
        $systemMetrics = $this->performanceMonitor->getSystemMetrics();
        $slowOperations = $this->performanceMonitor->getSlowOperations(1000, 24);

        return view('admin.ai-performance.index', compact(
            'cacheStats',
            'perfStats',
            'systemMetrics',
            'slowOperations'
        ));
    }

    /**
     * Get performance statistics
     */
    public function getStats(Request $request)
    {
        $hours = $request->input('hours', 1);
        
        try {
            $stats = $this->performanceMonitor->getStats($hours);
            
            return response()->json([
                'success' => true,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get performance stats',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear cache
     */
    public function clearCache(Request $request)
    {
        try {
            $pattern = $request->input('pattern', '*');
            
            if ($pattern === '*') {
                $this->cacheService->clearAll();
                $message = 'All AI caches cleared';
            } else {
                $this->cacheService->invalidate($pattern);
                $message = "Cache pattern '{$pattern}' cleared";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Warm up cache
     */
    public function warmUpCache()
    {
        try {
            $warmed = $this->cacheService->warmUp();
            
            return response()->json([
                'success' => true,
                'warmed' => $warmed,
                'message' => 'Cache warmed up successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to warm up cache',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get suggested indexes
     */
    public function getSuggestedIndexes()
    {
        try {
            $indexes = $this->queryOptimizer->suggestIndexes();
            
            return response()->json([
                'success' => true,
                'indexes' => $indexes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get suggested indexes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get slow operations
     */
    public function getSlowOperations(Request $request)
    {
        $threshold = $request->input('threshold', 1000);
        $hours = $request->input('hours', 24);
        
        try {
            $operations = $this->performanceMonitor->getSlowOperations($threshold, $hours);
            
            return response()->json([
                'success' => true,
                'operations' => array_values($operations),
                'count' => count($operations),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get slow operations',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get system metrics
     */
    public function getSystemMetrics()
    {
        try {
            $metrics = $this->performanceMonitor->getSystemMetrics();
            
            return response()->json([
                'success' => true,
                'metrics' => $metrics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get system metrics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
