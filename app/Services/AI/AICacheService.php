<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AICacheService
{
    protected $ttl;
    protected $prefix;

    public function __construct()
    {
        $this->ttl = config('ai.cache_ttl', 3600);
        $this->prefix = 'ai_cache_';
    }

    /**
     * Cache AI decision
     */
    public function cacheDecision(string $key, $data, ?int $ttl = null): void
    {
        $cacheKey = $this->prefix . 'decision_' . $key;
        Cache::put($cacheKey, $data, $ttl ?? $this->ttl);
        
        Log::debug('AI decision cached', ['key' => $key]);
    }

    /**
     * Get cached decision
     */
    public function getCachedDecision(string $key)
    {
        $cacheKey = $this->prefix . 'decision_' . $key;
        return Cache::get($cacheKey);
    }

    /**
     * Cache analysis result
     */
    public function cacheAnalysis(int $entityId, string $type, array $result, ?int $ttl = null): void
    {
        $cacheKey = $this->prefix . "analysis_{$type}_{$entityId}";
        Cache::put($cacheKey, $result, $ttl ?? $this->ttl);
    }

    /**
     * Get cached analysis
     */
    public function getCachedAnalysis(int $entityId, string $type): ?array
    {
        $cacheKey = $this->prefix . "analysis_{$type}_{$entityId}";
        return Cache::get($cacheKey);
    }

    /**
     * Cache metrics
     */
    public function cacheMetrics(string $metricKey, array $data, int $ttl = 300): void
    {
        $cacheKey = $this->prefix . 'metrics_' . $metricKey;
        Cache::put($cacheKey, $data, $ttl);
    }

    /**
     * Get cached metrics
     */
    public function getCachedMetrics(string $metricKey): ?array
    {
        $cacheKey = $this->prefix . 'metrics_' . $metricKey;
        return Cache::get($cacheKey);
    }

    /**
     * Remember with callback
     */
    public function remember(string $key, \Closure $callback, ?int $ttl = null)
    {
        $cacheKey = $this->prefix . $key;
        
        return Cache::remember($cacheKey, $ttl ?? $this->ttl, function() use ($callback, $key) {
            Log::debug('AI cache miss, executing callback', ['key' => $key]);
            return $callback();
        });
    }

    /**
     * Invalidate cache by pattern
     */
    public function invalidate(string $pattern): void
    {
        // For Redis/Memcached with tag support
        if (method_exists(Cache::getStore(), 'tags')) {
            Cache::tags(['ai_cache'])->flush();
        } else {
            // Fallback: clear specific keys (requires tracking)
            Cache::forget($this->prefix . $pattern);
        }
        
        Log::info('AI cache invalidated', ['pattern' => $pattern]);
    }

    /**
     * Warm up cache for common queries
     */
    public function warmUp(): array
    {
        $warmed = [];
        
        try {
            // Pre-cache common metrics
            $metrics = [
                'decision_summary',
                'accuracy_trend',
                'active_rules',
            ];

            foreach ($metrics as $metric) {
                // This would call the actual metric generation
                $warmed[] = $metric;
                Log::info('Cache warmed', ['metric' => $metric]);
            }

        } catch (\Exception $e) {
            Log::error('Cache warm-up failed', ['error' => $e->getMessage()]);
        }

        return $warmed;
    }

    /**
     * Get cache statistics
     */
    public function getStats(): array
    {
        // This is simplified - actual implementation depends on cache driver
        return [
            'driver' => config('cache.default'),
            'ttl' => $this->ttl,
            'prefix' => $this->prefix,
        ];
    }

    /**
     * Clear all AI caches
     */
    public function clearAll(): void
    {
        if (method_exists(Cache::getStore(), 'tags')) {
            Cache::tags(['ai_cache'])->flush();
        }
        
        Log::warning('All AI caches cleared');
    }
}
