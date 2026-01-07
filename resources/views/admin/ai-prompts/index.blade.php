@extends('layouts.master')

@section('title') AI Prompts @endsection

@section('css')
<style>
.prompt-card {
    transition: all 0.3s ease;
    border-left: 4px solid #5b73e8;
}
.prompt-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.version-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
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
                    <i class="ri-edit-box-line"></i> AI Prompts Management
                </h4>

                <div class="page-title-right">
                    @can('manage-ai-prompts')
                    <a href="{{ route('ai.prompts.create') }}" class="btn btn-primary">
                        <i class="ri-add-line"></i> Create New Prompt
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('ai.prompts.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" onchange="this.form.submit()">
                                <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Types</option>
                                <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>System</option>
                                <option value="user" {{ request('type') == 'user' ? 'selected' : '' }}>User</option>
                                <option value="assistant" {{ request('type') == 'assistant' ? 'selected' : '' }}>Assistant</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="active" class="form-select" onchange="this.form.submit()">
                                <option value="">All</option>
                                <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Search</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search by name or description..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="ri-search-line"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Prompts List -->
    <div class="row">
        @forelse($prompts as $prompt)
        <div class="col-md-6 col-xl-4">
            <div class="card prompt-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title mb-0">
                            <code>{{ $prompt->name }}</code>
                        </h5>
                        <span class="badge bg-{{ $prompt->is_active ? 'success' : 'secondary' }}">
                            {{ $prompt->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <p class="text-muted small mb-2">
                        {{ Str::limit($prompt->description, 100) }}
                    </p>

                    <div class="mb-3">
                        <span class="badge bg-soft-primary text-primary me-1">
                            <i class="ri-code-line"></i> {{ ucfirst($prompt->type) }}
                        </span>
                        <span class="badge bg-soft-info text-info version-badge">
                            v{{ $prompt->version }}
                        </span>
                        <span class="badge bg-soft-secondary text-secondary">
                            <i class="ri-bar-chart-line"></i> {{ $prompt->usage_count }} uses
                        </span>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('ai.prompts.show', $prompt->id) }}" class="btn btn-sm btn-soft-primary flex-fill">
                            <i class="ri-eye-line"></i> View
                        </a>
                        @can('manage-ai-prompts')
                        <a href="{{ route('ai.prompts.edit', $prompt->id) }}" class="btn btn-sm btn-soft-info">
                            <i class="ri-edit-line"></i>
                        </a>
                        @endcan
                    </div>

                    <div class="text-muted small mt-2">
                        Updated {{ $prompt->updated_at->diffForHumans() }}
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="ri-inbox-line font-size-48 text-muted d-block mb-3"></i>
                    <h5 class="text-muted">No prompts found</h5>
                    @can('manage-ai-prompts')
                    <a href="{{ route('ai.prompts.create') }}" class="btn btn-primary mt-3">
                        <i class="ri-add-line"></i> Create Your First Prompt
                    </a>
                    @endcan
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($prompts->hasPages())
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $prompts->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
