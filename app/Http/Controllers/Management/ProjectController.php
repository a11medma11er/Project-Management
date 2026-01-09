<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Project::with(['creator', 'teamLead', 'members']);

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->priority($request->priority);
        }

        // Filter favorites
        if ($request->boolean('favorites')) {
            $query->favorites();
        }

        $projects = $query->latest()->paginate(12);

        return view('apps-projects-list', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::select('id', 'name', 'avatar')->orderBy('name')->get();
        return view('apps-projects-create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('projects/thumbnails',  'public');
        }

        // Convert skills string to array
        if (isset($data['skills'])) {
            $data['skills'] = array_map('trim', explode(',', $data['skills']));
        }

        // Set created_by
        $data['created_by'] = Auth::id();

        // Generate slug if not exists
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Create project
        $project = Project::create($data);

        // Attach members if provided
        if ($request->filled('members')) {
            $project->members()->attach($request->members);
        }

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            \Log::info('Files received:', ['count' => count($request->file('attachments'))]);
            
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('projects/attachments', 'public');
                
                \Log::info('File uploaded:', [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path
                ]);
                
                $project->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => Auth::id(),
                ]);
            }
        } else {
            \Log::info('No files received in request');
        }
        
        return redirect()->route('management.projects.index')
            ->with('success', 'Project created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $project = Project::with(['creator', 'teamLead', 'members', 'attachments', 'comments.user', 'comments.replies.user', 'tasks.assignedUsers', 'tasks.creator'])->findOrFail($id);
        $users = User::select('id', 'name', 'avatar')->orderBy('name')->get();
        
        // Get all project-related activities
        $activities = \Spatie\Activitylog\Models\Activity::where(function($query) use ($id) {
            // Activities on the project itself
            $query->where(function($q) use ($id) {
                $q->where('subject_type', 'App\\Models\\Project')
                  ->where('subject_id', $id);
            })
            // Activities on project tasks
            ->orWhereHasMorph('subject', ['App\\Models\\Task'], function($q) use ($id) {
                $q->where('project_id', $id);
            })
            // Activities on project comments
            ->orWhereHasMorph('subject', ['App\\Models\\ProjectComment'], function($q) use ($id) {
                $q->where('project_id', $id);
            })
            // Activities on project attachments
            ->orWhereHasMorph('subject', ['App\\Models\\ProjectAttachment'], function($q) use ($id) {
                $q->where('project_id', $id);
            });
        })
        ->with('causer', 'subject')
        ->latest()
        ->take(50)
        ->get();
        
        return view('apps-projects-overview', compact('project', 'users', 'activities'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $project = Project::with(['members'])->findOrFail($id);
        $users = User::select('id', 'name', 'avatar')->orderBy('name')->get();
        
        return view('apps-projects-create', compact('project', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, $id)
    {
        $project = Project::findOrFail($id);
        $data = $request->validated();

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($project->thumbnail) {
                Storage::disk('public')->delete($project->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('projects/thumbnails', 'public');
        }

        // Convert skills string to array
        if (isset($data['skills'])) {
            $data['skills'] = array_map('trim', explode(',', $data['skills']));
        }

        // Update project
        $project->update($data);

        // Sync members
        if ($request->has('members')) {
            $project->members()->sync($request->members);
        }

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            \Log::info('Files received for update:', ['count' => count($request->file('attachments'))]);
            
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('projects/attachments', 'public');
                
                \Log::info('File uploaded:', [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path
                ]);
                
                $project->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => Auth::id(),
                ]);
            }
        } else {
            \Log::info('No files received in update request');
        }

        return redirect()->route('management.projects.index')
            ->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        // Delete thumbnail
        if ($project->thumbnail) {
            Storage::disk('public')->delete($project->thumbnail);
        }

        // Delete all attachments
        foreach ($project->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $project->delete();

        return redirect()->route('management.projects.index')
            ->with('success', 'Project deleted successfully!');
    }

    /**
     * Toggle favorite status
     */
    public function toggleFavorite($id)
    {
        $project = Project::findOrFail($id);
        $project->is_favorite = !$project->is_favorite;
        $project->save();

        return response()->json([
            'success' => true,
            'is_favorite' => $project->is_favorite
        ]);
    }

    /**
     * Store project comment
     */
    public function storeComment(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        
        $request->validate([
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:project_comments,id',
        ]);

        $project->allComments()->create([
            'user_id' => Auth::id(),
            'comment' => $request->comment,
            'parent_id' => $request->parent_id,
        ]);

        return back()->with('success', 'Comment added successfully!');
    }
}
