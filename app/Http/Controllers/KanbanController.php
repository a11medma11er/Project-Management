<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class KanbanController extends Controller
{
    /**
     * Display the Kanban board with tasks grouped by status.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Task::query();

        // Role-based filtering
        if (!$user->hasRole('Super Admin') && !$user->hasRole('Admin')) {
            if ($user->hasRole('Manager')) {
                // Manager sees tasks in their projects
                $projectIds = Project::where('team_lead_id', $user->id)
                    ->orWhereHas('members', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })->pluck('id');
                
                $query->whereIn('project_id', $projectIds);
            } else {
                // User sees only assigned tasks
                $query->whereHas('assignedUsers', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
        }

        $tasks = $query->with(['assignedUsers', 'project', 'comments', 'attachments'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Group tasks by status
        $boardTasks = [
            'unassigned' => $tasks->filter(fn($t) => $t->assignedUsers->isEmpty()),
            'todo' => $tasks->where('status.value', 'pending')->filter(fn($t) => $t->assignedUsers->isNotEmpty()),
            'inprogress' => $tasks->where('status.value', 'in_progress'),
            'reviews' => $tasks->where('status.value', 'review'), // Assuming 'review' status exists or map appropriately
            'completed' => $tasks->where('status.value', 'completed'),
        ];

        return view('apps-tasks-kanban', compact('boardTasks'));
    }

    /**
     * Update task status via AJAX (Drag & Drop).
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'taskId' => 'required|exists:tasks,id',
            'status' => 'required|string'
        ]);

        $task = Task::find($request->taskId);
        
        // Map frontend column IDs to database status values
        $statusMap = [
            'todo-task' => 'pending',
            'inprogress-task' => 'in_progress',
            'reviews-task' => 'review',
            'completed-task' => 'completed',
            'unassigned-task' => 'pending' // Default fallback
        ];

        $newStatus = $statusMap[$request->status] ?? $request->status;
        
        // Enum validation check would be good here if using PHP 8.1 Enums
        // For now, simple string assignment assuming model handles casting or validation
        
        $task->status = $newStatus;
        $task->save();

        return response()->json(['success' => true, 'message' => 'Task status updated']);
    }
}
