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

    public function __construct(AIDecisionEngine $decisionEngine)
    {
        $this->decisionEngine = $decisionEngine;
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
            // Update decision status
            $decision->update(['user_action' => 'accepted']);

            // Execute the decision
            $executed = $this->decisionEngine->executeDecision($decision);

            // Log activity
            activity('ai')
                ->causedBy(auth()->user())
                ->performedOn($decision)
                ->withProperties(['action' => 'accepted', 'executed' => $executed])
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
            // Update decision status
            $decision->update([
                'user_action' => 'rejected',
                'user_feedback' => $validated['rejection_reason'] ?? null,
            ]);

            // Log activity
            activity('ai')
                ->causedBy(auth()->user())
                ->performedOn($decision)
                ->withProperties([
                    'action' => 'rejected',
                    'reason' => $validated['rejection_reason'] ?? null
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
            // Update decision
            $decision->update([
                'user_action' => 'modified',
                'user_feedback' => $validated['modified_recommendation'],
            ]);

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
                    'executed' => $executed
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
                    $decision->update(['user_action' => 'accepted']);
                    
                    if ($this->decisionEngine->executeDecision($decision)) {
                        $accepted++;
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
