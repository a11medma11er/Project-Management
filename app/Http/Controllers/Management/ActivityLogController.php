<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\Management\ActivityLogFilterRequest;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display activity log dashboard
     */
    public function index(ActivityLogFilterRequest $request)
    {
        $query = Activity::with(['causer', 'subject'])
            ->latest();
            
        // Filter by log name
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }
        
        // Filter by event
        if ($request->filled('event')) {
            $query->where('description', $request->event);
        }
        
        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Search in properties
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', "%{$request->search}%");
            });
        }
        
        $activities = $query->paginate(config('ai.context.per_page', 50));
        
        // Statistics
        $stats = [
            'total_today' => Activity::whereDate('created_at', today())->count(),
            'tasks_events' => Activity::where('log_name', 'tasks')->whereDate('created_at', today())->count(),
            'ai_events' => Activity::where('log_name', 'ai')->whereDate('created_at', today())->count(),
            'unique_users' => Activity::whereDate('created_at', today())->distinct('causer_id')->count('causer_id'),
        ];
        
        $logNames = Activity::distinct('log_name')->pluck('log_name');
        $users = User::select('id', 'name')->get();
        
        return view('management.activity-logs.index', compact('activities', 'stats', 'logNames', 'users'));
    }
    
    /**
     * Show activity details
     */
    public function show(Activity $activity)
    {
        $this->authorize('view-activity-logs');
        
        $activity->load(['causer', 'subject']);
        
        return view('management.activity-logs.show', compact('activity'));
    }
    
    /**
     * Analytics page
     */
    public function analytics()
    {
        $this->authorize('view-activity-logs');
        
        // Activity trends (last 30 days)
        $dailyActivities = Activity::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Most active users
        $topUsers = Activity::selectRaw('causer_id, COUNT(*) as activity_count')
            ->whereNotNull('causer_id')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('causer_id')
            ->orderByDesc('activity_count')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $item->causer = User::find($item->causer_id);
                return $item;
            });
            
        // Event distribution
        $eventDistribution = Activity::selectRaw('description, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('description')
            ->orderByDesc('count')
            ->limit(15)
            ->get();
            
        // AI acceptance rate
        $aiAccepted = Activity::where('log_name', 'ai')
            ->where('description', 'ai_suggestion_accepted')
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->count();
            
        $aiRejected = Activity::where('log_name', 'ai')
            ->where('description', 'ai_suggestion_rejected')
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->count();
            
        $aiStats = [
            'accepted' => $aiAccepted,
            'rejected' => $aiRejected,
            'acceptance_rate' => $aiAccepted + $aiRejected > 0 
                ? round(($aiAccepted / ($aiAccepted + $aiRejected)) * 100, 2) 
                : 0
        ];
        
        return view('management.activity-logs.analytics', compact(
            'dailyActivities',
            'topUsers',
            'eventDistribution',
            'aiStats'
        ));
    }
    
    /**
     * Cleanup old activities
     */
    public function cleanup(Request $request)
    {
        $this->authorize('manage-activity-logs');
        
        $request->validate([
            'days' => 'required|integer|min:30'
        ]);
        
        $deleted = Activity::where('created_at', '<', now()->subDays($request->days))
            ->delete();
            
        return back()->with('success', "Deleted {$deleted} old activities");
    }
}
