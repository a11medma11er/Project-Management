<?php

namespace App\Http\Controllers\Admin\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\AIIntegrationService;
use Illuminate\Http\Request;

class AIIntegrationController extends Controller
{
    protected $integrationService;

    public function __construct(AIIntegrationService $integrationService)
    {
        $this->middleware(['auth', 'can:manage-ai-settings']);
        $this->integrationService = $integrationService;
    }

    /**
     * Display integrations dashboard
     */
    public function index()
    {
        $health = $this->integrationService->getIntegrationHealth();
        
        return view('admin.ai-integrations.index', compact('health'));
    }

    /**
     * Test external AI provider
     */
    public function testProvider(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:openai,claude,local',
            'prompt' => 'required|string|max:500',
        ]);

        try {
            // Check rate limit
            if (!$this->integrationService->checkRateLimit($request->provider)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rate limit exceeded. Please try again later.',
                ], 429);
            }

            $result = $this->integrationService->callExternalAI(
                $request->provider,
                $request->prompt
            );

            return response()->json([
                'success' => true,
                'result' => $result,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Provider test failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send test webhook
     */
    public function testWebhook(Request $request)
    {
        $request->validate([
            'event' => 'required|string',
            'data' => 'nullable|array',
        ]);

        try {
            $sent = $this->integrationService->sendWebhook(
                $request->event,
                $request->data ?? ['test' => true]
            );

            return response()->json([
                'success' => $sent,
                'message' => $sent ? 'Webhook sent successfully' : 'Webhook delivery failed',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Webhook test failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send test Slack notification
     */
    public function testSlack(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        try {
            $sent = $this->integrationService->sendSlackNotification(
                $request->message,
                [
                    'channel' => $request->channel,
                    'username' => $request->username ?? 'AI Test',
                ]
            );

            return response()->json([
                'success' => $sent,
                'message' => $sent ? 'Slack notification sent' : 'Slack notification failed',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Slack test failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get integration health status
     */
    public function health()
    {
        try {
            $health = $this->integrationService->getIntegrationHealth();

            return response()->json([
                'success' => true,
                'health' => $health,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get health status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
