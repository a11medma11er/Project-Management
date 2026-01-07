@extends('layouts.master')
@section('title') Activity Details @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') <a href="{{ route('management.activity-logs.index') }}">Activity Logs</a> @endslot
        @slot('title') Activity Details @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Activity Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="200">Event:</th>
                            <td>
                                <span class="badge bg-primary">{{ $activity->description }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Log Name:</th>
                            <td><span class="badge bg-info">{{ $activity->log_name }}</span></td>
                        </tr>
                        <tr>
                            <th>Time:</th>
                            <td>{{ $activity->created_at->format('Y-m-d H:i:s') }} ({{ $activity->created_at->diffForHumans() }})</td>
                        </tr>
                        <tr>
                            <th>Causer:</th>
                            <td>
                                @if($activity->causer)
                                    {{ $activity->causer->name }} (ID: {{ $activity->causer_id }})
                                @else
                                    <span class="text-muted">System</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Subject:</th>
                            <td>
                                @if($activity->subject)
                                    {{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}
                                @else
                                    <span class="text-muted">No subject</span>
                                @endif
                            </td>
                        </tr>
                        @if($activity->batch_uuid)
                        <tr>
                            <th>Batch UUID:</th>
                            <td><code>{{ $activity->batch_uuid }}</code></td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            @if($activity->properties && $activity->properties->isNotEmpty())
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Properties</h5>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded"><code>{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</code></pre>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('management.activity-logs.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line"></i> Back to List
                        </a>
                        @if($activity->subject)
                            @if($activity->subject_type === 'App\Models\Task')
                                <a href="{{ route('management.tasks.show', $activity->subject_id) }}" class="btn btn-primary">
                                    <i class="ri-eye-line"></i> View Task
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            @if($activity->properties->has('context'))
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Context</h5>
                </div>
                <div class="card-body">
                    @php $context = $activity->properties->get('context'); @endphp
                    @if(is_array($context))
                        <ul class="list-unstyled mb-0">
                            @foreach($context as $key => $value)
                                <li class="mb-2">
                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                    <span class="text-muted">{{ is_bool($value) ? ($value ? 'Yes' : 'No') : $value }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
