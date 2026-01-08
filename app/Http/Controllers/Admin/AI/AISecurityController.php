<?php

namespace App\Http\Controllers\Admin\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\AISecurityService;
use Illuminate\Http\Request;

class AISecurityController extends Controller
{
    protected $securityService;

    public function __construct(AISecurityService $securityService)
    {
        $this->middleware(['auth', 'can:manage-ai-settings']);
        $this->securityService = $securityService;
    }

    /**
     * Display security dashboard
     */
    public function index()
    {
        $metrics = $this->securityService->getSecurityMetrics(7);
        $recommendations = $this->securityService->getSecurityRecommendations();

        return view('admin.ai-security.index', compact('metrics', 'recommendations'));
    }

    /**
     * Check rate limit status
     */
    public function checkRateLimit(Request $request)
    {
        $userId = $request->input('user_id', auth()->id());
        $action = $request->input('action', 'ai_action');

        $allowed = $this->securityService->checkRateLimit($userId, $action);

        return response()->json([
            'success' => true,
            'allowed' => $allowed,
        ]);
    }

    /**
     * Validate input
     */
    public function validateInput(Request $request)
    {
        $input = $request->input('data');

        $sanitized = $this->securityService->sanitizeInput($input);
        $suspicious = $this->securityService->detectSuspiciousActivity([$input]);

        return response()->json([
            'success' => true,
            'sanitized' => $sanitized,
            'suspicious' => $suspicious,
        ]);
    }

    /**
     * Get security metrics
     */
    public function getMetrics(Request $request)
    {
        $days = $request->input('days', 7);

        $metrics = $this->securityService->getSecurityMetrics($days);

        return response()->json([
            'success' => true,
            'metrics' => $metrics,
        ]);
    }
}
