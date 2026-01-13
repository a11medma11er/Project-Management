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
                                    <th>Description</th>
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
                                        <small class="text-muted">{{ \Illuminate\Support\Str::limit($version->description, 40) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $version->is_active ? 'success' : 'secondary' }}">
                                            {{ $version->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ $version->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('ai.prompts.show', $version->id) }}" class="btn btn-soft-primary" title="View">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            
                                            @if(!$version->is_active)
                                            <form action="{{ route('ai.prompts.activate', $version->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-soft-success" title="Activate this version" onclick="return confirm('Activate v{{$version->version}}? This will switch the system to use this prompt.')">
                                                    <i class="ri-checkbox-circle-line"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
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

                    @if($prompt->category)
                    <div class="mb-3">
                        <label class="text-muted small">Category</label>
                        <div>
                            <span class="badge" style="background: {{ $prompt->category->color }}20; color: {{ $prompt->category->color }}; padding: 0.5rem 0.75rem;">
                                <i class="{{ $prompt->category->icon }}"></i>
                                {{ $prompt->category->name }}
                            </span>
                        </div>
                    </div>
                    @endif

                    @if($prompt->tags->count() > 0)
                    <div class="mb-3">
                        <label class="text-muted small">Tags</label>
                        <div>
                            @foreach($prompt->tags as $tag)
                            <span class="badge mb-1" style="background: {{ $tag->color }}20; color: {{ $tag->color }}; padding: 0.35rem 0.6rem;">
                                {{ $tag->name }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

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
                    <code class="d-block mb-1">@{{ '{{' . $variable . '}}' }}</code>
                    @endforeach
                </div>
            </div>
            @endif

            @if($prompt->description)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Documentation</h5>
                    
                    @php
                        $desc = $prompt->description ?? '';
                        
                        // Extract sections
                        preg_match('/📍\s*(.+?)(?=🎯|📤|⚠️|$)/s', $desc, $locationMatch);
                        preg_match('/🎯\s*PURPOSE:\s*(.+?)(?=📤|⚠️|$)/s', $desc, $purposeMatch);
                        preg_match('/📤\s*EXPECTED RESPONSE FORMAT:\s*(.+?)(?=⚠️|$)/s', $desc, $formatMatch);
                        preg_match('/⚠️\s*(?:CRITICAL )?EDITING (?:TIPS|RULES):\s*(.+)$/s', $desc, $tipsMatch);
                        
                        $location = trim($locationMatch[1] ?? '');
                        $purpose = trim($purposeMatch[1] ?? '');
                        $format = trim($formatMatch[1] ?? '');
                        $tips = trim($tipsMatch[1] ?? '');
                    @endphp

                    @if($location)
                    <div class="alert alert-warning mb-3" style="border-left: 4px solid #f1b44c;">
                        <strong><i class="ri-map-pin-line"></i> UI Location:</strong>
                        <div class="mt-1">{{ $location }}</div>
                    </div>
                    @endif

                    @if($purpose)
                    <div class="alert alert-info mb-3" style="border-left: 4px solid #50a5f1;">
                        <strong><i class="ri-focus-line"></i> Purpose:</strong>
                        <div class="mt-1">{{ $purpose }}</div>
                    </div>
                    @endif

                    @if($format)
                    <div class="alert alert-success mb-3" style="border-left: 4px solid #34c38f;">
                        <strong><i class="ri-file-code-line"></i> Expected Response Format:</strong>
                        <pre class="mt-2 mb-0 bg-light p-2 rounded" style="white-space: pre-wrap; font-size: 0.85rem;">{{ $format }}</pre>
                    </div>
                    @endif

                    @if($tips)
                    <div class="alert alert-danger mb-0" style="border-left: 4px solid #f46a6a;">
                        <strong><i class="ri-error-warning-line"></i> Editing Rules:</strong>
                        <pre class="mt-2 mb-0" style="white-space: pre-wrap; font-size: 0.85rem; color: inherit;">{{ $tips }}</pre>
                    </div>
                    @endif

                    @if(!$location && !$purpose && !$format && !$tips)
                    <p class="text-muted mb-0">{{ $desc }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
