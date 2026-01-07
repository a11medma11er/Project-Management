<?php

namespace App\Contracts;

/**
 * AI Provider Interface
 * 
 * Defines the contract for AI service providers.
 * Implementations must handle graceful degradation.
 */
interface AIProvider
{
    /**
     * Get AI suggestion based on context
     * 
     * @param array $context The decision context
     * @return array|null Returns suggestion array or null on failure
     */
    public function getSuggestion(array $context): ?array;
    
    /**
     * Check if AI service is available
     * 
     * @return bool
     */
    public function isAvailable(): bool;
    
    /**
     * Record user feedback on AI suggestion
     * 
     * @param string $suggestionId The suggestion identifier
     * @param bool $accepted Whether the user accepted the suggestion
     * @param array $metadata Additional feedback metadata
     * @return void
     */
    public function recordFeedback(string $suggestionId, bool $accepted, array $metadata = []): void;
    
    /**
     * Get AI model information
     * 
     * @return array Model name, version, capabilities
     */
    public function getModelInfo(): array;
}
