<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\AI\ContextBuilder;
use App\Services\AI\AIGateway;
use App\Services\ActivityService;
use Illuminate\Http\Request;

/**
 * Test Controller for Context Readiness Features
 * 
 * This controller provides endpoints to test and demo
 * the Context Readiness implementation.
 */
class ContextTestController extends Controller
{
    
    /**
     * Test Context Builder
     * 
     * Route: GET /management/context-test/builder/{task}
     */
    public function testContextBuilder(Task $task)
    {
        $this->authorize('view-tasks');
        
        $contextBuilder = app(ContextBuilder::class);
        
        // Build context
        $context = $contextBuilder->buildTaskContext($task);
        
        return response()->json([
            'success' => true,
            'task_id' => $task->id,
            'task_title' => $task->title,
            'context' => $context->toArray(),
            'summary' => $context->getSummary(),
            'is_ready' => $context->isReady(),
        ], 200, [], JSON_PRETTY_PRINT);
    }
    
    /**
     * Test Activity Service
     * 
     * Route: GET /management/context-test/activities/{task}
     */
    public function testActivityService(Task $task)
    {
        $this->authorize('view-tasks');
        
        $activityService = app(ActivityService::class);
        $activities = $activityService->getTaskActivities($task);
        
        return response()->json([
            'success' => true,
            'task_id' => $task->id,
            'activities_count' => $activities->count(),
            'activities' => $activities->map(function($activity) {
                return [
                    'id' => $activity->id,
                    'description' => $activity->description,
                    'causer' => $activity->causer?->name,
                    'created_at' => $activity->created_at->toIso8601String(),
                    'properties' => $activity->properties,
                ];
            }),
        ], 200, [], JSON_PRETTY_PRINT);
    }
    
    /**
     * Test AI Gateway Status
     * 
     * Route: GET /management/context-test/ai-status
     */
    public function testAIStatus()
    {
        $this->authorize('view-tasks');
        
        $aiGateway = app(AIGateway::class);
        
        return response()->json([
            'success' => true,
            'ai_status' => $aiGateway->getStatus(),
            'message' => $aiGateway->isAvailable() 
                ? 'AI is available and ready' 
                : 'AI is not configured (system works without it)',
        ], 200, [], JSON_PRETTY_PRINT);
    }
    
    /**
     * Test User Patterns
     * 
     * Route: GET /management/context-test/user-patterns/{userId}
     */
    public function testUserPatterns(int $userId)
    {
        $this->authorize('view-tasks');
        
        $activityService = app(ActivityService::class);
        $patterns = $activityService->getUserPatterns($userId);
        
        return response()->json([
            'success' => true,
            'user_id' => $userId,
            'patterns' => $patterns,
        ], 200, [], JSON_PRETTY_PRINT);
    }
    
    /**
     * Demo Dashboard
     * 
     * Route: GET /management/context-test/demo
     */
    public function demo()
    {
        $this->authorize('view-tasks');
        
        // Get a sample task
        $task = Task::with(['assignedUsers', 'project'])->first();
        
        if (!$task) {
            return view('management.context-test.demo', [
                'error' => 'No tasks found. Please create a task first.',
            ]);
        }
        
        // Manual service instantiation
        $activityService = app(ActivityService::class);
        $contextBuilder = app(ContextBuilder::class);
        $aiGateway = app(AIGateway::class);
        
        // Build context
        $context = $contextBuilder->buildTaskContext($task);
        
        // Get activities
        $activities = $activityService->getTaskActivities($task);
        
        // AI status
        $aiStatus = $aiGateway->getStatus();
        
        return view('management.context-test.demo', compact('task', 'context', 'activities', 'aiStatus'));
    }
}
