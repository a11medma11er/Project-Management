<?php

namespace App\Console\Commands;

use App\Services\AI\AIDecisionEngine;
use App\Services\AI\AIDataAggregator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AnalyzeTasksAndProjects extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ai:analyze
                            {--type=all : Type of analysis (tasks|projects|all)}
                            {--limit=20 : Maximum items to analyze}
                            {--verbose : Display detailed output}';

    /**
     * The console command description.
     */
    protected $description = 'Analyze tasks and projects using AI Decision Engine';

    protected $decisionEngine;
    protected $dataAggregator;

    /**
     * Create a new command instance.
     */
    public function __construct(
        AIDecisionEngine $decisionEngine,
        AIDataAggregator $dataAggregator
    ) {
        parent::__construct();
        $this->decisionEngine = $decisionEngine;
        $this->dataAggregator = $dataAggregator;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🤖 AI Analysis Started...');
        $this->newLine();

        $type = $this->option('type');
        $limit = (int) $this->option('limit');

        $decisionsCreated = 0;

        try {
            // Analyze tasks
            if ($type === 'all' || $type === 'tasks') {
                $this->info('📋 Analyzing Tasks...');
                $taskDecisions = $this->analyzeTasks($limit);
                $decisionsCreated += $taskDecisions;
                $this->info("✅ Tasks analyzed: {$taskDecisions} decisions created");
                $this->newLine();
            }

            // Analyze projects
            if ($type === 'all' || $type === 'projects') {
                $this->info('📁 Analyzing Projects...');
                $projectDecisions = $this->analyzeProjects($limit);
                $decisionsCreated += $projectDecisions;
                $this->info("✅ Projects analyzed: {$projectDecisions} decisions created");
                $this->newLine();
            }

            // Summary
            $this->displaySummary($decisionsCreated);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Analysis failed: ' . $e->getMessage());
            Log::error('AI Analysis command failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Analyze tasks needing attention
     */
    protected function analyzeTasks(int $limit): int
    {
        $decisionsCreated = 0;

        // Get tasks needing attention
        $tasks = $this->dataAggregator->getTasksNeedingAttention($limit);
        
        if ($this->option('verbose')) {
            $this->line("Found {$tasks->count()} tasks needing attention");
        }

        foreach ($tasks as $task) {
            try {
                $decision = $this->decisionEngine->analyzeTask($task->task_id);
                
                if ($decision) {
                    $decisionsCreated++;
                    
                    if ($this->option('verbose')) {
                        $this->line("  ✓ Task #{$task->task_id}: {$task->title}");
                        $this->line("    Recommendation: {$decision->recommendation}");
                        $this->line("    Confidence: " . round($decision->confidence_score * 100, 2) . "%");
                    }
                }
            } catch (\Exception $e) {
                if ($this->option('verbose')) {
                    $this->warn("  ✗ Failed to analyze task #{$task->task_id}: " . $e->getMessage());
                }
            }
        }

        return $decisionsCreated;
    }

    /**
     * Analyze projects at risk
     */
    protected function analyzeProjects(int $limit): int
    {
        $decisionsCreated = 0;

        // Get projects at risk
        $projects = $this->dataAggregator->getProjectsAtRisk($limit);
        
        if ($this->option('verbose')) {
            $this->line("Found {$projects->count()} projects at risk");
        }

        foreach ($projects as $project) {
            try {
                $decision = $this->decisionEngine->analyzeProject($project->project_id);
                
                if ($decision) {
                    $decisionsCreated++;
                    
                    if ($this->option('verbose')) {
                        $this->line("  ✓ Project #{$project->project_id}: {$project->title}");
                        $this->line("    Recommendation: {$decision->recommendation}");
                        $this->line("    Confidence: " . round($decision->confidence_score * 100, 2) . "%");
                    }
                }
            } catch (\Exception $e) {
                if ($this->option('verbose')) {
                    $this->warn("  ✗ Failed to analyze project #{$project->project_id}: " . $e->getMessage());
                }
            }
        }

        return $decisionsCreated;
    }

    /**
     * Display summary
     */
    protected function displaySummary(int $decisionsCreated): void
    {
        $this->info('═══════════════════════════════════════');
        $this->info('📊 Analysis Summary');
        $this->info('═══════════════════════════════════════');
        $this->line("Total Decisions Created: {$decisionsCreated}");
        $this->line("Timestamp: " . now()->format('Y-m-d H:i:s'));
        $this->newLine();
        
        if ($decisionsCreated > 0) {
            $this->comment('💡 View pending decisions in the AI Control Panel');
        } else {
            $this->comment('✨ No issues detected - Everything looks good!');
        }
    }
}
