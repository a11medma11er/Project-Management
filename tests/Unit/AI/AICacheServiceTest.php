<?php

namespace Tests\Unit\AI;

use Tests\TestCase;
use App\Services\AI\AICacheService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AICacheServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $cacheService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheService = new AICacheService();
        Cache::flush(); // Clear cache before each test
    }

    /** @test */
    public function it_can_cache_decisions()
    {
        $key = 'test_decision';
        $data = ['confidence' => 0.9, 'type' => 'priority'];

        $this->cacheService->cacheDecision($key, $data);
        
        $cached = $this->cacheService->getCachedDecision($key);
        
        $this->assertEquals($data, $cached);
    }

    /** @test */
    public function it_can_cache_analysis_results()
    {
        $entityId = 123;
        $type = 'task_analysis';
        $result = ['score' => 85, 'recommendations' => ['Update priority']];

        $this->cacheService->cacheAnalysis($entityId, $type, $result);
        
        $cached = $this->cacheService->getCachedAnalysis($entityId, $type);
        
        $this->assertEquals($result, $cached);
    }

    /** @test */
    public function it_returns_null_for_missing_cache()
    {
        $cached = $this->cacheService->getCachedDecision('non_existent_key');
        
        $this->assertNull($cached);
    }

    /** @test */
    public function it_uses_remember_with_callback()
    {
        $key = 'test_remember';
        $callbackExecuted = false;

        $result = $this->cacheService->remember($key, function() use (&$callbackExecuted) {
            $callbackExecuted = true;
            return 'computed_value';
        });

        $this->assertTrue($callbackExecuted);
        $this->assertEquals('computed_value', $result);

        // Second call should use cache
        $callbackExecuted = false;
        $result = $this->cacheService->remember($key, function() use (&$callbackExecuted) {
            $callbackExecuted = true;
            return 'new_value';
        });

        $this->assertFalse($callbackExecuted); // Callback not executed (cache hit)
        $this->assertEquals('computed_value', $result); // Still returns cached value
    }
}
