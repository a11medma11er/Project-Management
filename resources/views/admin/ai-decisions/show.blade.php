@extends('layouts.master')

@section('title') Decision Details @endsection

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    <i class="ri-lightbulb-line"></i> Decision Details
                </h4>

                <div class="page-title-right">
                    <a href="{{ route('ai.decisions.index') }}" class="btn btn-secondary">
                        <i class="ri-arrow-left-line"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Decision Info -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="card-title mb-2">
                                @if($decision->task)
                                <i class="ri-checkbox-line text-primary"></i> {{ $decision->task->title }}
                                @elseif($decision->project)
                                <i class="ri-folder-line text-info"></i> {{ $decision->project->title }}
                                @endif
                            </h4>
                            <div class="mb-2">
                                <span class="badge bg-soft-primary text-primary me-1">
                                    {{ str_replace('_', ' ', ucfirst($decision->decision_type)) }}
                                </span>
                                <span class="badge bg-{{ $decision->user_action === 'pending' ? 'warning' : ($decision->user_action === 'accepted' ? 'success' : 'danger') }}">
                                    {{ ucfirst($decision->user_action) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Recommendation -->
                    <div class="mb-4">
                        <h5 class="mb-2"><i class="ri-star-line text-warning"></i> Recommendation</h5>
                        <div class="alert alert-info mb-0">
                            <strong>{{ $decision->recommendation }}</strong>
                        </div>
                    </div>

                    <!-- Confidence Score -->
                    <div class="mb-4">
                        <h5 class="mb-2"><i class="ri-bar-chart-line text-success"></i> Confidence Score</h5>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-{{ $decision->confidence_score >= 0.8 ? 'success' : ($decision->confidence_score >= 0.6 ? 'warning' : 'danger') }}" 
                                 role="progressbar" 
                                 style="width: {{ $decision->confidence_score * 100 }}%" 
                                 aria-valuenow="{{ $decision->confidence_score * 100 }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ round($decision->confidence_score * 100, 2) }}%
                            </div>
                        </div>
                    </div>

                    <!-- Reasoning -->
                    @if($decision->reasoning && count($decision->reasoning) > 0)
                    <div class="mb-4">
                        <h5 class="mb-3"><i class="ri-lightbulb-flash-line text-info"></i> Reasoning</h5>
                        <ul class="list-group list-group-flush">
                            @foreach($decision->reasoning as $reason)
                            <li class="list-group-item">
                                <i class="ri-checkbox-circle-line text-success"></i> {{ $reason }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Alternatives -->
                    @if($decision->alternatives && count($decision->alternatives) > 0)
                    <div class="mb-4">
                        <h5 class="mb-3"><i class="ri-route-line text-warning"></i> Alternative Actions</h5>
                        @foreach($decision->alternatives as $index => $alternative)
                        <div class="card border mb-2">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $alternative['action'] }}</h6>
                                        <p class="text-muted mb-0">{{ $alternative['description'] }}</p>
                                    </div>
                                    <span class="badge bg-{{ $alternative['impact'] === 'Low' ? 'success' : ($alternative['impact'] === 'Medium' ? 'warning' : 'danger') }}">
                                        {{ $alternative['impact'] }} Impact
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- User Feedback -->
                    @if($decision->user_feedback)
                    <div class="mb-4">
                        <h5 class="mb-2"><i class="ri-message-2-line text-primary"></i> User Feedback</h5>
                        <div class="alert alert-secondary">
                            {{ $decision->user_feedback }}
                        </div>
                    </div>
                    @endif

                    <!-- Execution Result -->
                    @if($decision->executed_at && $decision->execution_result)
                    <div class="mb-4">
                        <h5 class="mb-2"><i class="ri-play-line text-success"></i> Execution Result</h5>
                        <div class="card border-success">
                            <div class="card-body">
                                @if(isset($decision->execution_result['status']))
                                <p class="mb-1">
                                    <strong>Status:</strong> 
                                    <span class="badge bg-{{ $decision->execution_result['status'] === 'failed' ? 'danger' : 'success' }}">
                                        {{ ucfirst($decision->execution_result['status']) }}
                                    </span>
                                </p>
                                @endif
                                @if(isset($decision->execution_result['action_taken']))
                                <p class="mb-1"><strong>Action:</strong> {{ $decision->execution_result['action_taken'] }}</p>
                                @endif
                                @if(isset($decision->execution_result['timestamp']))
                                <p class="mb-0"><strong>Time:</strong> {{ $decision->execution_result['timestamp'] }}</p>
                                @endif
                                @if(isset($decision->execution_result['error']))
                                <div class="alert alert-danger mt-2 mb-0">
                                    <strong>Error:</strong> {{ $decision->execution_result['error'] }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    @can('approve-ai-actions')
                    @if($decision->user_action === 'pending')
                    <hr>
                    <div class="d-flex gap-2">
                        <form action="{{ route('ai.decisions.accept', $decision->id) }}" method="POST" class="flex-fill">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Accept this recommendation?')">
                                <i class="ri-check-line"></i> Accept & Execute
                            </button>
                        </form>
                        
                        <button type="button" class="btn btn-info flex-fill" data-bs-toggle="modal" data-bs-target="#modifyModal">
                            <i class="ri-edit-line"></i> Modify & Execute
                        </button>
                        
                        <button type="button" class="btn btn-danger flex-fill" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="ri-close-line"></i> Reject
                        </button>
                    </div>
                    @endif
                    @endcan
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Metadata -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Metadata</h5>
                    
                    <div class="mb-3">
                        <label class="text-muted small">Decision ID</label>
                        <div><code>#{{ $decision->id }}</code></div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Type</label>
                        <div>{{ str_replace('_', ' ', ucfirst($decision->decision_type)) }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Status</label>
                        <div>
                            <span class="badge bg-{{ $decision->user_action === 'pending' ? 'warning' : ($decision->user_action === 'accepted' ? 'success' : 'danger') }}">
                                {{ ucfirst($decision->user_action) }}
                            </span>
                        </div>
                    </div>

                    @if($decision->task_id)
                    <div class="mb-3">
                        <label class="text-muted small">Related Task</label>
                        <div>
                            <a href="#" class="text-primary">
                                #{{ $decision->task_id }} - {{ $decision->task->title ?? 'N/A' }}
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($decision->project_id)
                    <div class="mb-3">
                        <label class="text-muted small">Related Project</label>
                        <div>
                            <a href="#" class="text-primary">
                                #{{ $decision->project_id }} - {{ $decision->project->title ?? 'N/A' }}
                            </a>
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="text-muted small">Created</label>
                        <div>{{ $decision->created_at->format('Y-m-d H:i') }}</div>
                        <small class="text-muted">{{ $decision->created_at->diffForHumans() }}</small>
                    </div>

                    @if($decision->executed_at)
                    <div class="mb-3">
                        <label class="text-muted small">Executed</label>
                        <div>{{ $decision->executed_at->format('Y-m-d H:i') }}</div>
                        <small class="text-muted">{{ $decision->executed_at->diffForHumans() }}</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modify Modal -->
<div class="modal fade" id="modifyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('ai.decisions.modify', $decision->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Modify Recommendation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Modified Recommendation</label>
                        <textarea class="form-control" name="modified_recommendation" rows="4" required 
                                  placeholder="Enter your modified recommendation...">{{ old('modified_recommendation', $decision->recommendation) }}</textarea>
                    </div>
                    <div class="alert alert-info">
                        <small><i class="ri-information-line"></i> The modified recommendation will be executed instead of the original.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Modify & Execute</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('ai.decisions.reject', $decision->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Decision</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason (Optional)</label>
                        <textarea class="form-control" name="rejection_reason" rows="3" 
                                  placeholder="Why are you rejecting this recommendation?"></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <small><i class="ri-alert-line"></i> This decision will be marked as rejected and will not be executed.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Decision</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
