<?php

namespace App\Http\Controllers\Admin\AI;

use App\Http\Controllers\Controller;
use App\Models\AI\AIPrompt;
use App\Services\AI\AIPromptTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIPromptController extends Controller
{
    protected $templateService;

    public function __construct(AIPromptTemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    /**
     * Display list of prompts
     */
    public function index(Request $request)
    {
        $query = AIPrompt::with(['category', 'tags', 'creator']);

        // Filter by category
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category_id', $request->category);
        }

        // Filter by tags
        if ($request->has('tags') && !empty($request->tags)) {
            $tagIds = is_array($request->tags) ? $request->tags : [$request->tags];
            $query->withTags($tagIds);
        }

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by active status
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $prompts = $query->orderBy('name')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->except('page'));

        // Get all categories and tags for filters
        $categories = \App\Models\AI\PromptCategory::active()->ordered()->get();
        $tags = \App\Models\AI\PromptTag::alphabetical()->get();

        return view('admin.ai-prompts.index', compact('prompts', 'categories', 'tags'));
    }

    /**
     * Show form to create new prompt
     */
    public function create()
    {
        $categories = \App\Models\AI\PromptCategory::active()->ordered()->get();
        $tags = \App\Models\AI\PromptTag::alphabetical()->get();
        
        return view('admin.ai-prompts.create', compact('categories', 'tags'));
    }

    /**
     * Store new prompt
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-z0-9_-]+$/',
            'type' => 'required|in:system,user,assistant',
            'category_id' => 'nullable|exists:prompt_categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:prompt_tags,id',
            'template' => 'required|string',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            // Validate template
            $errors = $this->templateService->validateTemplate($validated['template']);
            if (!empty($errors)) {
                return back()
                    ->withInput()
                    ->withErrors(['template' => implode(', ', $errors)]);
            }

            // Extract variables
            $variables = $this->templateService->extractVariables($validated['template']);

            // Create prompt
            $prompt = $this->templateService->createVersion(
                $validated['name'],
                $validated['template'],
                $variables,
                $validated['description'] ?? null,
                $validated['type']
            );

            // Set category
            if (!empty($validated['category_id'])) {
                $prompt->update(['category_id' => $validated['category_id']]);
            }

            // Sync tags
            if (!empty($validated['tags'])) {
                $prompt->tags()->sync($validated['tags']);
            }

            // Log activity
            activity('ai')
                ->causedBy(auth()->user())
                ->performedOn($prompt)
                ->withProperties(['version' => $prompt->version])
                ->log('prompt_created');

            return redirect()
                ->route('ai.prompts.show', $prompt->id)
                ->with('success', 'Prompt created successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to create AI prompt: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create prompt: ' . $e->getMessage()]);
        }
    }

    /**
     * Show prompt details
     */
    public function show(AIPrompt $prompt)
    {
        $prompt->load(['creator', 'category', 'tags']);
        
        // Get history
        $history = $this->templateService->getHistory($prompt->name);

        return view('admin.ai-prompts.show', compact('prompt', 'history'));
    }

    /**
     * Show edit form
     */
    public function edit(AIPrompt $prompt)
    {
        $prompt->load(['category', 'tags']);
        $categories = \App\Models\AI\PromptCategory::active()->ordered()->get();
        $tags = \App\Models\AI\PromptTag::alphabetical()->get();
        
        return view('admin.ai-prompts.edit', compact('prompt', 'categories', 'tags'));
    }

    /**
     * Update prompt (creates new version)
     */
    public function update(Request $request, AIPrompt $prompt)
    {
        $validated = $request->validate([
            'template' => 'required|string',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'nullable|exists:prompt_categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:prompt_tags,id',
            'version_type' => 'required|in:major,minor,patch',
        ]);

        try {
            // Validate template
            $errors = $this->templateService->validateTemplate($validated['template']);
            if (!empty($errors)) {
                return back()
                    ->withInput()
                    ->withErrors(['template' => implode(', ', $errors)]);
            }

            // Extract variables
            $variables = $this->templateService->extractVariables($validated['template']);

            // Create new version
            $newPrompt = $this->templateService->createVersion(
                $prompt->name,
                $validated['template'],
                $variables,
                $validated['description'] ?? null,
                $prompt->type
            );

            // Set category
            if (isset($validated['category_id'])) {
                $newPrompt->update(['category_id' => $validated['category_id']]);
            }

            // Sync tags
            if (isset($validated['tags'])) {
                $newPrompt->tags()->sync($validated['tags']);
            } else {
                $newPrompt->tags()->detach();
            }

            // Log activity
            activity('ai')
                ->causedBy(auth()->user())
                ->performedOn($newPrompt)
                ->withProperties([
                    'old_version' => $prompt->version,
                    'new_version' => $newPrompt->version,
                ])
                ->log('prompt_updated');

            return redirect()
                ->route('ai.prompts.show', $newPrompt->id)
                ->with('success', 'New version created successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update AI prompt: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update prompt: ' . $e->getMessage()]);
        }
    }

    /**
     * Activate a specific version of a prompt
     */
    public function activate(AIPrompt $prompt)
    {
        try {
            // 1. Deactivate all other versions with the same name
            AIPrompt::where('name', $prompt->name)
                ->where('id', '!=', $prompt->id)
                ->update(['is_active' => false]);

            // 2. Activate this version
            $prompt->update(['is_active' => true]);

            // 3. Clear Cache (Both Service and Helper keys)
            \Illuminate\Support\Facades\Cache::forget("ai_prompt_{$prompt->name}_latest");
            \Illuminate\Support\Facades\Cache::forget("system_prompt_{$prompt->name}");

            // 4. Log activity
            activity('ai')
                ->causedBy(auth()->user())
                ->performedOn($prompt)
                ->withProperties(['version' => $prompt->version])
                ->log('prompt_activated');

            return back()->with('success', "Version v{$prompt->version} successfully activated!");

        } catch (\Exception $e) {
            Log::error('Failed to activate prompt version: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to activate version.']);
        }
    }

    /**
     * Soft delete prompt
     */
    public function destroy(AIPrompt $prompt)
    {
        try {
            // PROTECT SYSTEM PROMPTS FROM DELETION
            if ($prompt->is_system) {
                return back()->withErrors([
                    'error' => 'Cannot delete system prompts. System prompts are core to the AI functionality and cannot be removed. You can edit them to create new versions instead.'
                ]);
            }

            $prompt->delete();

            // Log activity
            activity('ai')
                ->causedBy(auth()->user())
                ->performedOn($prompt)
                ->log('prompt_deleted');

            return redirect()
                ->route('ai.prompts.index')
                ->with('success', 'Prompt deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to delete AI prompt: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to delete prompt.']);
        }
    }

    /**
     * Restore soft-deleted prompt
     */
    public function restore($id)
    {
        $prompt = AIPrompt::withTrashed()->findOrFail($id);
        $prompt->restore();

        // Log activity
        activity('ai')
            ->causedBy(auth()->user())
            ->performedOn($prompt)
            ->log('prompt_restored');

        return redirect()
            ->route('ai.prompts.show', $prompt->id)
            ->with('success', 'Prompt restored successfully!');
    }

    /**
     * Test prompt with sample data
     */
    public function test(Request $request, AIPrompt $prompt)
    {
        $validated = $request->validate([
            'sample_data' => 'required|array',
        ]);

        $result = $this->templateService->test(
            $prompt->template,
            $validated['sample_data']
        );

        return response()->json($result);
    }

    /**
     * Quick test (for testing page)
     */
    public function quickTest(Request $request)
    {
        $validated = $request->validate([
            'template' => 'required|string',
            'sample_data' => 'required|array',
        ]);

        $result = $this->templateService->test(
            $validated['template'],
            $validated['sample_data']
        );

        return response()->json($result);
    }

    /**
     * Toggle active status
     */
    public function toggleActive(AIPrompt $prompt)
    {
        $prompt->update(['is_active' => !$prompt->is_active]);

        // Log activity
        activity('ai')
            ->causedBy(auth()->user())
            ->performedOn($prompt)
            ->withProperties(['is_active' => $prompt->is_active])
            ->log('prompt_toggled');

        return response()->json([
            'success' => true,
            'is_active' => $prompt->is_active,
            'message' => $prompt->is_active ? 'Prompt activated' : 'Prompt deactivated',
        ]);
    }
}
