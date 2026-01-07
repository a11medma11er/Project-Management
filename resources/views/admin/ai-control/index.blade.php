@extends('layouts.master')

@section('title') AI Control Panel @endsection

@section('css')
<style>
.ai-card {
    transition: all 0.3s ease;
    border-left: 4px solid #5b73e8;
}
.ai-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.status-badge {
    font-size: 0.875rem;
    padding: 0.35rem 0.75rem;
}
.metric-value {
    font-size: 2rem;
    font-weight: 700;
    color: #5b73e8;
}
.ai-toggle {
    transform: scale(1.5);
}
.health-indicator {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-right: 5px;
}
.health-good { background-color: #34c38f; }
.health-warning { background-color: #f1b44c; }
.health-critical { background-color: #f46a6a; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    <i class="ri-robot-line"></i> AI Control Panel
                </h4>

                <!-- AI System Toggle -->
                <div class="page-title-right">
                    <div class="form-check form-switch form-switch-lg">
                        <input class="form-check-input ai-toggle" type="checkbox" 
                               id="aiSystemToggle"
                               @if($ai_enabled) checked @endif
                               onchange="toggleAISystem(this.checked)">
                        <label class="form-check-label ms-2" for="aiSystemToggle">
                            <strong>AI System {{ $ai_enabled ? 'Enabled' : 'Disabled' }}</strong>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card ai-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Total Decisions</p>
                            <h4 class="metric-value mb-0">{{ number_format($total_decisions) }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-primary">
                                    <i class="ri-brain-line font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card ai-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Pending Review</p>
                            <h4 class="metric-value mb-0 text-warning">{{ $pending_decisions }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center ">
                            <div class="avatar-sm rounded-circle bg-warning mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-warning">
                                    <i class="ri-time-line font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card ai-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Acceptance Rate</p>
                            <h4 class="metric-value mb-0 text-success">{{ number_format($acceptance_rate, 1) }}%</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="avatar-sm rounded-circle bg-success mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-success">
                                    <i class="ri-check-line font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card ai-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Avg. Confidence</p>
                            <h4 class="metric-value mb-0">{{ number_format($avg_confidence * 100, 1) }}%</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="avatar-sm rounded-circle bg-info mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-info">
                                    <i class="ri-pie-chart-line font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- System Health -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="ri-heart-pulse-line"></i> System Health
                    </h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Response Time:</span>
                            <span class="badge bg-{{ $system_health['response_time'] < 2000 ? 'success' : ($system_health['response_time'] < 3000 ? 'warning' : 'danger') }} status-badge">
                                {{ $system_health['response_time'] }}ms
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Fallback Rate:</span>
                            <span class="badge bg-{{ $system_health['fallback_rate'] < 5 ? 'success' : 'warning' }} status-badge">
                                {{ $system_health['fallback_rate'] }}%
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Errors (24h):</span>
                            <span class="badge bg-{{ $system_health['error_count_24h'] === 0 ? 'success' : 'danger' }} status-badge">
                                {{ $system_health['error_count_24h'] }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Last Decision:</span>
                            <span class="text-muted small">{{ $system_health['last_decision'] }}</span>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        @php
                            $isHealthy = $system_health['response_time'] < 2000 && $system_health['fallback_rate'] < 5 && $system_health['error_count_24h'] === 0;
                        @endphp
                        <span class="health-indicator health-{{ $isHealthy ? 'good' : 'warning' }}"></span>
                        <strong>{{ $isHealthy ? 'All Systems Operational' : 'Performance Degraded' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent AI Activity -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="ri-history-line"></i> Recent AI Activity
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Related To</th>
                                    <th>Confidence</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_activity as $decision)
                                <tr>
                                    <td>
                                        <span class="badge bg-soft-primary text-primary">
                                            {{ str_replace('_', ' ', ucfirst($decision->decision_type)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($decision->task)
                                            <i class="ri-task-line"></i> {{ Str::limit($decision->task->title, 30) }}
                                        @elseif($decision->project)
                                            <i class="ri-folder-3-line"></i> {{ Str::limit($decision->project->title, 30) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 8px; width: 60px;">
                                            <div class="progress-bar bg-{{ $decision->confidence_score >= 0.7 ? 'success' : ($decision->confidence_score >= 0.5 ? 'warning' : 'danger') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $decision->confidence_score * 100 }}%">
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ number_format($decision->confidence_score * 100) }}%</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $decision->user_action === 'accepted' ? 'success' : 
                                            ($decision->user_action === 'rejected' ? 'danger' : 
                                            ($decision->user_action === 'modified' ? 'info' : 'warning')) 
                                        }}">
                                            {{ ucfirst($decision->user_action) }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $decision->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="ri-inbox-line font-size-24 d-block mb-2"></i>
                                        No AI activity yet
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($recent_activity->count() > 0)
                    <div class="mt-3 text-end">
                        <a href="{{ route('ai.decisions.index') }}" class="btn btn-sm btn-soft-primary">
                            View All Decisions <i class="ri-arrow-right-line ms-1"></i>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Review Widget -->
    <div class="row">
        @include('components.ai-quick-review')
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="ri-flashlight-line"></i> Quick Actions
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @can('view-ai-decisions')
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('ai.decisions.index') }}" class="btn btn-soft-primary w-100">
                                <i class="ri-eye-line"></i> View All Decisions
                            </a>
                        </div>
                        @endcan

                        @can('manage-ai-prompts')
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('ai.prompts.index') }}" class="btn btn-soft-info w-100">
                                <i class="ri-edit-line"></i> Manage Prompts
                            </a>
                        </div>
                        @endcan

                        @can('manage-ai-settings')
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('ai.settings.index') }}" class="btn btn-soft-warning w-100">
                                <i class="ri-settings-3-line"></i> AI Settings
                            </a>
                        </div>
                        @endcan

                        @can('view-ai-analytics')
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('ai.analytics.index') }}" class="btn btn-soft-success w-100">
                                <i class="ri-bar-chart-line"></i> Analytics
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
function toggleAISystem(enabled) {
    const toggle = document.getElementById('aiSystemToggle');
    toggle.disabled = true;

    axios.post('{{ route("ai.control.toggle") }}', {
        enabled: enabled,
        _token: '{{ csrf_token() }}'
    })
    .then(response => {
        toastr.success(response.data.message);
        setTimeout(() => {
            location.reload();
        }, 1000);
    })
    .catch(error => {
        console.error('Toggle failed:', error);
        toggle.checked = !enabled; // Revert toggle
        toastr.error('Failed to toggle AI system. Please try again.');
    })
    .finally(() => {
        toggle.disabled = false;
    });
}

// Auto-refresh system health every 30 seconds
setInterval(() => {
    axios.get('{{ route("ai.control.health") }}')
        .then(response => {
            console.log('Health check:', response.data);
            // Optional: Update health indicators without page reload
        })
        .catch(error => {
            console.error('Health check failed:', error);
        });
}, 30000);
</script>
@endsection
