<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\TaskSubTask;
use App\Enums\TaskStatus;
use App\Enums\TaskPriority;
use App\Services\TaskStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['project', 'creator', 'assignedUsers']);

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->priority($request->priority);
        }

        // Filter by date range
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('due_date', [$request->date_from, $request->date_to]);
        }

        $tasks = $query->latest()->paginate(10);

        // Statistics
        $stats = [
            'total' => Task::count(),
            'pending' => Task::where('status', TaskStatus::PENDING->value)->count(),
            'completed' => Task::where('status', TaskStatus::COMPLETED->value)->count(),
            'overdue' => Task::overdue()->count(),
            'deleted' => Task::onlyTrashed()->count(),
        ];

        $projects = Project::select('id', 'title')->orderBy('title')->get();
        $users = User::select('id', 'name', 'avatar')->orderBy('name')->get();

        return view('apps-tasks-list-view', compact('tasks', 'stats', 'projects', 'users'));
    }


    public function create()
    {
        $this->authorize('create-tasks');
        
        $projects = Project::select('id', 'title')->orderBy('title')->get();
        $users = User::select('id', 'name', 'avatar')->orderBy('name')->get();
        
        return view('management.tasks.create', compact('projects', 'users'));
    }


    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();

        // Create task
        $task = Task::create($data);

        // Assign users
        if ($request->filled('assigned_users')) {
            $task->assignedUsers()->attach($request->assigned_users);
        }

        // Add tags
        if ($request->filled('tags')) {
            foreach ($request->tags as $tag) {
                $task->tags()->create(['tag' => $tag]);
            }
        }

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('tasks/attachments', $fileName, 'public');
                
                $task->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => round($file->getSize() / 1024), // Convert to KB
                ]);
            }
        }

        // Add sub-tasks
        if ($request->filled('sub_tasks')) {
            foreach ($request->sub_tasks as $subTaskTitle) {
                if (!empty(trim($subTaskTitle))) {
                    $task->subTasks()->create([
                        'title' => trim($subTaskTitle),
                        'is_completed' => false,
                    ]);
                }
            }
        }

        return redirect()->route('management.tasks.index')
            ->with('success', 'Task created successfully!');
    }

    public function show(Task $task)
    {
        $this->authorize('view-tasks');
        
        $task->load([
            'project',
            'creator',
            'assignedUsers',
            'attachments',
            'comments.user',
            'comments.replies.user',
            'tags',
            'subTasks',
            'timeEntries.user'
        ]);

        $users = User::orderBy('name')->get();

        return view('apps-tasks-details', compact('task', 'users'));
    }

    public function edit(Task $task)
    {
        $this->authorize('edit-tasks');
        
        $projects = Project::select('id', 'title')->orderBy('title')->get();
        $users = User::select('id', 'name', 'avatar')->orderBy('name')->get();
        
        return view('management.tasks.create', compact('task', 'projects', 'users'));
    }


    public function update(UpdateTaskRequest $request, Task $task)
    {
        $data = $request->validated();

        // Update task
        $task->update($data);

        // Sync assigned users
        if ($request->has('assigned_users')) {
            $task->assignedUsers()->sync($request->assigned_users);
        }

        // Sync tags
        if ($request->has('tags')) {
            $task->tags()->delete();
            foreach ($request->tags as $tag) {
                $task->tags()->create(['tag' => $tag]);
            }
        }

        // Handle new attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('tasks/attachments', $fileName, 'public');
                
                $task->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => round($file->getSize() / 1024), // Convert to KB
                ]);
            }
        }

        // Update sub-tasks (keep existing, add new ones)
        if ($request->filled('sub_tasks')) {
            foreach ($request->sub_tasks as $subTaskTitle) {
                if (!empty(trim($subTaskTitle))) {
                    $task->subTasks()->create([
                        'title' => trim($subTaskTitle),
                        'is_completed' => false,
                    ]);
                }
            }
        }

        return redirect()->route('management.tasks.index')
            ->with('success', 'Task updated successfully!');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete-tasks');

        $task->delete();

        return redirect()->route('management.tasks.index')
            ->with('success', 'Task deleted successfully!');
    }

    // Additional methods for comments, attachments, sub-tasks, etc.


    public function storeComment(Request $request, Task $task)
    {
        $this->authorize('view-tasks');
        
        $request->validate([
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:task_comments,id',
        ]);


        $task->allComments()->create([
            'user_id' => Auth::id(),
            'comment' => $request->comment,
            'parent_id' => $request->parent_id,
        ]);

        return back()->with('success', 'Comment added successfully!');
    }


    public function storeAttachment(Request $request, Task $task)
    {
        $this->authorize('edit-tasks');
        
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:10240', // 10MB
                'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg,gif,zip,rar,txt',
                'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,image/jpeg,image/png,image/gif,application/zip,text/plain',
            ],
        ]);


        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('task_attachments', $fileName, 'public');

            $task->attachments()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_type' => $file->getClientOriginalExtension(),
                'file_size' => round($file->getSize() / 1024), // KB
            ]);
        }

        return back()->with('success', 'File uploaded successfully!');
    }


    public function storeSubTask(Request $request, Task $task)
    {
        $this->authorize('edit-tasks');
        
        $request->validate([
            'title' => 'required|string|max:255',
        ]);


        $task->subTasks()->create([
            'title' => $request->title,
        ]);

        return back()->with('success', 'Sub-task added successfully!');
    }


    public function toggleSubTask(Task $task, TaskSubTask $subTask)
    {
        $this->authorize('edit-tasks');
        
        $subTask->update([
            'is_completed' => !$subTask->is_completed,
        ]);


        return response()->json([
            'success' => true,
            'is_completed' => $subTask->is_completed,
        ]);
    }


    public function storeTimeEntry(Request $request, Task $task)
    {
        $this->authorize('view-tasks');
        
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'duration_minutes' => 'required|integer|min:1|max:1440',
            'idle_minutes' => 'nullable|integer|min:0',
            'task_title' => 'nullable|string|max:255',
        ]);


        $task->timeEntries()->create([
            'user_id' => Auth::id(),
            'date' => $request->date,
            'duration_minutes' => $request->duration_minutes,
            'idle_minutes' => $request->idle_minutes ?? 0,
            'task_title' => $request->task_title,
        ]);

        return back()->with('success', 'Time entry added successfully!');
    }
}
