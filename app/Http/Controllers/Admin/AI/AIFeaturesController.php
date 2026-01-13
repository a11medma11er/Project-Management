<?php

namespace App\Http\Controllers\Admin\AI;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIFeaturesController extends Controller
{
    protected $aiGateway;
    protected $decisionEngine;

    public function __construct(\App\Services\AI\AIGateway $aiGateway, \App\Services\AI\AIDecisionEngine $decisionEngine)
    {
        $this->middleware(['auth']);
        $this->aiGateway = $aiGateway;
        $this->decisionEngine = $decisionEngine;
    }

    /**
     * Display AI features page
     */
    public function index()
    {
        return view('admin.ai-features.index');
    }

    /**
     * Generate development plan for a project
     */
    public function createDevelopmentPlan(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'requirements' => 'nullable|string|max:5000',
        ]);

        try {
            $project = Project::with(['tasks', 'members'])->findOrFail($request->project_id);
            
            // Analyze project context
            $context = $this->analyzeProjectContext($project, $request->requirements);
            
            // Generate development plan
            $plan = $this->generateDevelopmentPlan($context);
            
            // Get AI provider info for transparency
            $aiGateway = app(\App\Services\AI\AIGateway::class);
            $modelInfo = $aiGateway->getModelInfo();
            
            // STORE RESULT IN DATABASE
            \App\Models\AI\AIGeneratedContent::create([
                'project_id' => $project->id,
                'user_id' => auth()->id(),
                'feature_type' => 'development_plan',
                'prompt_name' => 'ai_feature_development_plan',
                'content' => $plan,
                'metrics' => [ // Optional: metrics logic to be refined
                    'generated_at' => now(),
                    'input_tokens' => 0, // Placeholder
                    'output_tokens' => 0 // Placeholder
                ],
                'provider' => $modelInfo['provider'] ?? null,
                'model' => $modelInfo['model'] ?? null,
            ]);
            
            // Log activity
            activity('ai')
                ->causedBy(auth()->user())
                ->performedOn($project)
                ->withProperties(['feature' => 'development_plan'])
                ->log('generated_development_plan');
            
            return response()->json([
                'success' => true,
                'plan' => $plan,
                'project' => $project->title,
                'ai_info' => $modelInfo,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Development plan generation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate development plan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Analyze project context
     */
    protected function analyzeProjectContext(Project $project, ?string $additionalRequirements): array
    {
        $existingTasks = $project->tasks()->count();
        $completedTasks = $project->tasks()->where('status', 'completed')->count();
        $progress = $project->progress ?? 0;
        
        return [
            'project_id' => $project->id,
            'project_title' => $project->title,
            'project_description' => $project->description,
            'budget' => $project->budget,
            'deadline' => $project->end_date,
            'team_size' => $project->members()->count(),
            'existing_tasks' => $existingTasks,
            'completed_tasks' => $completedTasks,
            'progress' => $progress,
            'priority' => $project->priority,
            'additional_requirements' => $additionalRequirements,
        ];
    }

    /**
     * Generate development plan based on context
     */
    protected function generateDevelopmentPlan(array $context): array
    {
        // Try AI generation with system prompt
        $aiPlan = $this->aiGateway->suggest('ai_feature_development_plan', [
            'project_title' => $context['project_title'],
            'project_description' => $context['project_description'] ?? 'No description provided',
            'budget' => $context['budget'] ?? 'Not specified',
            'deadline' => $context['deadline'] ?? 'Not specified',
            'team_size' => $context['team_size'],
            'existing_tasks' => $context['existing_tasks'],
            'progress' => $context['progress'],
        ]);
        
        if ($aiPlan && $this->validatePlanStructure($aiPlan)) {
            return $aiPlan;
        }

        throw new \Exception('Failed to generate development plan. AI service returned no valid data.');
    }

    /**
     * Generate project overview
     */
    protected function generateOverview(array $context): array
    {
        return [
            'title' => 'Development Plan for: ' . $context['project_title'],
            'summary' => 'AI-generated comprehensive development plan based on project analysis',
            'estimated_duration' => $this->estimateDuration($context),
            'complexity' => $this->assessComplexity($context),
            'confidence' => 0.85,
        ];
    }

    /**
     * Generate development phases
     */
    protected function generatePhases(array $context): array
    {
        $basePhases = [
            [
                'name' => 'Phase 1: Planning & Analysis',
                'duration' => '1-2 weeks',
                'tasks' => [
                    'Requirements gathering',
                    'Technical feasibility study',
                    'Resource allocation',
                    'Risk assessment',
                ],
                'deliverables' => ['Requirements document', 'Technical specification', 'Project plan'],
            ],
            [
                'name' => 'Phase 2: Design',
                'duration' => '2-3 weeks',
                'tasks' => [
                    'System architecture design',
                    'Database schema design',
                    'UI/UX design',
                    'API specification',
                ],
                'deliverables' => ['Architecture diagram', 'Database schema', 'UI mockups', 'API docs'],
            ],
            [
                'name' => 'Phase 3: Development',
                'duration' => '4-8 weeks',
                'tasks' => [
                    'Backend development',
                    'Frontend development',
                    'Database implementation',
                    'API integration',
                ],
                'deliverables' => ['Working application', 'Test cases', 'Documentation'],
            ],
            [
                'name' => 'Phase 4: Testing & QA',
                'duration' => '2-3 weeks',
                'tasks' => [
                    'Unit testing',
                    'Integration testing',
                    'User acceptance testing',
                    'Performance testing',
                ],
                'deliverables' => ['Test reports', 'Bug fixes', 'Performance metrics'],
            ],
            [
                'name' => 'Phase 5: Deployment',
                'duration' => '1 week',
                'tasks' => [
                    'Production setup',
                    'Data migration',
                    'User training',
                    'Go-live',
                ],
                'deliverables' => ['Deployed application', 'Training materials', 'Support documentation'],
            ],
        ];
        
        // Adjust based on project complexity and size
        if ($context['existing_tasks'] > 50) {
            $basePhases[2]['duration'] = '8-12 weeks';
        }
        
        return $basePhases;
    }

    /**
     * Generate timeline
     */
    protected function generateTimeline(array $context): array
    {
        $estimatedWeeks = $this->estimateDuration($context);
        $startDate = now();
        
        return [
            'start_date' => $startDate->format('Y-m-d'),
            'estimated_end_date' => $startDate->addWeeks($estimatedWeeks)->format('Y-m-d'),
            'total_weeks' => $estimatedWeeks,
            'milestones' => [
                ['week' => 2, 'milestone' => 'Requirements finalized'],
                ['week' => 5, 'milestone' => 'Design approved'],
                ['week' => $estimatedWeeks - 4, 'milestone' => 'Development complete'],
                ['week' => $estimatedWeeks - 2, 'milestone' => 'Testing complete'],
                ['week' => $estimatedWeeks, 'milestone' => 'Production deployment'],
            ],
        ];
    }

    /**
     * Generate resource plan
     */
    protected function generateResourcePlan(array $context): array
    {
        return [
            'team_requirements' => [
                'Backend developers' => 2,
                'Frontend developers' => 2,
                'UI/UX designer' => 1,
                'QA engineer' => 1,
                'DevOps engineer' => 1,
            ],
            'current_team' => $context['team_size'],
            'additional_resources_needed' => max(0, 7 - $context['team_size']),
            'budget_allocation' => [
                'Development' => '50%',
                'Testing & QA' => '20%',
                'Infrastructure' => '15%',
                'Contingency' => '15%',
            ],
        ];
    }

    /**
     * Identify potential risks
     */
    protected function identifyRisks(array $context): array
    {
        $risks = [];
        
        if ($context['team_size'] < 3) {
            $risks[] = [
                'risk' => 'Insufficient team size',
                'severity' => 'High',
                'mitigation' => 'Consider hiring additional team members or contractors',
            ];
        }
        
        if ($context['progress'] < 30 && $context['existing_tasks'] > 20) {
            $risks[] = [
                'risk' => 'Low progress with many tasks',
                'severity' => 'Medium',
                'mitigation' => 'Review task priorities and consider reallocation',
            ];
        }
        
        if (!$context['deadline']) {
            $risks[] = [
                'risk' => 'No defined deadline',
                'severity' => 'Low',
                'mitigation' => 'Set clear project milestones and deadlines',
            ];
        }
        
        return $risks;
    }

    /**
     * Generate recommendations
     */
    protected function generateRecommendations(array $context): array
    {
        $recommendations = [];
        
        if ($context['priority'] === 'high') {
            $recommendations[] = 'Consider allocating additional resources to meet high priority demands';
        }
        
        if ($context['existing_tasks'] < 10) {
            $recommendations[] = 'Break down project into more granular tasks for better tracking';
        }
        
        if ($context['team_size'] > 0) {
            $recommendations[] = 'Implement daily standups to improve team coordination';
        }
        
        $recommendations[] = 'Use AI-powered task prioritization for optimal workflow';
        $recommendations[] = 'Set up automated testing early in the development cycle';
        
        return $recommendations;
    }

    /**
     * Estimate project duration in weeks
     */
    protected function estimateDuration(array $context): int
    {
        $baseWeeks = 12;
        
        // Adjust based on existing tasks
        if ($context['existing_tasks'] > 50) {
            $baseWeeks += 4;
        } elseif ($context['existing_tasks'] > 30) {
            $baseWeeks += 2;
        }
        
        // Adjust based on team size
        if ($context['team_size'] < 3) {
            $baseWeeks += 3;
        } elseif ($context['team_size'] > 7) {
            $baseWeeks -= 2;
        }
        
        // Adjust based on priority
        if ($context['priority'] === 'high') {
            $baseWeeks -= 2;
        }
        
        return max(8, $baseWeeks); // Minimum 8 weeks
    }



    /**
     * Assess project complexity
     */
    protected function assessComplexity(array $context): string
    {
        $complexityScore = 0;
        
        if ($context['existing_tasks'] > 50) $complexityScore += 3;
        elseif ($context['existing_tasks'] > 30) $complexityScore += 2;
        elseif ($context['existing_tasks'] > 10) $complexityScore += 1;
        
        if ($context['team_size'] > 10) $complexityScore += 2;
        elseif ($context['team_size'] > 5) $complexityScore += 1;
        
        if ($complexityScore >= 4) return 'High';
        if ($complexityScore >= 2) return 'Medium';
        return 'Low';
    }

    /**
     * Analyze task for AI insights
     */
    public function analyzeTask(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'decision_type' => 'nullable|string'
        ]);

        try {
            $task = Task::with(['project', 'assignedUsers'])->findOrFail($request->task_id);
            
            // If decision type is specific (e.g. risk_assessment), use the Engine
            if ($request->filled('decision_type')) {
                $decision = $this->decisionEngine->analyzeTask($task, $request->decision_type);
                
                if ($decision) {
                    return response()->json([
                        'success' => true,
                        'analysis' => [
                            'recommendation' => $decision->recommendation,
                            'reasoning' => $decision->reasoning,
                            'confidence' => $decision->confidence_score,
                            'alternatives' => $decision->alternatives
                        ]
                    ]);
                }
                
                return response()->json(['success' => false, 'message' => 'No decision generated'], 404);
            }

            // Fallback for generic 'analyze' button (Legacy/Direct Gateway usage)
            // Use system prompt for task analysis via Gateway directly if no type specified
            $assignedNames = $task->assignedUsers->isNotEmpty() 
                ? $task->assignedUsers->pluck('name')->implode(', ') 
                : 'Unassigned';

            $aiAnalysis = $this->aiGateway->suggest('ai_feature_task_analysis', [
                'task_title' => $task->title,
                'task_description' => $task->description ?? 'No description',
                'priority' => $task->priority ?? 'medium',
                'assigned_to' => $assignedNames,
                'due_date' => $task->due_date ?? 'Not set',
            ]);

            if ($aiAnalysis) {
                $analysis = $aiAnalysis;
            } else {
                // Fallback
                $analysis = [
                    'task' => $task->title,
                    'estimated_effort' => $this->estimateEffort($task),
                    'complexity' => $this->analyzeComplexity($task),
                    'dependencies' => $this->findDependencies($task),
                    'recommendations' => $this->generateTaskRecommendations($task),
                ];
            }
            
            return response()->json([
                'success' => true,
                'analysis' => $analysis,
            ]);
            
        } catch (\Exception $e) {
            Log::error("Analysis failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Analysis failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Estimate task effort
     */
    protected function estimateEffort(Task $task): string
    {
        // Simple heuristic based on task attributes
        $titleLength = strlen($task->title);
        $descLength = strlen($task->description ?? '');
        
        if ($titleLength > 100 || $descLength > 500) {
            return '8-16 hours';
        } elseif ($titleLength > 50 || $descLength > 200) {
            return '4-8 hours';
        } else {
            return '2-4 hours';
        }
    }

    /**
     * Analyze task complexity
     */
    protected function analyzeComplexity(Task $task): string
    {
        // Simplified complexity analysis
        if ($task->priority === 'high') {
            return 'Medium-High';
        }
        return 'Low-Medium';
    }

    /**
     * Find task dependencies
     */
    protected function findDependencies(Task $task): array
    {
        // Placeholder - in production would analyze actual task relationships
        return [
            'blocking' => [],
            'blocked_by' => [],
        ];
    }

    /**
     * Generate task-specific recommendations
     */
    protected function generateTaskRecommendations(Task $task): array
    {
        $recommendations = [];
        
        if (!$task->assigned_to) {
            $recommendations[] = 'Assign this task to a team member';
        }
        
        if (!$task->due_date) {
            $recommendations[] = 'Set a due date for better time management';
        }
        
        if ($task->priority === 'high') {
            $recommendations[] = 'Consider breaking down this high-priority task';
        }
        
        return $recommendations;
    }

    /**
     * Create feasibility study
     */
    public function createStudy(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'study_type' => 'required|in:feasibility,technical,risk',
        ]);

        try {
            $project = Project::findOrFail($request->project_id);
            
            $studyAnalysis = $this->generateStudy($project, $request->study_type);
            
            $study = [
                'type' => $request->study_type,
                'project' => $project->title,
                'analysis' => $studyAnalysis,
                'generated_at' => now()->toIso8601String(),
            ];

            // STORE RESULT IN DATABASE
            \App\Models\AI\AIGeneratedContent::create([
                'project_id' => $project->id,
                'user_id' => auth()->id(),
                'feature_type' => $request->study_type . '_study',
                'prompt_name' => $request->study_type === 'feasibility' ? 'ai_feature_feasibility_study' : ($request->study_type . '_study'),
                'content' => $study,
                'metrics' => ['generated_at' => now()],
            ]);
            
            return response()->json([
                'success' => true,
                'study' => $study,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Study generation failed',
            ], 500);
        }
    }

    /**
     * Generate study content using AI
     */
    protected function generateStudy(Project $project, string $type): array
    {
        // Use the same prompt for all study types, changing the context variable
        $promptName = 'ai_feature_feasibility_study';
        
        if ($promptName) {
            $aiStudy = $this->aiGateway->suggest($promptName, [
                'study_type' => ucfirst($type) . ' Study', // Pass the dynamic type (Feasibility/Technical/Risk)
                'project_title' => $project->title,
                'project_description' => $project->description ?? 'No description',
                'budget' => $project->budget ?? 'Not specified',
                'timeline' => $project->end_date ?? 'Not specified',
                'team_size' => $project->members()->count(),
                'progress' => $project->progress ?? 0,
                'existing_tasks' => $project->tasks()->count(),
            ]);

            if ($aiStudy && isset($aiStudy['analysis'])) {
                return $aiStudy['analysis'];
            }
        }

        throw new \Exception('Failed to generate study. AI service returned no valid data.');
    }

    /**
     * Generate feasibility study
     */
    protected function generateFeasibilityStudy(Project $project): array
    {
        return [
            'executive_summary' => 'Project appears technically and financially feasible',
            'technical_feasibility' => 'High - existing technology stack is suitable',
            'financial_feasibility' => 'Medium - budget allocation needs review',
            'operational_feasibility' => 'High - team has necessary skillset',
            'schedule_feasibility' => 'Medium - timeline is ambitious but achievable',
            'conclusion' => 'Project is recommended to proceed with contingency planning',
        ];
    }

    /**
     * Generate technical study
     */
    protected function generateTechnicalStudy(Project $project): array
    {
        return [
            'technology_stack' => ['Laravel', 'PostgreSQL', 'Redis', 'Vue.js'],
            'infrastructure_requirements' => 'Cloud-based with auto-scaling capability',
            'security_considerations' => 'Implement OAuth2, data encryption, and regular security audits',
            'scalability_assessment' => 'Architecture supports horizontal scaling',
            'integration_points' => 'External APIs, payment gateways, notification services',
        ];
    }

    /**
     * Generate risk study
     */
    protected function generateRiskStudy(Project $project): array
    {
        return [
            'identified_risks' => [
                ['risk' => 'Scope creep', 'probability' => 'High', 'impact' => 'High'],
                ['risk' => 'Resource constraints', 'probability' => 'Medium', 'impact' => 'Medium'],
                ['risk' => 'Technical debt', 'probability' => 'Medium', 'impact' => 'Low'],
            ],
            'mitigation_strategies' => [
                'Implement strict change management process',
                'Regular resource capacity planning',
                'Code review and refactoring cycles',
            ],
            'contingency_plans' => 'Buffer time allocated, backup resources identified',
        ];
    }

    /**
     * Breakdown project into detailed tasks
     */
    public function breakdownProject(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'granularity' => 'nullable|in:high,medium,low',
        ]);

        try {
            $project = Project::with(['tasks'])->findOrFail($request->project_id);
            $granularity = $request->granularity ?? 'medium';
            
            // Generate task breakdown
            $breakdown = $this->generateTaskBreakdown($project, $granularity);

            // STORE RESULT IN DATABASE
            \App\Models\AI\AIGeneratedContent::create([
                'project_id' => $project->id,
                'user_id' => auth()->id(),
                'feature_type' => 'project_breakdown',
                'prompt_name' => 'ai_feature_project_breakdown',
                'content' => $breakdown,
                'metrics' => [
                    'granularity' => $granularity,
                    'generated_at' => now()
                ],
            ]);
            
            // Log activity
            activity('ai')
                ->causedBy(auth()->user())
                ->performedOn($project)
                ->withProperties(['feature' => 'project_breakdown', 'granularity' => $granularity])
                ->log('generated_project_breakdown');
            
            return response()->json([
                'success' => true,
                'breakdown' => $breakdown,
                'project' => $project->title,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Project breakdown failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to breakdown project',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate detailed task breakdown
     */
    protected function generateTaskBreakdown(Project $project, string $granularity): array
    {
        // Use system prompt for project breakdown
        $aiBreakdown = $this->aiGateway->suggest('ai_feature_project_breakdown', [
            'project_title' => $project->title,
            'project_description' => $project->description ?? 'No description',
            'granularity' => $granularity,
            'deadline' => $project->end_date ?? 'Not specified',
        ]);
        
        if ($aiBreakdown && $this->validateBreakdownStructure($aiBreakdown)) {
            return $aiBreakdown;
        }

        throw new \Exception('Failed to generate project breakdown. AI service returned no valid data.');
    }

    /**
     * Get task templates based on granularity
     */
    protected function getTaskTemplates(string $granularity): array
    {
        $templates = [
            'Planning' => [
                'Define project scope and objectives',
                'Create detailed requirements document',
                'Establish project timeline and milestones',
                'Identify key stakeholders',
            ],
            'Design' => [
                'Create system architecture diagram',
                'Design database schema',
                'Create UI/UX mockups',
                'Define API endpoints',
            ],
            'Development' => [
                'Setup development environment',
                'Implement backend API',
                'Implement frontend UI',
                'Database migration scripts',
                'Integration with third-party services',
            ],
            'Testing' => [
                'Write unit tests',
                'Perform integration testing',
                'Conduct UAT',
                'Performance testing',
            ],
            'Deployment' => [
                'Setup production environment',
                'Deploy application',
                'Configure monitoring',
                'Create deployment documentation',
            ],
        ];
        
        if ($granularity === 'high') {
            // Add more detailed sub-tasks
            $templates['Development'][] = 'Code review process';
            $templates['Development'][] = 'Refactoring and optimization';
            $templates['Testing'][] = 'Security testing';
            $templates['Testing'][] = 'Load testing';
        }
        
        return $templates;
    }

    /**
     * Estimate category duration
     */
    protected function estimateCategoryDuration(array $tasks): string
    {
        $taskCount = count($tasks);
        $weeks = ceil($taskCount / 3); // Rough estimate
        
        return $weeks . ' week' . ($weeks > 1 ? 's' : '');
    }

    /**
     * Determine category priority
     */
    protected function determineCategoryPriority(string $category): string
    {
        return match ($category) {
            'Planning' => 'Critical',
            'Design' => 'High',
            'Development' => 'High',
            'Testing' => 'Medium',
            'Deployment' => 'High',
            default => 'Medium',
        };
    }

    /**
     * Count total tasks
     */
    protected function countTotalTasks(array $breakdown): int
    {
        $total = 0;
        foreach ($breakdown as $category) {
            if (isset($category['tasks']) && is_array($category['tasks'])) {
                $total += count($category['tasks']);
            }
        }
        return $total;
    }

    /**
     * Validate plan structure
     */
    protected function validatePlanStructure(array $plan): bool
    {
        return isset($plan['overview'], $plan['phases']) 
            && is_array($plan['phases'])
            && isset($plan['overview']['title'], $plan['overview']['summary']);
    }

    /**
     * Validate breakdown structure
     */
    protected function validateBreakdownStructure(array $breakdown): bool
    {
        return isset($breakdown['categories'], $breakdown['total_estimated_tasks'])
            && is_array($breakdown['categories']);
    }

    /**
     * Get AI generation history for a project
     */
    public function getHistory(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        
        $history = \App\Models\AI\AIGeneratedContent::where('project_id', $project->id)
            ->when($request->feature_type, function($query, $type) {
                return $query->where('feature_type', $type);
            })
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'history' => $history
        ]);
    }
}
