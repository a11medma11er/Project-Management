<?php

namespace App\Console\Commands;

use App\Services\AI\AIAutomationService;
use Illuminate\Console\Command;

class RunAIAutomation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:automate {--type=all : Type of automation to run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run AI automation workflows';

    protected $automationService;

    /**
     * Create a new command instance.
     */
    public function __construct(AIAutomationService $automationService)
    {
        parent::__construct();
        $this->automationService = $automationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');

        $this->info('Starting AI Automation...');
        $this->info("Type: {$type}");

        try {
            $results = $this->automationService->runAutomatedAnalysis();

            $this->displayResults($results);

            $this->info('✓ AI Automation completed successfully');
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('✗ AI Automation failed');
            $this->error($e->getMessage());
            
            return Command::FAILURE;
        }
    }

    /**
     * Display automation results
     */
    protected function displayResults(array $results): void
    {
        $this->newLine();
        $this->line('=== Results ===');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Tasks Analyzed', $results['tasks_analyzed']],
                ['Decisions Created', $results['decisions_created']],
                ['Auto-Executed', $results['auto_executed']],
                ['Errors', count($results['errors'])],
            ]
        );

        if (!empty($results['errors'])) {
            $this->newLine();
            $this->error('Errors encountered:');
            foreach ($results['errors'] as $error) {
                $this->line("  - {$error}");
            }
        }
    }
}
