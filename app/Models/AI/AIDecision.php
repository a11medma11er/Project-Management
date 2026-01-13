<?php

namespace App\Models\AI;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AIDecision extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ai_decisions';

    protected $fillable = [
        'task_id',
        'project_id',
        'decision_type',
        'ai_response',
        'suggested_actions',
        'confidence_score',
        'reasoning',
        'recommendation',
        'alternatives',
        'user_action',
        'user_feedback',
        'reviewed_by',
        'reviewed_at',
        'executed_at',
        'execution_result',
        'guardrail_violations',
        'guardrail_check',
    ];

    protected $casts = [
        'ai_response' => 'array',
        'suggested_actions' => 'array',
        'reasoning' => 'array',
        'alternatives' => 'array',
        'execution_result' => 'array',
        'guardrail_check' => 'array',
        'confidence_score' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'executed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }



    // Scopes
    public function scopePending($query)
    {
        return $query->where('user_action', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('user_action', 'accepted');
    }

    public function scopeRejected($query)
    {
        return $query->where('user_action', 'rejected');
    }

    public function scopeHighConfidence($query, $threshold = 0.7)
    {
        return $query->where('confidence_score', '>=', $threshold);
    }

    // Helper Methods
    public function isPending(): bool
    {
        return $this->user_action === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->user_action === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->user_action === 'rejected';
    }
}
