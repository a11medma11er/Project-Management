<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AIIntegrationService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('ai');
    }

    /**
     * Call external AI provider
     */
    public function callExternalAI(string $provider, string $prompt, array $context = []): array
    {
        $method = "call" . ucfirst($provider);
        
        if (!method_exists($this, $method)) {
            throw new \InvalidArgumentException("Unsupported AI provider: {$provider}");
        }

        try {
            $response = $this->$method($prompt, $context);
            
            Log::info('External AI call successful', [
                'provider' => $provider,
                'prompt_length' => strlen($prompt),
            ]);

            return $response;

        } catch (\Exception $e) {
            Log::error('External AI call failed', [
                'provider' => $provider,
                'error' => $e->getMessage(),
            ]);

            return $this->getFallbackResponse($prompt);
        }
    }

    /**
     * OpenAI integration
     */
    protected function callOpenai(string $prompt, array $context): array
    {
        $apiKey = config('ai.openai.api_key');
        
        if (!$apiKey) {
            throw new \Exception('OpenAI API key not configured');
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
            'model' => config('ai.openai.model', 'gpt-4'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful project management AI assistant.',
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'temperature' => 0.7,
            'max_tokens' => 1000,
        ]);

        if ($response->failed()) {
            throw new \Exception('OpenAI API request failed: ' . $response->body());
        }

        $data = $response->json();

        return [
            'provider' => 'openai',
            'response' => $data['choices'][0]['message']['content'] ?? '',
            'model' => $data['model'] ?? '',
            'usage' => $data['usage'] ?? [],
        ];
    }

    /**
     * Claude (Anthropic) integration
     */
    protected function callClaude(string $prompt, array $context): array
    {
        $apiKey = config('ai.claude.api_key');
        
        if (!$apiKey) {
            throw new \Exception('Claude API key not configured');
        }

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
            'model' => config('ai.claude.model', 'claude-3-sonnet-20240229'),
            'max_tokens' => 1024,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
        ]);

        if ($response->failed()) {
            throw new \Exception('Claude API request failed: ' . $response->body());
        }

        $data = $response->json();

        return [
            'provider' => 'claude',
            'response' => $data['content'][0]['text'] ?? '',
            'model' => $data['model'] ?? '',
            'usage' => $data['usage'] ?? [],
        ];
    }

    /**
     * Local AI fallback (rule-based)
     */
    protected function callLocal(string $prompt, array $context): array
    {
        // Simple rule-based analysis as fallback
        $analysis = $this->analyzeLocally($prompt, $context);

        return [
            'provider' => 'local',
            'response' => $analysis,
            'model' => 'rule-based',
        ];
    }

    /**
     * Simple local analysis
     */
    protected function analyzeLocally(string $prompt, array $context): string
    {
        // Basic keyword analysis
        $keywords = ['urgent', 'high priority', 'overdue', 'deadline'];
        $hasUrgency = false;

        foreach ($keywords as $keyword) {
            if (stripos($prompt, $keyword) !== false) {
                $hasUrgency = true;
                break;
            }
        }

        if ($hasUrgency) {
            return "This task appears to have high urgency. Consider prioritizing it and assigning resources promptly.";
        }

        return "Based on the information provided, this task requires standard attention and planning.";
    }

    /**
     * Get fallback response
     */
    protected function getFallbackResponse(string $prompt): array
    {
        return [
            'provider' => 'fallback',
            'response' => 'AI analysis unavailable. Please review manually.',
            'model' => 'none',
        ];
    }

    /**
     * Rate limiting check
     */
    public function checkRateLimit(string $provider): bool
    {
        $key = "ai_rate_limit_{$provider}";
        $limit = config("ai.{$provider}.rate_limit", 60);
        
        $current = Cache::get($key, 0);
        
        if ($current >= $limit) {
            Log::warning('AI rate limit exceeded', [
                'provider' => $provider,
                'limit' => $limit,
            ]);
            return false;
        }

        Cache::put($key, $current + 1, now()->addMinute());
        return true;
    }

    /**
     * Send webhook notification
     */
    public function sendWebhook(string $event, array $data): bool
    {
        $webhookUrl = config('ai.webhook_url');
        
        if (!$webhookUrl) {
            return false;
        }

        try {
            $response = Http::timeout(10)->post($webhookUrl, [
                'event' => $event,
                'timestamp' => now()->toIso8601String(),
                'data' => $data,
            ]);

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Webhook delivery failed', [
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send Slack notification
     */
    public function sendSlackNotification(string $message, array $options = []): bool
    {
        $webhookUrl = config('ai.slack.webhook_url');
        
        if (!$webhookUrl) {
            return false;
        }

        try {
            $payload = [
                'text' => $message,
                'username' => $options['username'] ?? 'AI Assistant',
                'icon_emoji' => $options['icon'] ?? ':robot_face:',
            ];

            if (isset($options['channel'])) {
                $payload['channel'] = $options['channel'];
            }

            $response = Http::timeout(10)->post($webhookUrl, $payload);

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Slack notification failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get integration health status
     */
    public function getIntegrationHealth(): array
    {
        $providers = ['openai', 'claude', 'local'];
        $health = [];

        foreach ($providers as $provider) {
            $health[$provider] = [
                'configured' => !empty(config("ai.{$provider}.api_key")),
                'rate_limited' => !$this->checkRateLimit($provider),
                'last_call' => Cache::get("ai_last_call_{$provider}"),
            ];
        }

        return $health;
    }
}
