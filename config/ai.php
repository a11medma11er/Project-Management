<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | AI Features Enabled
    |--------------------------------------------------------------------------
    */
    
    'enabled' => env('AI_ENABLED', false),
    
    /*
    |--------------------------------------------------------------------------
    | AI Provider
    |--------------------------------------------------------------------------
    */
    
    'provider' => env('AI_PROVIDER', null),
    
    /*
    |--------------------------------------------------------------------------
    | OpenAI Configuration
    |--------------------------------------------------------------------------
    */
    
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4'),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 1000),
        'temperature' => env('OPENAI_TEMPERATURE', 0.7),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Google Gemini Configuration
    |--------------------------------------------------------------------------
    */
    
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-pro'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Context Settings
    |--------------------------------------------------------------------------
    */
    
    'context' => [
        // Cache duration for context data (seconds)
        'cache_duration' => env('AI_CACHE_DURATION', 3600),
        
        // Maximum number of similar tasks to include
        'max_similar_tasks' => 10,
        
        // Days to look back for user patterns
        'history_days' => 90,
        
        // Pagination
        'per_page' => 50,
        
        // Activity limits
        'activity_limit' => 100,
        'analytics_days' => 30,
        'top_users_limit' => 10,
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Suggestion Settings
    |--------------------------------------------------------------------------
    */
    
    'suggestions' => [
        // Minimum confidence threshold to show suggestions
        'min_confidence' => 0.6,
        
        // Maximum suggestions to show at once
        'max_suggestions' => 3,
        
        // Cache suggestions for duration (seconds)
        'cache_duration' => 1800,
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */
    
    'logging' => [
        // Log AI requests and responses
        'log_requests' => env('AI_LOG_REQUESTS', true),
        
        // Log AI errors
        'log_errors' => env('AI_LOG_ERRORS', true),
        
        // Log user feedback
        'log_feedback' => env('AI_LOG_FEEDBACK', true),
    ],
    
];
