<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Project extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'title',
        'slug',
        'thumbnail',
        'description',
        'priority',
        'status',
        'privacy',
        'category',
        'skills',
        'deadline',
        'start_date',
        'progress',
        'is_favorite',
        'team_lead_id',
        'created_by',
    ];

    protected $casts = [
        'skills' => 'array',
        'deadline' => 'date',
        'start_date' => 'date',
        'created_at' => 'datetime',
        'is_favorite' => 'boolean',
        'progress' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }
        });
    }

    /**
     * Get the team lead for the project
     */
    public function teamLead()
    {
        return $this->belongsTo(User::class, 'team_lead_id');
    }

    /**
     * Get the user who created the project
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all tasks for the project
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Calculate and update project progress based on tasks
     */
    public function calculateProgress()
    {
        $totalTasks = $this->tasks()->count();
        
        if ($totalTasks == 0) {
            $this->progress = 0;
        } else {
            $completedTasks = $this->tasks()
                ->where(function($q) {
                    $q->where('kanban_status', 'completed')
                      ->orWhere('status', \App\Enums\TaskStatus::COMPLETED);
                })
                ->count();
            
            $this->progress = round(($completedTasks / $totalTasks) * 100);
        }
        
        $this->saveQuietly(); // Use saveQuietly to avoid triggering events if any
    }

    /**
     * Get all members of the project
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Get all attachments for the project
     */
    public function attachments()
    {
        return $this->hasMany(ProjectAttachment::class);
    }

    /**
     * Get all comments for the project
     */
    public function comments()
    {
        return $this->hasMany(ProjectComment::class)->whereNull('parent_id');
    }

    /**
     * Get all comments including replies
     */
    public function allComments()
    {
        return $this->hasMany(ProjectComment::class);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by priority
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for favorites only
     */
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    /**
     * Scope for search by title
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('title', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%");
    }

    /**
     * Configure activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status', 'priority', 'progress', 'deadline'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
