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
        $query = AIPrompt::query();

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by active status
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        // Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $prompts = $query->orderBy('name')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.ai-prompts.index', compact('prompts'));
    }

    /**
     * Show form to create new prompt
     */
    public function create()
    {
        return view('admin.ai-prompts.create');
    }

    /**
     * Store new prompt
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-z0-9_-]+$/',
            'type' => 'required|in:system,user,assistant',
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
                $validated['description'],
                $validated['type']
            );

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
        $prompt->load('creator');
        
        // Get history
        $history = $this->templateService->getHistory($prompt->name);

        return view('admin.ai-prompts.show', compact('prompt', 'history'));
    }

    /**
     * Show edit form
     */
    public function edit(AIPrompt $prompt)
    {
        return view('admin.ai-prompts.edit', compact('prompt'));
    }

    /**
     * Update prompt (creates new version)
     */
    public function update(Request $request, AIPrompt $prompt)
    {
        $validated = $request->validate([
            'template' => 'required|string',
            'description' => 'nullable|string|max:1000',
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
                $validated['description'],
                $prompt->type
            );

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
     * Soft delete prompt
     */
    public function destroy(AIPrompt $prompt)
    {
        try {
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
