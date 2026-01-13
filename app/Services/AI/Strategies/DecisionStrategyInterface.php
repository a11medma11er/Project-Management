<?php

namespace App\Services\AI\Strategies;

interface DecisionStrategyInterface
{
    /**
     * Analyze task based on context
     */
    public function analyzeTask(array $context, ?string $decisionType = null): array;

    /**
     * Analyze project based on context
     */
    public function analyzeProject(array $context): array;
}
