@extends('layouts.master')

@section('title') AI Decisions @endsection

@section('css')
<style>
.decision-card {
    transition: all 0.3s ease;
    border-left: 4px solid #5b73e8;
}
.decision-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.confidence-bar {
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
}
.confidence-fill {
    height: 100%;
    transition: width 0.3s ease;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    <i class="ri-lightbulb-line"></i> AI Decisions
                </h4>

                <div class="page-title-right">
                    @can('approve-ai-actions')
                    <button type="button" class="btn btn-success" id="bulk-accept-btn" disabled>
                        <i class="ri-check-line"></i> Accept Selected
                    </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card card-body bg-soft-warning">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-time-line font-size-24 text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mb-0">{{ $decisions->where('user_action', 'pending')->count() }}</h5>
                        <p class="text-muted mb-0">Pending Review</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-body bg-soft-success">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-check-line font-size-24 text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mb-0">{{ $decisions->where('user_action', 'accepted')->count() }}</h5>
                        <p class="text-muted mb-0">Accepted</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-body bg-soft-danger">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-close-line font-size-24 text-danger"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mb-0">{{ $decisions->where('user_action', 'rejected')->count() }}</h5>
                        <p class="text-muted mb-0">Rejected</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-body bg-soft-info">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-star-line font-size-24 text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mb-0">{{ round($decisions->avg('confidence_score') * 100, 1) }}%</h5>
                        <p class="text-muted mb-0">Avg Confidence</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('ai.decisions.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="modified" {{ request('status') == 'modified' ? 'selected' : '' }}>Modified</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" onchange="this.form.submit()">
                                <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Types</option>
                                <option value="task_analysis" {{ request('type') == 'task_analysis' ? 'selected' : '' }}>Task Analysis</option>
                                <option value="project_analysis" {{ request('type') == 'project_analysis' ? 'selected' : '' }}>Project Analysis</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Min Confidence (%)</label>
                            <input type="number" name="confidence" class="form-control" min="0" max="100" step="5" 
                                   value="{{ request('confidence', 0) }}" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sort By</label>
                            <select name="order_by" class="form-select" onchange="this.form.submit()">
                                <option value="created_at" {{ request('order_by') == 'created_at' ? 'selected' : '' }}>Date</option>
                                <option value="confidence_score" {{ request('order_by') == 'confidence_score' ? 'selected' : '' }}>Confidence</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Decisions List -->
    <div class="row">
        @forelse($decisions as $decision)
        <div class="col-12">
            <div class="card decision-card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-1 text-center">
                            @can('approve-ai-actions')
                            @if($decision->user_action === 'pending')
                            <input type="checkbox" class="form-check-input decision-checkbox" 
                                   value="{{ $decision->id }}" style="width: 20px; height: 20px;">
                            @endif
                            @endcan
                        </div>
                        
                        <div class="col-md-7">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="mb-1">
                                        @if($decision->task)
                                        <i class="ri-checkbox-line text-primary"></i> {{ $decision->task->title }}
                                        @elseif($decision->project)
                                        <i class="ri-folder-line text-info"></i> {{ $decision->project->title }}
                                        @endif
                                    </h5>
                                    <span class="badge bg-soft-primary text-primary me-1">
                                        {{ str_replace('_', ' ', ucfirst($decision->decision_type)) }}
                                    </span>
                                    <span class="badge bg-{{ $decision->user_action === 'pending' ? 'warning' : ($decision->user_action === 'accepted' ? 'success' : 'danger') }}">
                                        {{ ucfirst($decision->user_action) }}
                                    </span>
                                </div>
                            </div>
                            
                            <p class="text-muted mb-2">
                                <strong>Recommendation:</strong> {{ $decision->recommendation }}
                            </p>
                            
                            <div class="mb-2">
                                <small class="text-muted">Confidence:</small>
                                <div class="confidence-bar">
                                    <div class="confidence-fill bg-{{ $decision->confidence_score >= 0.8 ? 'success' : ($decision->confidence_score >= 0.6 ? 'warning' : 'danger') }}" 
                                         style="width: {{ $decision->confidence_score * 100 }}%"></div>
                                </div>
                                <small class="text-muted">{{ round($decision->confidence_score * 100, 2) }}%</small>
                            </div>
                            
                            <small class="text-muted">
                                <i class="ri-time-line"></i> {{ $decision->created_at->diffForHumans() }}
                            </small>
                        </div>
                        
                        <div class="col-md-4 text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('ai.decisions.show', $decision->id) }}" class="btn btn-sm btn-soft-primary">
                                    <i class="ri-eye-line"></i> View
                                </a>
                                
                                @can('approve-ai-actions')
                                @if($decision->user_action === 'pending')
                                <form action="{{ route('ai.decisions.accept', $decision->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" 
                                            onclick="return confirm('Accept this recommendation?')">
                                        <i class="ri-check-line"></i> Accept
                                    </button>
                                </form>
                                <form action="{{ route('ai.decisions.reject', $decision->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Reject this recommendation?')">
                                        <i class="ri-close-line"></i> Reject
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="ri-lightbulb-line font-size-48 text-muted d-block mb-3"></i>
                    <h5 class="text-muted">No AI decisions found</h5>
                    <p class="text-muted">Run analysis to generate recommendations</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($decisions->hasPages())
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $decisions->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Bulk Accept Form -->
<form id="bulk-accept-form" action="{{ route('ai.decisions.bulk-accept') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="decision_ids[]" id="bulk-decision-ids">
</form>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.decision-checkbox');
    const bulkAcceptBtn = document.getElementById('bulk-accept-btn');
    const bulkForm = document.getElementById('bulk-accept-form');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkButton);
    });
    
    if (bulkAcceptBtn) {
        bulkAcceptBtn.addEventListener('click', function() {
            const selected = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            
            if (selected.length > 0 && confirm(`Accept ${selected.length} decision(s)?`)) {
                // Clear existing inputs
                const container = bulkForm.querySelector('#bulk-decision-ids').parentElement;
                container.innerHTML = '';
                
                // Add selected IDs
                selected.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'decision_ids[]';
                    input.value = id;
                    container.appendChild(input);
                });
                
                bulkForm.submit();
            }
        });
    }
    
    function updateBulkButton() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        if (bulkAcceptBtn) {
            bulkAcceptBtn.disabled = !anyChecked;
        }
    }
});
</script>
@endsection
