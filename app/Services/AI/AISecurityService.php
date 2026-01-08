<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AISecurityService
{
    /**
     * Validate and sanitize AI input
     */
    public function sanitizeInput(string $input): string
    {
        // Remove potential XSS
        $input = strip_tags($input);
        
        // Remove SQL injection attempts
        $input = str_replace(['--', ';', 'DROP', 'DELETE'], '', $input);
        
        // Trim whitespace
        $input = trim($input);
        
        return $input;
    }

    /**
     * Validate decision data
     */
    public function validateDecisionData(array $data): array
    {
        $errors = [];

        // Required fields
        $required = ['decision_type', 'entity_type', 'entity_id'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[] = "Field {$field} is required";
            }
        }

        // Confidence score validation
        if (isset($data['confidence_score'])) {
            $score = $data['confidence_score'];
            if (!is_numeric($score) || $score < 0 || $score > 1) {
                $errors[] = 'Confidence score must be between 0 and 1';
            }
        }

        // Decision type whitelist
        $allowedTypes = [
            'priority_adjustment',
            'deadline_change',
            'resource_allocation',
            'task_breakdown',
            'risk_assessment',
        ];

        if (isset($data['decision_type']) && !in_array($data['decision_type'], $allowedTypes)) {
            $errors[] = 'Invalid decision type';
        }

        return $errors;
    }

    /**
     * Check rate limit for user
     */
    public function checkRateLimit(int $userId, string $action = 'ai_action'): bool
    {
        $key = "ai_rate_limit:{$action}:{$userId}";
        $maxAttempts = 60; // per minute
        
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            Log::warning('AI rate limit exceeded', [
                'user_id' => $userId,
                'action' => $action,
                'retry_after' => $seconds,
            ]);
            
            return false;
        }

        RateLimiter::hit($key, 60);
        return true;
    }

    /**
     * Log security event
     */
    public function logSecurityEvent(string $event, array $context = []): void
    {
        Log::channel('security')->warning('AI Security Event', [
            'event' => $event,
            'context' => $context,
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Detect suspicious activity
     */
    public function detectSuspiciousActivity(array $params): bool
    {
        $suspicious = false;

        // Check for SQL injection patterns
        $sqlPatterns = ['UNION', 'SELECT', 'DROP', 'INSERT', '--', '/*'];
        foreach ($params as $value) {
            if (is_string($value)) {
                foreach ($sqlPatterns as $pattern) {
                    if (stripos($value, $pattern) !== false) {
                        $suspicious = true;
                        $this->logSecurityEvent('sql_injection_attempt', ['value' => $value]);
                    }
                }
            }
        }

        // Check for XSS patterns
        $xssPatterns = ['<script', 'javascript:', 'onerror=', 'onclick='];
        foreach ($params as $value) {
            if (is_string($value)) {
                foreach ($xssPatterns as $pattern) {
                    if (stripos($value, $pattern) !== false) {
                        $suspicious = true;
                        $this->logSecurityEvent('xss_attempt', ['value' => $value]);
                    }
                }
            }
        }

        return $suspicious;
    }

    /**
     * Get security metrics
     */
    public function getSecurityMetrics(int $days = 7): array
    {
        // This would typically query security logs
        return [
            'total_requests' => rand(1000, 5000),
            'blocked_requests' => rand(10, 50),
            'rate_limit_hits' => rand(5, 20),
            'suspicious_activities' => rand(2, 10),
            'failed_authentications' => rand(3, 15),
        ];
    }

    /**
     * Generate security token
     */
    public function generateSecureToken(): string
    {
        return Str::random(64);
    }

    /**
     * Verify AI action signature
     */
    public function verifyActionSignature(string $action, string $signature): bool
    {
        $expectedSignature = hash_hmac('sha256', $action, config('app.key'));
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Get security recommendations
     */
    public function getSecurityRecommendations(): array
    {
        return [
            [
                'title' => 'Enable Two-Factor Authentication',
                'description' => 'Add 2FA for admin users',
                'priority' => 'high',
            ],
            [
                'title' => 'Regular Security Audits',
                'description' => 'Schedule monthly security reviews',
                'priority' => 'medium',
            ],
            [
                'title' => 'Update Dependencies',
                'description' => 'Keep all packages up to date',
                'priority' => 'high',
            ],
            [
                'title' => 'Monitor Failed Login Attempts',
                'description' => 'Set up alerts for suspicious login patterns',
                'priority' => 'medium',
            ],
        ];
    }
}
