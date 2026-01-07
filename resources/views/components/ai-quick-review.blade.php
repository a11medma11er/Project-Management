{{-- Quick AI Review Widget --}}
@can('view-ai-decisions')
<div class="col-xl-6">
    <div class="card card-height-100">
        <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1">
                <i class="ri-lightbulb-line text-warning"></i> AI Quick Review
            </h4>
            <div class="flex-shrink-0">
                <a href="{{ route('ai.decisions.index') }}" class="btn btn-sm btn-soft-primary">
                    View All <i class="ri-arrow-right-line"></i>
                </a>
            </div>
        </div>

        <div class="card-body">
            @php
                $pendingDecisions = \App\Models\AI\AIDecision::where('user_action', 'pending')
                    ->with(['task', 'project'])
                    ->orderBy('confidence_score', 'desc')
                    ->take(5)
                    ->get();
                
                $stats = [
                    'pending' => \App\Models\AI\AIDecision::where('user_action', 'pending')->count(),
                    'today' => \App\Models\AI\AIDecision::whereDate('created_at', today())->count(),
                    'avg_confidence' => \App\Models\AI\AIDecision::where('user_action', 'pending')
                        ->avg('confidence_score'),
                ];
            @endphp

            <!-- Stats Row -->
            <div class="row g-3 mb-3">
                <div class="col-4">
                    <div class="text-center">
                        <h3 class="mb-1 text-warning">{{ $stats['pending'] }}</h3>
                        <p class="text-muted mb-0 fs-12">Pending</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-center">
                        <h3 class="mb-1 text-info">{{ $stats['today'] }}</h3>
                        <p class="text-muted mb-0 fs-12">Today</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-center">
                        <h3 class="mb-1 text-success">{{ round($stats['avg_confidence'] * 100) }}%</h3>
                        <p class="text-muted mb-0 fs-12">Avg Confidence</p>
                    </div>
                </div>
            </div>

            <hr class="my-3">

            <!-- Decisions List -->
            <div data-simplebar style="max-height: 300px;">
                @forelse($pendingDecisions as $decision)
                <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-xs">
                            <span class="avatar-title bg-{{ $decision->confidence_score >= 0.8 ? 'success' : ($decision->confidence_score >= 0.6 ? 'warning' : 'danger') }}-subtle text-{{ $decision->confidence_score >= 0.8 ? 'success' : ($decision->confidence_score >= 0.6 ? 'warning' : 'danger') }} rounded-circle">
                                {{ round($decision->confidence_score * 100) }}%
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <h6 class="mb-1 text-truncate">
                            @if($decision->task)
                            <i class="ri-checkbox-line text-primary"></i> {{ Str::limit($decision->task->title, 35) }}
                            @elseif($decision->project)
                            <i class="ri-folder-line text-info"></i> {{ Str::limit($decision->project->title, 35) }}
                            @endif
                        </h6>
                        <p class="text-muted mb-1 fs-13">{{ Str::limit($decision->recommendation, 60) }}</p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('ai.decisions.show', $decision->id) }}" class="btn btn-sm btn-soft-primary">
                                <i class="ri-eye-line"></i> Review
                            </a>
                            @can('approve-ai-actions')
                            <form action="{{ route('ai.decisions.accept', $decision->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Accept this?')">
                                    <i class="ri-check-line"></i>
                                </button>
                            </form>
                            <form action="{{ route('ai.decisions.reject', $decision->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Reject this?')">
                                    <i class="ri-close-line"></i>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="ri-checkbox-circle-line fs-48 text-success"></i>
                    <p class="text-muted mt-2 mb-0">No pending AI decisions</p>
                    <small class="text-muted">All caught up! 🎉</small>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endcan
