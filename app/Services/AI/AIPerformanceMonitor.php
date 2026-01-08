<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AIPerformanceMonitor
{
    protected $startTime;
    protected $metrics = [];

    /**
     * Start performance monitoring
     */
    public function start(string $operation): void
    {
        $this->startTime = microtime(true);
        $this->metrics[$operation] = [
            'start' => $this->startTime,
            'memory_start' => memory_get_usage(true),
        ];
    }

    /**
     * End performance monitoring
     */
    public function end(string $operation): array
    {
        if (!isset($this->metrics[$operation])) {
            return [];
        }

        $endTime = microtime(true);
        $duration = ($endTime - $this->metrics[$operation]['start']) * 1000; // ms
        
        $memoryEnd = memory_get_usage(true);
        $memoryUsed = $memoryEnd - $this->metrics[$operation]['memory_start'];

        $result = [
            'operation' => $operation,
            'duration_ms' => round($duration, 2),
            'memory_used_mb' => round($memoryUsed / 1024 / 1024, 2),
            'timestamp' => now()->toIso8601String(),
        ];

        // Log if slow
        if ($duration > 1000) {
            Log::warning('Slow AI operation detected', $result);
        }

        // Store metric
        $this->storeMetric($result);

        return $result;
    }

    /**
     * Store performance metric
     */
    protected function storeMetric(array $metric): void
    {
        $key = 'ai_performance_' . date('Y-m-d-H');
        
        $metrics = Cache::get($key, []);
        $metrics[] = $metric;
        
        // Keep only last 100 metrics per hour
        if (count($metrics) > 100) {
            $metrics = array_slice($metrics, -100);
        }
        
        Cache::put($key, $metrics, now()->addHours(24));
    }

    /**
     * Get performance statistics
     */
    public function getStats(int $hours = 1): array
    {
        $allMetrics = [];
        
        for ($i = 0; $i < $hours; $i++) {
            $hour = now()->subHours($i);
            $key = 'ai_performance_' . $hour->format('Y-m-d-H');
            
            $metrics = Cache::get($key, []);
            $allMetrics = array_merge($allMetrics, $metrics);
        }

        if (empty($allMetrics)) {
            return [
                'count' => 0,
                'avg_duration' => 0,
                'max_duration' => 0,
                'avg_memory' => 0,
            ];
        }

        $durations = array_column($allMetrics, 'duration_ms');
        $memories = array_column($allMetrics, 'memory_used_mb');

        return [
            'count' => count($allMetrics),
            'avg_duration' => round(array_sum($durations) / count($durations), 2),
            'max_duration' => max($durations),
            'min_duration' => min($durations),
            'avg_memory' => round(array_sum($memories) / count($memories), 2),
            'max_memory' => max($memories),
        ];
    }

    /**
     * Get slow operations
     */
    public function getSlowOperations(int $threshold = 1000, int $hours = 1): array
    {
        $allMetrics = [];
        
        for ($i = 0; $i < $hours; $i++) {
            $hour = now()->subHours($i);
            $key = 'ai_performance_' . $hour->format('Y-m-d-H');
            
            $metrics = Cache::get($key, []);
            $allMetrics = array_merge($allMetrics, $metrics);
        }

        return array_filter($allMetrics, function ($metric) use ($threshold) {
            return $metric['duration_ms'] > $threshold;
        });
    }

    /**
     * Monitor system resources
     */
    public function getSystemMetrics(): array
    {
        return [
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'memory_peak' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            'memory_limit' => ini_get('memory_limit'),
            'cpu_load' => sys_getloadavg(),
        ];
    }

    /**
     * Clear old metrics
     */
    public function clearOldMetrics(int $hoursToKeep = 24): void
    {
        $cutoff = now()->subHours($hoursToKeep);
        
        // Clear metrics older than cutoff
        for ($i = $hoursToKeep; $i < $hoursToKeep + 24; $i++) {
            $hour = now()->subHours($i);
            $key = 'ai_performance_' . $hour->format('Y-m-d-H');
            Cache::forget($key);
        }
        
        Log::info('Old performance metrics cleared', ['hours_kept' => $hoursToKeep]);
    }
}
