<?php

namespace App\Models\AI;

use Illuminate\Database\Eloquent\Model;

class AutomationRule extends Model
{
    protected $table = 'automation_rules';

    protected $fillable = [
        'name',
        'trigger', 
        'conditions',
        'action',
        'is_active',
        'last_triggered_at'
    ];

    protected $casts = [
        'conditions' => 'array',
        'action' => 'array',
        'is_active' => 'boolean',
        'last_triggered_at' => 'datetime',
    ];
}
