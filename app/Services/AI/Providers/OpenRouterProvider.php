<?php

namespace App\Services\AI\Providers;

use App\Contracts\AIProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * OpenRouter AI Provider
 * 
 * Uses OpenRouter API to access multiple AI models
 * Documentation: https://openrouter.ai/docs
 */
class OpenRouterProvider implements AIProvider
{
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://openrouter.ai/api/v1';
    private string $siteUrl;
    private string $appName;
    private $promptHelper;

    public function __construct(
        string $apiKey, 
        string $model = 'openai/gpt-4',
        string $siteUrl = 'http://localhost',
        string $appName = 'Project Management AI'
    ) {
        $this->apiKey = $apiKey;
        $this->model = $model;
        $this->siteUrl = $siteUrl;
        $this->appName = $appName;
        $this->promptHelper = app(\App\Services\AI\AIPromptHelper::class);
        $this->siteUrl = $siteUrl;
        $this->appName = $appName;
    }

    /**
     * Get AI suggestion based on context
     */
    public function getSuggestion(array $context): ?array
    {
        $type = $context['type'] ?? 'general';
        $data = $context['context'] ?? [];

        $systemPrompt = $this->getSystemPrompt($type, $data);
        $userPrompt = $this->constructUserPrompt($type, $data);

        try {
            // Increase PHP execution time for slow AI responses
            set_time_limit(180);
            
            $response = Http::withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'HTTP-Referer' => $this->siteUrl,
                    'X-Title' => $this->appName,
                ])
                ->timeout(180)
                ->post("{$this->baseUrl}/chat/completions", [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'temperature' => 0.7,
                    'response_format' => ['type' => 'json_object'],
                ]);

            if ($response->failed()) {
                Log::error('OpenRouter API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $content = $response->json('choices.0.message.content');
            
            if (!$content) {
                return null;
            }

            return json_decode($content, true);

        } catch (\Exception $e) {
            Log::error('OpenRouter Request Failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Check if AI service is available
     */
    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Record user feedback
     */
    public function recordFeedback(string $suggestionId, bool $accepted, array $metadata = []): void
    {
        Log::info('AI Feedback', [
            'provider' => 'openrouter',
            'model' => $this->model,
            'id' => $suggestionId,
            'accepted' => $accepted,
            'metadata' => $metadata
        ]);
    }

    /**
     * Get AI model information
     */
    public function getModelInfo(): array
    {
        return [
            'provider' => 'OpenRouter',
            'model' => $this->model,
            'capabilities' => ['text', 'code', 'json', 'multi-model'],
        ];
    }

    /**
     * Get system prompt based on type
     */
    private function getSystemPrompt(string $type, array $data = []): string
    {
        // 1. Try to get from Database first
        
        // Case A: The type IS the prompt name (e.g. passed from AIGateway)
        if ($this->promptHelper->exists($type)) {
            $compiledPrompt = $this->promptHelper->getCompiledPrompt($type, $data);
            Log::info("🤖 Using DB Prompt directly: {$type}");
            return $compiledPrompt;
        }

        // Case B: Map short feature codes to prompt names
        $promptMap = [
            'development_plan' => 'ai_feature_development_plan',
            'project_breakdown' => 'ai_feature_project_breakdown',
            'task_analysis' => 'ai_feature_task_analysis',
            'feasibility_study' => 'ai_feature_feasibility_study',
            'technical_study' => 'ai_feature_technical_study',
            'risk_study' => 'ai_feature_risk_study',
        ];

        $promptName = $promptMap[$type] ?? null;

        if ($promptName && $this->promptHelper->exists($promptName)) {
            // Use getCompiledPrompt to replace variables with data
            $compiledPrompt = $this->promptHelper->getCompiledPrompt($promptName, $data);
            Log::info("🤖 Using DB Prompt: {$promptName}");
            return $compiledPrompt;
        }

        // 2. Prompt not found
        Log::error("❌ System prompt not found for type: {$type}");
        throw new \Exception("System prompt not found for type: {$type}. Please ensure the prompt is active in the administration panel.");
    }

    /**
     * Construct user prompt from data
     */
    private function constructUserPrompt(string $type, array $data): string
    {
        // Check if we are using a DB prompt (Direct Name)
        if ($this->promptHelper->exists($type)) {
            return "Please proceed with the analysis based on the provided project details above.";
        }

        // Check if we are using a DB prompt (Mapped Name)
        $promptMap = [
            'development_plan' => 'ai_feature_development_plan',
            'project_breakdown' => 'ai_feature_project_breakdown',
            'task_analysis' => 'ai_feature_task_analysis',
            'feasibility_study' => 'ai_feature_feasibility_study',
            'technical_study' => 'ai_feature_technical_study',
            'risk_study' => 'ai_feature_risk_study',
        ];
        
        $promptName = $promptMap[$type] ?? null;
        
        if ($promptName && $this->promptHelper->exists($promptName)) {
            // Context is already embedded in the System Prompt via template variables.
            // Just provide a short confirmation or additional context if needed.
            return "Please proceed with the analysis based on the provided project details above.";
        }

        // Legacy Fallback
        return "Analyze the following project management context and provide your response as valid JSON:\n\n" . 
               json_encode($data, JSON_PRETTY_PRINT);
    }
}
