<?php

namespace App\Services\AI;

use App\Models\AI\AIPrompt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AIPromptTemplateService
{
    /**
     * Parse template and replace variables
     */
    public function parse(string $template, array $variables = []): string
    {
        $parsed = $template;
        
        // Replace {{variable}} syntax
        foreach ($variables as $key => $value) {
            $parsed = str_replace("{{" . $key . "}}", $this->formatValue($value), $parsed);
        }
        
        // Check for missing variables
        if (preg_match_all('/\{\{([^}]+)\}\}/', $parsed, $matches)) {
            $missingVars = $matches[1];
            throw new \Exception("Missing template variables: " . implode(', ', $missingVars));
        }
        
        return $parsed;
    }

    /**
     * Format value based on type
     */
    protected function formatValue($value): string
    {
        if (is_array($value)) {
            return json_encode($value, JSON_PRETTY_PRINT);
        }
        
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        
        if (is_null($value)) {
            return 'null';
        }
        
        return (string) $value;
    }

    /**
     * Extract variables from template
     */
    public function extractVariables(string $template): array
    {
        preg_match_all('/\{\{([^}]+)\}\}/', $template, $matches);
        
        return array_unique($matches[1]);
    }

    /**
     * Validate template syntax
     */
    public function validateTemplate(string $template): array
    {
        $errors = [];
        
        // Check for unmatched braces
        $openCount = substr_count($template, '{{');
        $closeCount = substr_count($template, '}}');
        
        if ($openCount !== $closeCount) {
            $errors[] = "Unmatched braces: {$openCount} opening, {$closeCount} closing";
        }
        
        // Check for nested braces
        if (preg_match('/\{\{[^}]*\{\{/', $template)) {
            $errors[] = "Nested braces are not allowed";
        }
        
        // Check for empty variable names
        if (preg_match('/\{\{\s*\}\}/', $template)) {
            $errors[] = "Empty variable names are not allowed";
        }
        
        return $errors;
    }

    /**
     * Get prompt by name and version
     */
    public function getPrompt(string $name, ?string $version = null): ?AIPrompt
    {
        $cacheKey = "ai_prompt_{$name}_" . ($version ?? 'latest');
        
        return Cache::remember($cacheKey, 3600, function () use ($name, $version) {
            $query = AIPrompt::where('name', $name)->active();
            
            if ($version) {
                $query->where('version', $version);
            } else {
                $query->orderBy('created_at', 'desc');
            }
            
            return $query->first();
        });
    }

    /**
     * Create new prompt version
     */
    public function createVersion(string $name, string $template, array $variables, string $description = null, string $type = 'user'): AIPrompt
    {
        // Get latest version
        $latestPrompt = AIPrompt::where('name', $name)
            ->orderBy('created_at', 'desc')
            ->first();
        
        // Increment version
        $newVersion = $latestPrompt 
            ? $this->incrementVersion($latestPrompt->version)
            : '1.0.0';
        
        // Validate template
        $errors = $this->validateTemplate($template);
        if (!empty($errors)) {
            throw new \Exception("Template validation failed: " . implode(', ', $errors));
        }
        
        // Create new prompt
        $prompt = AIPrompt::create([
            'name' => $name,
            'type' => $type,
            'template' => $template,
            'version' => $newVersion,
            'variables' => $variables,
            'description' => $description,
            'is_active' => true,
            'is_system' => $type === 'system',
            'usage_count' => 0,
            'created_by' => auth()->id(),
        ]);
        
        // Deactivate old versions (optional - keep only latest active)
        if ($latestPrompt) {
            AIPrompt::where('name', $name)
                ->where('id', '!=', $prompt->id)
                ->update(['is_active' => false]);
        }
        
        // Clear cache
        Cache::forget("ai_prompt_{$name}_latest");
        Cache::forget("system_prompt_{$name}"); // Also clear AIPromptHelper cache
        
        return $prompt;
    }

    /**
     * Increment semantic version
     */
    protected function incrementVersion(string $version, string $type = 'minor'): string
    {
        [$major, $minor, $patch] = explode('.', $version);
        
        switch ($type) {
            case 'major':
                return ($major + 1) . '.0.0';
            case 'minor':
                return $major . '.' . ($minor + 1) . '.0';
            case 'patch':
                return $major . '.' . $minor . '.' . ($patch + 1);
            default:
                return $major . '.' . ($minor + 1) . '.0';
        }
    }

    /**
     * Render prompt with variables
     */
    public function render(string $name, array $variables, ?string $version = null): string
    {
        $prompt = $this->getPrompt($name, $version);
        
        if (!$prompt) {
            throw new \Exception("Prompt not found: {$name}" . ($version ? " (version: {$version})" : ''));
        }
        
        // Increment usage count
        $prompt->incrementUsage();
        
        return $this->parse($prompt->template, $variables);
    }

    /**
     * Test prompt with sample data
     */
    public function test(string $template, array $sampleData): array
    {
        $startTime = microtime(true);
        
        try {
            // Validate
            $errors = $this->validateTemplate($template);
            if (!empty($errors)) {
                return [
                    'success' => false,
                    'errors' => $errors,
                    'output' => null,
                    'execution_time' => 0,
                ];
            }
            
            // Extract expected variables
            $expectedVars = $this->extractVariables($template);
            $missingVars = array_diff($expectedVars, array_keys($sampleData));
            
            if (!empty($missingVars)) {
                return [
                    'success' => false,
                    'errors' => ["Missing sample data for: " . implode(', ', $missingVars)],
                    'output' => null,
                    'execution_time' => 0,
                ];
            }
            
            // Parse
            $output = $this->parse($template, $sampleData);
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            return [
                'success' => true,
                'errors' => [],
                'output' => $output,
                'execution_time' => $executionTime,
                'variables_used' => $expectedVars,
                'character_count' => strlen($output),
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => [$e->getMessage()],
                'output' => null,
                'execution_time' => round((microtime(true) - $startTime) * 1000, 2),
            ];
        }
    }

    /**
     * Get all prompts by type
     */
    public function getByType(string $type): \Illuminate\Support\Collection
    {
        return Cache::remember("ai_prompts_type_{$type}", 1800, function () use ($type) {
            return AIPrompt::where('type', $type)
                ->active()
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get prompt history
     */
    public function getHistory(string $name): \Illuminate\Support\Collection
    {
        return AIPrompt::where('name', $name)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
