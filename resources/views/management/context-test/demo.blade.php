@extends('layouts.master')
@section('title') Context Readiness Demo @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Management @endslot
        @slot('title') Context Readiness Demo @endslot
    @endcomponent

    @if(isset($error))
        <div class="alert alert-warning">
            <i class="ri-alert-line me-2"></i> {{ $error }}
        </div>
    @else
        {{-- Task Info --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h5 class="card-title text-white mb-0">
                            <i class="ri-task-line me-2"></i> Task: {{ $task->title }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th width="150">ID:</th>
                                        <td>{{ $task->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td><x-task-badge :value="$task->status" type="status" /></td>
                                    </tr>
                                    <tr>
                                        <th>Priority:</th>
                                        <td><x-task-badge :value="$task->priority" type="priority" /></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th width="150">Due Date:</th>
                                        <td>{{ $task->due_date?->format('Y-m-d') ?? 'Not set' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Overdue:</th>
                                        <td>
                                            @if($task->isOverdue())
                                                <span class="badge bg-danger">Yes ({{ $task->getDaysOverdue() }} days)</span>
                                            @else
                                                <span class="badge bg-success">No</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Urgency:</th>
                                        <td><span class="badge bg-warning">{{ $task->getUrgencyLevel() }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Context Data --}}
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info">
                        <h5 class="card-title text-white mb-0">
                            <i class="ri-database-2-line me-2"></i> Context Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3"><strong>Summary:</strong> {{ $context->getSummary() }}</p>
                        <p class="mb-0"><strong>Ready for AI:</strong> 
                            @if($context->isReady())
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-danger">No</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success">
                        <h5 class="card-title text-white mb-0">
                            <i class="ri-robot-line me-2"></i> AI Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <th width="150">Enabled:</th>
                                <td>
                                    @if($aiStatus['enabled'])
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Available:</th>
                                <td>
                                    @if($aiStatus['available'])
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-warning">No (System still works!)</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Context Details --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ri-code-s-slash-line me-2"></i> Full Context Data (JSON)
                        </h5>
                    </div>
                    <div class="card-body">
                        <pre class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;"><code>{{ $context->toJson() }}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Activities --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ri-history-line me-2"></i> Recent Activities ({{ $activities->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($activities->isEmpty())
                            <p class="text-muted text-center">No activities yet. Make some changes to the task to see activity logging in action!</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Time</th>
                                            <th>Event</th>
                                            <th>User</th>
                                            <th>Changes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activities->take(10) as $activity)
                                            <tr>
                                                <td><small>{{ $activity->created_at->diffForHumans() }}</small></td>
                                                <td><span class="badge bg-primary">{{ $activity->description }}</span></td>
                                                <td>{{ $activity->causer?->name ?? 'System' }}</td>
                                                <td>
                                                    @if($activity->properties->has('attributes'))
                                                        <small class="text-muted">
                                                            {{ count($activity->properties->get('attributes')) }} fields
                                                        </small>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- API Endpoints --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark">
                        <h5 class="card-title text-white mb-0">
                            <i class="ri-terminal-box-line me-2"></i> Test API Endpoints
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">Test these endpoints in your browser or API client:</p>
                        <div class="list-group">
                            <a href="{{ route('management.context-test.builder', $task) }}" target="_blank" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Context Builder</h6>
                                    <small class="text-muted">GET</small>
                                </div>
                                <p class="mb-1"><code>{{ route('management.context-test.builder', $task) }}</code></p>
                                <small>Returns full context data for this task</small>
                            </a>
                            <a href="{{ route('management.context-test.activities', $task) }}" target="_blank" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Activity Service</h6>
                                    <small class="text-muted">GET</small>
                                </div>
                                <p class="mb-1"><code>{{ route('management.context-test.activities', $task) }}</code></p>
                                <small>Returns all activities for this task</small>
                            </a>
                            <a href="{{ route('management.context-test.ai-status') }}" target="_blank" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">AI Status</h6>
                                    <small class="text-muted">GET</small>
                                </div>
                                <p class="mb-1"><code>{{ route('management.context-test.ai-status') }}</code></p>
                                <small>Returns AI gateway status and configuration</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
