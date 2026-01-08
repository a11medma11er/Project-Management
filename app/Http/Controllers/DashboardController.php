<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\TaskTimeEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all dashboard data
        $statistics = $this->getStatistics($user);
        $chartData = $this->getProjectsChart($user);
        $upcomingTasks = $this->getUpcomingTasks($user);
        $activeProjects = $this->getActiveProjects($user);
        $userTasks = $this->getUserTasks($user);
        $teamMembers = $this->getTeamMembers($user);
        
        return view('dashboard-projects', compact(
            'statistics',
            'chartData',
            'upcomingTasks',
            'activeProjects',
            'userTasks',
            'teamMembers'
        ));
    }

    /**
     * Get statistics for dashboard cards.
     */
    private function getStatistics($user)
    {
        // Active Projects Count
        $activeProjectsCount = $this->getUserProjectsQuery($user)
            ->where('status', 'Inprogress')
            ->count();
            
        // Last month active projects for comparison
        $lastMonthActiveProjects = $this->getUserProjectsQuery($user)
            ->where('status', 'Inprogress')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
            
        // Calculate percentage change
        $projectsTrend = $lastMonthActiveProjects > 0 
            ? round((($activeProjectsCount - $lastMonthActiveProjects) / $lastMonthActiveProjects) * 100, 2)
            : 0;
        
        // New Tasks This Month
        $newTasksCount = $this->getUserTasksQuery($user)
            ->where('status', 'new')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
            
        // Last month new tasks
        $lastMonthNewTasks = $this->getUserTasksQuery($user)
            ->where('status', 'new')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
            
        $tasksTrend = $lastMonthNewTasks > 0
            ? round((($newTasksCount - $lastMonthNewTasks) / $lastMonthNewTasks) * 100, 2)
            : 0;
        
        // Total Hours This Month
        $totalMinutes = TaskTimeEntry::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->when(!$user->hasRole(['Super Admin', 'Admin']), function($q) use ($user) {
                return $q->whereHas('task.assignedUsers', fn($query) => $query->where('user_id', $user->id));
            })
            ->sum('duration_minutes');
            
        $totalHours = floor($totalMinutes / 60);
        $totalMinutesRemainder = $totalMinutes % 60;
        
        // Last month total minutes
        $lastMonthMinutes = TaskTimeEntry::whereMonth('date', now()->subMonth()->month)
            ->whereYear('date', now()->subMonth()->year)
            ->when(!$user->hasRole(['Super Admin', 'Admin']), function($q) use ($user) {
                return $q->whereHas('task.assignedUsers', fn($query) => $query->where('user_id', $user->id));
            })
            ->sum('duration_minutes');
            
        $hoursTrend = $lastMonthMinutes > 0
            ? round((($totalMinutes - $lastMonthMinutes) / $lastMonthMinutes) * 100, 2)
            : 0;
        
        return [
            'active_projects' => $activeProjectsCount,
            'projects_trend' => $projectsTrend,
            'new_tasks' => $newTasksCount,
            'tasks_trend' => $tasksTrend,
            'total_hours' => $totalHours,
            'total_minutes' => $totalMinutesRemainder,
            'hours_trend' => $hoursTrend,
        ];
    }

    /**
     * Get projects chart data.
     */
    private function getProjectsChart($user)
    {
        // Total Projects
        $totalProjects = $this->getUserProjectsQuery($user)->count();
        
        // Active Projects
        $activeProjects = $this->getUserProjectsQuery($user)
            ->where('status', 'Inprogress')
            ->count();
        
        // Total Working Hours (all time)
        $totalMinutes = TaskTimeEntry::when(!$user->hasRole(['Super Admin', 'Admin']), function($q) use ($user) {
                return $q->whereHas('task.assignedUsers', fn($query) => $query->where('user_id', $user->id));
            })
            ->sum('duration_minutes');
        $workingHours = floor($totalMinutes / 60);
        
        // Monthly projects data for chart (last 12 months)
        $monthlyData = [];
        $months = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = $this->getUserProjectsQuery($user)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
                
            $monthlyData[] = $count;
            $months[] = $date->format('M Y');
        }
        
        return [
            'total_projects' => $totalProjects,
            'active_projects' => $activeProjects,
            'working_hours' => $workingHours,
            'monthly_data' => $monthlyData,
            'months' => $months,
        ];
    }

    /**
     * Get upcoming tasks with deadlines.
     */
    private function getUpcomingTasks($user)
    {
        return $this->getUserTasksQuery($user)
            ->with(['project:id,title'])
            ->whereNotNull('due_date')
            ->where('due_date', '>=', now())
            ->whereIn('status', ['new', 'pending', 'in_progress'])
            ->orderBy('due_date', 'asc')
            ->limit(4)
            ->get();
    }

    /**
     * Get active projects for table.
     */
    private function getActiveProjects($user)
    {
        return $this->getUserProjectsQuery($user)
            ->with(['teamLead:id,name,avatar', 'members:id,name,avatar'])
            ->where('status', 'Inprogress')
            ->orderBy('deadline', 'asc')
            ->paginate(5);
    }

    /**
     * Get user's tasks.
     */
    private function getUserTasks($user)
    {
        return $this->getUserTasksQuery($user)
            ->with(['project:id,title', 'assignedUsers:id,name,avatar'])
            ->orderBy('due_date', 'asc')
            ->limit(6)
            ->get();
    }

    /**
     * Get team members with statistics.
     */
    private function getTeamMembers($user)
    {
        $query = User::query();
        
        // Filter based on user role
        if (!$user->hasRole(['Super Admin', 'Admin'])) {
            // For Managers and Users, show only team members from their projects
            $query->whereHas('projects', function($q) use ($user) {
                $q->whereHas('members', fn($query) => $query->where('user_id', $user->id))
                  ->orWhere('team_lead_id', $user->id);
            });
        }
        
        return $query->with(['roles'])
            ->get()
            ->map(function($member) {
                // Calculate hours for this month
                $totalMinutes = TaskTimeEntry::whereMonth('date', now()->month)
                    ->whereYear('date', now()->year)
                    ->whereHas('task.assignedUsers', fn($q) => $q->where('user_id', $member->id))
                    ->sum('duration_minutes');
                
                $hours = floor($totalMinutes / 60);
                
                // Count tasks for this month
                $tasksCount = Task::whereHas('assignedUsers', fn($q) => $q->where('user_id', $member->id))
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count();
                
                // Calculate progress (assuming 150h target per month)
                $progress = min(round(($hours / 150) * 100), 100);
                
                $member->hours = $hours;
                $member->tasks_count = $tasksCount;
                $member->progress = $progress;
                $member->role_name = $member->roles->first()?->name ?? 'User';
                
                return $member;
            })
            ->take(5); // Show top 5 members
    }

    /**
     * Get projects query filtered by user role.
     */
    private function getUserProjectsQuery($user)
    {
        $query = Project::query();
        
        // Filter based on user role
        if (!$user->hasRole(['Super Admin', 'Admin'])) {
            $query->where(function($q) use ($user) {
                $q->whereHas('members', fn($query) => $query->where('user_id', $user->id))
                  ->orWhere('team_lead_id', $user->id)
                  ->orWhere('created_by', $user->id);
            });
        }
        
        return $query;
    }

    /**
     * Get tasks query filtered by user role.
     */
    private function getUserTasksQuery($user)
    {
        $query = Task::query();
        
        // Filter based on user role
        if (!$user->hasRole(['Super Admin', 'Admin'])) {
            $query->whereHas('assignedUsers', fn($q) => $q->where('user_id', $user->id));
        }
        
        return $query;
    }

    /**
     * AJAX: Filter tasks by status
     */
    public function filterTasks(Request $request)
    {
        $status = $request->get('status', 'all');
        $user = Auth::user();
        
        $query = $this->getUserTasksQuery($user)
            ->with(['project:id,title', 'assignedUsers:id,name,avatar']);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $tasks = $query->orderBy('due_date', 'asc')
            ->limit(6)
            ->get();
        
        return response()->json([
            'success' => true,
            'tasks' => $tasks->map(function($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'due_date' => $task->due_date ? $task->due_date->format('d M Y') : 'N/A',
                    'status' => $task->status->value,
                    'assignee' => $task->assignedUsers->first() ? [
                        'name' => $task->assignedUsers->first()->name,
                        'avatar' => $task->assignedUsers->first()->avatar ? asset('storage/'.$task->assignedUsers->first()->avatar) : null
                    ] : null
                ];
            })
        ]);
    }

    /**
     * AJAX: Get upcoming tasks by date
     */
    public function getUpcomingTasksByDate(Request $request)
    {
        $date = $request->get('date');
        $user = Auth::user();
        
        $query = $this->getUserTasksQuery($user)
            ->with(['project:id,title'])
            ->whereNotNull('due_date')
            ->whereIn('status', ['new', 'pending', 'in_progress']);
        
        if ($date) {
            $query->whereDate('due_date', $date);
        } else {
            $query->where('due_date', '>=', now());
        }
        
        $tasks = $query->orderBy('due_date', 'asc')
            ->limit(10)
            ->get();
        
        return response()->json([
            'success' => true,
            'tasks' => $tasks->map(function($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'project' => $task->project ? $task->project->title : 'N/A',
                    'due_date' => $task->due_date ? $task->due_date->format('d M Y') : 'N/A',
                    'day' => $task->due_date ? $task->due_date->format('d') : '--'
                ];
            })
        ]);
    }

    /**
     * AJAX: Sort team members
     */
    public function sortTeamMembers(Request $request)
    {
        $period = $request->get('period', 'month'); // today, week, month
        $user = Auth::user();
        
        $query = User::query();
        
        // Filter based on user role
        if (!$user->hasRole(['Super Admin', 'Admin'])) {
            $query->whereHas('projects', function($q) use ($user) {
                $q->whereHas('members', fn($query) => $query->where('user_id', $user->id))
                  ->orWhere('team_lead_id', $user->id);
            });
        }
        
        $dateFilter = match($period) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => now()->startOfMonth()
        };
        
        $members = $query->with(['roles'])
            ->get()
            ->map(function($member) use ($dateFilter) {
                $totalMinutes = TaskTimeEntry::where('date', '>=', $dateFilter)
                    ->whereHas('task.assignedUsers', fn($q) => $q->where('user_id', $member->id))
                    ->sum('duration_minutes');
                    
                $hours = floor($totalMinutes / 60);
                
                $tasksCount = Task::whereHas('assignedUsers', fn($q) => $q->where('user_id', $member->id))
                    ->where('created_at', '>=', $dateFilter)
                    ->count();
                
                $progress = min(round(($hours / 150) * 100), 100);
                
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'avatar' => $member->avatar ? asset('storage/'.$member->avatar) : null,
                    'role' => $member->roles->first()?->name ?? 'User',
                    'hours' => $hours,
                    'tasks_count' => $tasksCount,
                    'progress' => $progress
                ];
            })
            ->take(5);
        
        return response()->json([
            'success' => true,
            'members' => $members
        ]);
    }
}
