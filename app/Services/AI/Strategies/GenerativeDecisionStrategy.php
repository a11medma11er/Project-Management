<?php

namespace App\Services\AI\Strategies;

use App\Services\AI\AIContextBuilder;
use App\Services\AI\AIGateway;
use Illuminate\Support\Facades\Log;

class GenerativeDecisionStrategy implements DecisionStrategyInterface
{
    protected $contextBuilder;
    protected $aiGateway;
    protected $legacyStrategy;

    public function __construct(
        AIContextBuilder $contextBuilder,
        AIGateway $aiGateway,
        LegacyDecisionStrategy $legacyStrategy
    ) {
        $this->contextBuilder = $contextBuilder;
        $this->aiGateway = $aiGateway;
        $this->legacyStrategy = $legacyStrategy;
    }

    /**
     * Analyze task using Generative AI
     */
    /**
     * Analyze task using Generative AI
     */
    public function analyzeTask(array $context, ?string $decisionType = null): array
    {
        try {
            // Extract Task ID to build compact context
            $taskId = $context['task_context']['task']['id'] ?? null;
            
            if (!$taskId) {
                return $this->legacyStrategy->analyzeTask($context, $decisionType);
            }

            // 1. Build compact context (JSON payload)
            $compactContext = $this->contextBuilder->buildCompactContext($taskId);

            // 2. Determine Prompt Name based on decision type
            $promptName = match($decisionType) {
                'priority_suggestion' => 'ai_decision_priority_suggestion',
                'assignment_suggestion' => 'ai_decision_assignment_suggestion',
                'deadline_estimation' => 'ai_decision_deadline_estimation',
                'risk_assessment' => 'ai_decision_risk_assessment',
                default => 'ai_feature_task_analysis',
            };

            // 3. Call AI with specific prompt
            $response = $this->aiGateway->suggest($promptName, $compactContext);

            // 4. No fallback if AI fails (Strict Mode)
            if (empty($response)) {
                Log::warning("Generative AI returned empty response for task {$taskId} (Prompt: {$promptName}). Strict mode enabled: Skipping legacy fallback.");
                return [
                    'requires_action' => false,
                    'recommendation' => 'AI Analysis Failed (Empty Response)',
                    'reasoning' => ['Generative AI returned no response', 'Strict mode active'],
                    'confidence' => 0.0,
                    'alternatives' => []
                ];
            }

            // 5. Map response to expected format
            // If AI returned a response, assume action is required (decision should be created)
            // Extract recommendation from various possible response formats
            $recommendation = $response['recommendation'] 
                ?? $response['suggested_action'] 
                ?? $response['analysis'] 
                ?? json_encode($response);
            
            return [
                'requires_action' => true, // AI responded = always create decision
                'recommendation' => is_string($recommendation) ? $recommendation : json_encode($recommendation),
                'reasoning' => is_array($response['reasoning'] ?? null) 
                    ? $response['reasoning'] 
                    : [$response['reasoning'] ?? 'AI generated decision'],
                'confidence' => (float) ($response['confidence'] ?? 0.85),
                'alternatives' => $response['alternatives'] ?? [],
            ];

        } catch (\Exception $e) {
            Log::error("Generative AI strategy failed: " . $e->getMessage());
            // Strict mode: Do not fallback to legacy
            return [
                'requires_action' => false,
                'recommendation' => 'AI Analysis Error',
                'reasoning' => ['Generative AI encountered an error', $e->getMessage()],
                'confidence' => 0.0,
                'alternatives' => []
            ];
        }
    }

    /**
     * Analyze project using Generative AI
     */
    public function analyzeProject(array $context): array
    {
        // For now, we only implement Task Analysis via GenAI.
        // Fallback to legacy for projects until we add 'ai_decision_project_analysis' prompt.
        return $this->legacyStrategy->analyzeProject($context);
    }
}
