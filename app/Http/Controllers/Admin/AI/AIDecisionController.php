<?php

namespace App\Http\Controllers\Admin\AI;

use App\Http\Controllers\Controller;
use App\Models\AI\AIDecision;
use App\Services\AI\AIDecisionEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIDecisionController extends Controller
{
    protected $decisionEngine;
    protected $feedbackService;

    public function __construct(AIDecisionEngine $decisionEngine, \App\Services\AI\AIFeedbackService $feedbackService)
    {
        $this->decisionEngine = $decisionEngine;
        $this->feedbackService = $feedbackService;
    }

    /**
     * Display list of AI decisions
     */
    public function index(Request $request)
    {
        $query = AIDecision::with(['task', 'project']);

        // Filter by action status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('user_action', $request->status);
        }

        // Filter by decision type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('decision_type', $request->type);
        }

        // Filter by confidence
        if ($request->has('confidence')) {
            $confidence = floatval($request->confidence) / 100;
            $query->where('confidence_score', '>=', $confidence);
        }

        // Order by
        $orderBy = $request->get('order_by', 'created_at');
        $orderDirection = $request->get('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        $decisions = $query->paginate(20);

        return view('admin.ai-decisions.index', compact('decisions'));
    }

    /**
     * Show decision details
     */
    public function show(AIDecision $decision)
    {
        $decision->load(['task', 'project']);

        return view('admin.ai-decisions.show', compact('decision'));
    }

    /**
     * Accept decision
     */
    public function accept(AIDecision $decision, Request $request)
    {
        try {
            // Record feedback (handles status update and learning logs)
            $this->feedbackService->recordFeedback($decision, 'accepted');

            // Execute the decision
            $executed = $this->decisionEngine->executeDecision($decision);

            // Log activity
            activity('ai')
                ->causedBy(auth()->user())
                ->performedOn($decision)
                ->withProperties([
                    'action' => 'accepted',
                    'executed' => $executed,
                    'decision_type' => $decision->decision_type,
                    'confidence_score' => $decision->confidence_score,
                    'task_id' => $decision->task_id,
                    'project_id' => $decision->project_id,
                    'ai_response_time' => $decision->created_at->diffInSeconds(now()) . 's_after_creation'
                ])
                ->log('decision_accepted');

            $message = $executed 
                ? 'Decision accepted and executed successfully!'
                : 'Decision accepted but execution failed. Check logs for details.';

            return redirect()
                ->route('ai.decisions.show', $decision->id)
                ->with($executed ? 'success' : 'warning', $message);

        } catch (\Exception $e) {
            Log::error("Failed to accept decision #{$decision->id}: " . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to accept decision: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject decision
     */
    public function reject(AIDecision $decision, Request $request)
    {
        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        try {
            // Record feedback
            $this->feedbackService->recordFeedback(
                $decision, 
                'rejected', 
                $validated['rejection_reason'] ?? null
            );

            // Log activity
            activity('ai')
                ->causedBy(auth()->user())
                ->performedOn($decision)
                ->withProperties([
                    'action' => 'rejected',
                    'reason' => $validated['rejection_reason'] ?? null,
                    'decision_type' => $decision->decision_type,
                    'confidence_score' => $decision->confidence_score,
                    'task_id' => $decision->task_id,
                    'project_id' => $decision->project_id,
                ])
                ->log('decision_rejected');

            return redirect()
                ->route('ai.decisions.index')
                ->with('success', 'Decision rejected successfully!');

        } catch (\Exception $e) {
            Log::error("Failed to reject decision #{$decision->id}: " . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to reject decision.']);
        }
    }

    /**
     * Modify and accept decision
     */
    public function modify(AIDecision $decision, Request $request)
    {
        $validated = $request->validate([
            'modified_recommendation' => 'required|string|max:500',
        ]);

        try {
            // Record feedback
            $this->feedbackService->recordFeedback(
                $decision, 
                'modified', 
                $validated['modified_recommendation']
            );

            // Execute modified decision
            $executed = $this->decisionEngine->executeDecision(
                $decision,
                $validated['modified_recommendation']
            );

            // Log activity
            activity('ai')
                ->causedBy(auth()->user())
                ->performedOn($decision)
                ->withProperties([
                    'action' => 'modified',
                    'modified_recommendation' => $validated['modified_recommendation'],
                    'executed' => $executed,
                    'original_recommendation' => $decision->recommendation,
                    'decision_type' => $decision->decision_type,
                    'confidence_score' => $decision->confidence_score,
                    'task_id' => $decision->task_id,
                    'project_id' => $decision->project_id,
                ])
                ->log('decision_modified');

            return redirect()
                ->route('ai.decisions.show', $decision->id)
                ->with('success', 'Decision modified and executed successfully!');

        } catch (\Exception $e) {
            Log::error("Failed to modify decision #{$decision->id}: " . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to modify decision.']);
        }
    }

    /**
     * Bulk accept pending decisions
     */
    public function bulkAccept(Request $request)
    {
        $validated = $request->validate([
            'decision_ids' => 'required|array',
            'decision_ids.*' => 'exists:ai_decisions,id',
        ]);

        try {
            $accepted = 0;
            $failed = 0;

            foreach ($validated['decision_ids'] as $decisionId) {
                $decision = AIDecision::find($decisionId);
                
                if ($decision && $decision->user_action === 'pending') {
                    $this->feedbackService->recordFeedback($decision, 'accepted');
                    
                    if ($this->decisionEngine->executeDecision($decision)) {
                        $accepted++;
                        
                        // Log activity for each bulk decision
                        activity('ai')
                            ->causedBy(auth()->user())
                            ->performedOn($decision)
                            ->withProperties([
                                'action' => 'bulk_accepted',
                                'decision_type' => $decision->decision_type,
                                'confidence_score' => $decision->confidence_score,
                                'task_id' => $decision->task_id,
                                'project_id' => $decision->project_id,
                            ])
                            ->log('decision_accepted');
                    } else {
                        $failed++;
                    }
                }
            }

            $message = "Accepted {$accepted} decision(s)";
            if ($failed > 0) {
                $message .= " ({$failed} failed execution)";
            }

            return redirect()
                ->route('ai.decisions.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error("Bulk accept failed: " . $e->getMessage());
            
            return back()->withErrors(['error' => 'Bulk operation failed.']);
        }
    }

    /**
     * Delete decision (soft delete)
     */
    public function destroy(AIDecision $decision)
    {
        try {
            $decision->delete();

            // Log activity
            activity('ai')
                ->causedBy(auth()->user())
                ->performedOn($decision)
                ->withProperties([
                    'action' => 'deleted',
                    'decision_type' => $decision->decision_type,
                    'confidence_score' => $decision->confidence_score
                ])
                ->log('decision_deleted');

            return redirect()
                ->route('ai.decisions.index')
                ->with('success', 'Decision deleted successfully!');

        } catch (\Exception $e) {
            Log::error("Failed to delete decision #{$decision->id}: " . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to delete decision.']);
        }
    }
}
