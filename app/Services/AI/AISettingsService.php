<?php

namespace App\Services\AI;

use App\Models\AI\AISetting;
use Illuminate\Support\Facades\Cache;

class AISettingsService
{
    /**
     * Get a setting value with caching
     */
    public function get(string $key, $default = null)
    {
        return Cache::remember("ai_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = AISetting::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            return $setting->getCastedValue();
        });
    }

    /**
     * Set a setting value
     */
    public function set(string $key, $value, string $type = null, string $description = null, string $group = 'general'): void
    {
        $type = $type ?? $this->detectType($value);
        
        $setting = AISetting::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) || is_object($value) ? json_encode($value) : $value,
                'type' => $type,
                'description' => $description,
                'group' => $group,
            ]
        );

        Cache::forget("ai_setting_{$key}");
    }

    /**
     * Get all settings by group
     */
    public function getByGroup(string $group)
    {
        return Cache::remember("ai_settings_group_{$group}", 3600, function () use ($group) {
            return AISetting::byGroup($group)->get();
        });
    }

    /**
     * Auto-detect type from value
     */
    private function detectType($value): string
    {
        if (is_bool($value)) {
            return 'boolean';
        }
        if (is_int($value)) {
            return 'integer';
        }
        if (is_float($value)) {
            return 'float';
        }
        if (is_array($value) || is_object($value)) {
            return 'json';
        }
        return 'string';
    }
}
