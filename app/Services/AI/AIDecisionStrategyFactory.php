<?php

namespace App\Services\AI;

use App\Services\AI\Strategies\DecisionStrategyInterface;
use App\Services\AI\Strategies\LegacyDecisionStrategy;
use App\Services\AI\Strategies\GenerativeDecisionStrategy;
use App\Models\AI\AISetting;
use App\Services\AI\AISettingsService;
use Illuminate\Support\Facades\App;

class AIDecisionStrategyFactory
{
    public function __construct(protected AISettingsService $settings) {}

    /**
     * Get the appropriate decision strategy based on settings
     */
    public function getStrategy(): DecisionStrategyInterface
    {
        $provider = $this->settings->get('ai_provider', 'local');

        // If provider is local, use legacy rule-based strategy
        if ($provider === 'local') {
            return App::make(LegacyDecisionStrategy::class);
        }

        // For external providers (openai, gemini, etc.), use Generative Strategy
        // We assume AIGateway acts as the bridge
        return App::make(GenerativeDecisionStrategy::class);
    }
}
