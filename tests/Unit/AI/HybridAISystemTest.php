<?php

namespace Tests\Unit\AI;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Services\AI\AIDecisionStrategyFactory;
use App\Services\AI\Strategies\LegacyDecisionStrategy;
use App\Services\AI\Strategies\GenerativeDecisionStrategy;
use App\Services\AI\AISettingsService;
use App\Services\AI\AIGateway;
use App\Services\AI\Strategies\DecisionStrategyInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Illuminate\Support\Facades\Log;

class HybridAISystemTest extends TestCase
{
    // Removed RefreshDatabase to avoid migration errors with SQLite

    protected $settingsService;
    protected $factory;
    protected $dataAggregator;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock Settings Service
        $this->settingsService = Mockery::mock(AISettingsService::class);
        $this->app->instance(AISettingsService::class, $this->settingsService);
        
        // Mock AIDataAggregator to prevent any accidental DB hits from LegacyStrategy constructor?
        // Checking LegacyStrategy dependencies... it likely injects nothing or basic services.
        // But Factory injects SettingsService.
        
        $this->factory = app(AIDecisionStrategyFactory::class);
    }
    
    /** @test */
    public function it_uses_legacy_strategy_by_default_or_when_set_to_local()
    {
        // 1. Mock settings to return 'local'
        $this->settingsService->shouldReceive('get')->with('ai_provider', 'local')->andReturn('local');
        
        $strategy = $this->factory->getStrategy();
        $this->assertInstanceOf(LegacyDecisionStrategy::class, $strategy);
    }
    
    /** @test */
    public function it_switches_to_generative_strategy_when_provider_is_openai()
    {
        // Mock settings to return 'openai'
        $this->settingsService->shouldReceive('get')->with('ai_provider', 'local')->andReturn('openai');
        
        $strategy = $this->factory->getStrategy();
        $this->assertInstanceOf(GenerativeDecisionStrategy::class, $strategy);
    }

    /** @test */
    public function it_falls_back_to_legacy_logic_if_generative_ai_fails()
    {
        // Setup: Use Generative Strategy
        $this->settingsService->shouldReceive('get')->with('ai_provider', 'local')->andReturn('openai');

        // Mock AIGateway to simulate failure (return null)
        $mockGateway = Mockery::mock(AIGateway::class);
        $mockGateway->shouldReceive('suggest')->andReturn(null);
        $this->app->instance(AIGateway::class, $mockGateway);

        // Mock AIContextBuilder to avoid DB hits
        $mockContextBuilder = Mockery::mock(\App\Services\AI\AIContextBuilder::class);
        $mockContextBuilder->shouldReceive('buildDecisionContext')
            ->andReturn([
                'task_context' => [
                    'task' => ['id' => 999, 'status' => 'in_progress'],
                    'timeline' => ['days_overdue' => 5, 'urgency_level' => 'high'],
                    'ai_signals' => ['needs_attention' => true, 'stale_task' => false, 'low_engagement' => false, 'is_blocked' => false, 'is_completed' => false],
                    'engagement' => ['total_hours_logged' => 10], // Needed for compact context
                ],
                'project_context' => null,
                'system_context' => [],
            ]);
        $mockContextBuilder->shouldReceive('buildCompactContext')->andReturn([]); // Called by GenerativeStrategy
        
        $this->app->instance(\App\Services\AI\AIContextBuilder::class, $mockContextBuilder);

        // Fake Task model (just for the ID)
        $task = new Task(['id' => 999]);
        $task->id = 999; 

        // Get the strategy (Generative)
        $strategy = $this->factory->getStrategy();
        $this->assertInstanceOf(GenerativeDecisionStrategy::class, $strategy);

        // Analyze
        $engine = app(\App\Services\AI\AIDecisionEngine::class); // Re-resolve to inject mocks
        
        // We call analyzeTask just to trigger the flow inside Engine -> Strategy
        // But since we are testing the strategy directly or via Engine?
        // Let's test via Strategy to match the previous test intent, or Engine logic.
        // The previous test was calling strategy->analyzeTask($context).
        
        // Let's manually get the context like the Engine would
        $context = $mockContextBuilder->buildDecisionContext($task->id);
        
        // Execute analysis on the Strategy directly to verify fallback
        $analysis = $strategy->analyzeTask($context);

        // Assertions
        // Legacy strategy sees 'needs_attention' => true and 'days_overdue' => 5
        // So it should recommend action.
        $this->assertTrue($analysis['requires_action'], 'Fallback logic should have detected action required');
        $this->assertStringContainsStringIgnoringCase('Overdue', $analysis['recommendation'] ?? $analysis['reasoning'][0], 'Recommendation should detect overdue status (Legacy Logic)');
    }
}
