<?php

namespace App\Http\Controllers\Admin\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\AIAutomationService;
use Illuminate\Http\Request;

class AIWorkflowController extends Controller
{
    protected $automationService;

    public function __construct(AIAutomationService $automationService)
    {
        $this->middleware(['auth', 'can:manage-ai-settings']);
        $this->automationService = $automationService;
    }

    /**
     * Display workflows dashboard
     */
    public function index()
    {
        $activeRules = $this->automationService->getActiveRules();
        
        // Fetch Scheduled Jobs
        $scheduledJobs = \App\Models\AI\AISchedule::orderBy('run_at', 'desc')->limit(10)->get();
        
        // Get Workload Threshold
        $threshold = (int) (\App\Models\AI\AISetting::where('key', 'workload_threshold')->value('value') ?? 5);

        // Count available items for each feature
        $availableCounts = [
            'priority' => \App\Models\Task::where('status', 'in_progress')
                ->where(function ($query) {
                    $query->where('due_date', '<=', now()->addDays(3))
                        ->orWhereHas('dependencies', function ($q) {
                            $q->where('status', 'completed');
                        });
                })
                ->whereDoesntHave('aiDecisions', function ($q) {
                    $q->where('decision_type', 'priority_adjustment')
                      ->where('created_at', '>=', now()->subDay());
                })->count(),
            
            'assignment' => \App\Models\Task::doesntHave('assignedUsers')
                ->whereIn('status', ['pending', 'in_progress'])
                ->whereDoesntHave('aiDecisions', function ($q) {
                    $q->where('decision_type', 'assignment_suggestion')
                      ->where('created_at', '>=', now()->subDay());
                })
                ->count(),
            
            'deadline' => \App\Models\Task::where(function ($query) {
                    $query->where('due_date', '<', now())
                        ->orWhere('due_date', '<=', now()->addDays(3));
                })
                ->whereIn('status', ['in_progress', 'pending'])
                ->whereDoesntHave('aiDecisions', function ($q) {
                    $q->where('decision_type', 'deadline_extension')
                      ->where('created_at', '>=', now()->subDay());
                })
                ->count(),
            
            'projects' => \App\Models\Project::whereIn('status', ['active', 'in_progress'])
                ->count(),
            
            'overloaded_users' => \App\Models\User::whereHas('tasks', function ($query) {
                    $query->where('status', '!=', 'completed');
                }, '>', $threshold)
                ->count(),
        ];

        return view('admin.ai-workflows.index', compact(
            'activeRules',
            'scheduledJobs',
            'threshold',
            'availableCounts'
        ));
    }

    /**
     * Create automation rule
     */
    public function createRule(Request $request)
    {
        $request->validate([
            'trigger' => 'required|string',
            'conditions' => 'required|array',
            'action' => 'required|string',
        ]);

        try {
            $rule = $this->automationService->createAutomationRule([
                'trigger' => $request->trigger,
                'conditions' => $request->conditions,
                'action' => $request->action,
                'enabled' => $request->boolean('enabled', true),
            ]);

            return response()->json([
                'success' => true,
                'rule' => $rule,
                'message' => 'Automation rule created successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create automation rule',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Schedule analysis
     */
    public function scheduleAnalysis(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'params' => 'nullable|array',
            'run_at' => 'required|date|after:now',
        ]);

        try {
            $this->automationService->scheduleAnalysis(
                $request->type,
                $request->params ?? [],
                \Carbon\Carbon::parse($request->run_at)
            );

            return response()->json([
                'success' => true,
                'message' => 'Analysis scheduled successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule analysis',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Run automation manually
     */
    public function runAutomation()
    {
        try {
            $results = $this->automationService->runAutomatedAnalysis();

            return response()->json([
                'success' => true,
                'results' => $results,
                'message' => 'Automation completed successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Automation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get workload balance recommendations
     */
    public function workloadBalance()
    {
        try {
            $recommendations = $this->automationService->balanceWorkload();

            return response()->json([
                'success' => true,
                'recommendations' => $recommendations,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze workload',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Update workload threshold
     */
    public function updateWorkloadThreshold(Request $request)
    {
        $request->validate([
            'threshold' => 'required|integer|min:1|max:50',
        ]);

        \App\Models\AI\AISetting::updateOrCreate(
            ['key' => 'workload_threshold'],
            [
                'value' => $request->threshold,
                'type' => 'integer',
                'group' => 'system',
                'description' => 'Max tasks per user before flag'
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Threshold updated successfully'
        ]);
    }

    /**
     * Run specific AI feature (Cloud Mode Batch Processing)
     */
    public function runFeature(Request $request)
    {
        $request->validate([
            'feature' => 'required|in:priority,assignment,deadline,projects',
            'limit' => 'required|integer|min:1|max:50'
        ]);

        try {
            $feature = $request->feature;
            $limit = $request->limit;

            // Route to appropriate batch method
            $result = match($feature) {
                'priority' => $this->automationService->checkPriorityAdjustmentsBatch($limit),
                'assignment' => $this->automationService->checkAssignmentSuggestionsBatch($limit),
                'deadline' => $this->automationService->checkDeadlineExtensionsBatch($limit),
                'projects' => $this->automationService->checkProjectHealthBatch($limit),
                default => ['error' => 'Unknown feature']
            };

            return response()->json([
                'success' => true,
                'feature' => $feature,
                'result' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Feature execution failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
