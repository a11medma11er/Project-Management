<?php

namespace App\Models\AI;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIGeneratedContent extends Model
{
    use HasFactory;

    protected $table = 'ai_generated_contents';

    protected $fillable = [
        'project_id',
        'task_id',
        'user_id',
        'feature_type',
        'prompt_name',
        'content',
        'metrics',
        'provider',
        'model',
    ];

    protected $casts = [
        'content' => 'array',
        'metrics' => 'array',
    ];

    /**
     * Get the project that owns the content.
     */
    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class);
    }

    /**
     * Get the task that owns the content.
     */
    public function task()
    {
        return $this->belongsTo(\App\Models\Task::class);
    }

    /**
     * Get the user who generated the content.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
