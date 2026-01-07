@extends('layouts.master')

@section('title') {{ $prompt->name }} - AI Prompt @endsection

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    <code>{{ $prompt->name }}</code>
                    <span class="badge bg-{{ $prompt->is_active ? 'success' : 'secondary' }} ms-2">
                        {{ $prompt->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </h4>

                <div class="page-title-right">
                    <div class="btn-group" role="group">
                        <a href="{{ route('ai.prompts.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line"></i> Back
                        </a>
                        @can('manage-ai-prompts')
                        <a href="{{ route('ai.prompts.edit', $prompt->id) }}" class="btn btn-primary">
                            <i class="ri-edit-line"></i> Edit
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Template</h5>
                    <pre class="bg-dark text-white p-3 rounded"><code>{{ $prompt->template }}</code></pre>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Version History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Version</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($history as $version)
                                <tr class="{{ $version->id == $prompt->id ? 'table-primary' : '' }}">
                                    <td>
                                        <code>v{{ $version->version }}</code>
                                        @if($version->id == $prompt->id)
                                        <span class="badge bg-primary">Current</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $version->is_active ? 'success' : 'secondary' }}">
                                            {{ $version->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ $version->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="{{ route('ai.prompts.show', $version->id) }}" class="btn btn-sm btn-soft-primary">
                                            <i class="ri-eye-line"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Details</h5>
                    
                    <div class="mb-3">
                        <label class="text-muted small">Type</label>
                        <div>
                            <span class="badge bg-soft-primary text-primary">
                                {{ ucfirst($prompt->type) }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Version</label>
                        <div><code>{{ $prompt->version }}</code></div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Usage Count</label>
                        <div>{{ number_format($prompt->usage_count) }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Created By</label>
                        <div>{{ $prompt->creator->name ?? 'Unknown' }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Created At</label>
                        <div>{{ $prompt->created_at->format('Y-m-d H:i') }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Last Updated</label>
                        <div>{{ $prompt->updated_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>

            @if($prompt->variables && count($prompt->variables) > 0)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Variables</h5>
                    @foreach($prompt->variables as $variable)
                    <code class="d-block mb-1">{{{{ $variable }}}}</code>
                    @endforeach
                </div>
            </div>
            @endif

            @if($prompt->description)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Description</h5>
                    <p class="text-muted">{{ $prompt->description }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
