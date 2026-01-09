<?php

namespace App\Models;

use App\Enums\TaskStatus;
use App\Enums\TaskPriority;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

class Task extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'task_number',
        'title',
        'description',
        'project_id',
        'client_name',
        'due_date',
        'status',
        'priority',
        'progress',
        'position',
        'kanban_status',  // Separate column for Kanban board
        'created_by',
    ];


    protected $casts = [
        'due_date' => 'date',
        'status' => TaskStatus::class,
        'priority' => TaskPriority::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];


    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'task_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class)->whereNull('parent_id');
    }

    public function allComments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function tags()
    {
        return $this->hasMany(TaskTag::class);
    }

    public function subTasks()
    {
        return $this->hasMany(TaskSubTask::class);
    }

    public function timeEntries()
    {
        return $this->hasMany(TaskTimeEntry::class);
    }

    public function aiDecisions()
    {
        return $this->hasMany(\App\Models\AI\AIDecision::class);
    }

    // Scopes
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('task_number', 'like', "%{$term}%")
              ->orWhere('client_name', 'like', "%{$term}%");
        });
    }

    /**
     * Scope for Kanban board ordering (by position within status)
     */
    public function scopeKanbanOrder($query)
    {
        return $query->orderBy('position', 'asc')
                     ->orderBy('created_at', 'desc');
    }

    // Auto-generate task number
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($task) {
            if (empty($task->task_number)) {
                // Use max ID to avoid race condition
                $maxId = \DB::table('tasks')->max('id') ?? 0;
                $nextNumber = $maxId + 1;
                $task->task_number = '#VLZ' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
        });

        static::saved(function ($task) {
            if ($task->project_id) {
                $task->project?->calculateProgress();
            }
        });

        static::deleted(function ($task) {
            if ($task->project_id) {
                $task->project?->calculateProgress();
            }
        });
    }

    // Business Logic Methods
    
    /**
     * Check if task is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date < now()->startOfDay() 
            && !$this->status->isTerminal();
    }

    /**
     * Get days overdue (0 if not overdue)
     */
    public function getDaysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        // Fixed: should be due_date->diffInDays(now()) not now()->diffInDays(due_date)
        return $this->due_date->diffInDays(now()->startOfDay());
    }

    /**
     * Get urgency level based on how overdue the task is
     */
    public function getUrgencyLevel(): string
    {
        $days = $this->getDaysOverdue();
        
        return match(true) {
            $days > 7 => 'critical',
            $days > 3 => 'high',
            $days > 0 => 'medium',
            default => 'normal'
        };
    }

    /**
     * Check if task is due soon (within days)
     */
    public function isDueSoon(int $days = 3): bool
    {
        return $this->due_date <= now()->addDays($days) 
            && $this->due_date >= now()->startOfDay()
            && !$this->status->isTerminal();
    }

    // Query Scopes

    /**
     * Scope for overdue tasks
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now()->startOfDay())
            ->whereIn('status', [
                TaskStatus::NEW->value,
                TaskStatus::PENDING->value,
                TaskStatus::IN_PROGRESS->value,
                TaskStatus::ON_HOLD->value,
            ]);
    }

    /**
     * Scope for tasks due soon
     */
    public function scopeDueSoon($query, int $days = 3)
    {
        return $query->whereBetween('due_date', [
            now()->startOfDay(),
            now()->addDays($days)
        ])->whereIn('status', [
            TaskStatus::NEW->value,
            TaskStatus::PENDING->value,
            TaskStatus::IN_PROGRESS->value,
        ]);
    }

    /**
     * Scope for active tasks (in progress or pending)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            TaskStatus::IN_PROGRESS->value,
            TaskStatus::PENDING->value,
        ]);
    }

    /**
     * Scope for completed tasks
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', TaskStatus::COMPLETED->value);
    }
    
    // Activity Log Configuration
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'title', 
                'description', 
                'status', 
                'priority', 
                'due_date',
                'client_name',
                'project_id'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('tasks');
    }
    
    public function tapActivity(Activity $activity, string $eventName): void
    {
        $activity->properties = $activity->properties->merge([
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context' => [
                'project_id' => $this->project_id,
                'overdue' => $this->isOverdue(),
                'urgency' => $this->getUrgencyLevel(),
                'status_label' => $this->status->label(),
                'priority_label' => $this->priority->label(),
            ],
        ]);
    }
}
