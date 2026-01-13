@extends('layouts.master')

@section('title') AI Prompts @endsection

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />
<style>
.prompt-card {
    transition: all 0.3s ease;
    border-left: 4px solid #5b73e8;
}
.prompt-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.prompt-card.has-category {
    border-left-width: 4px;
}
.version-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}
.category-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.35rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.8rem;
    font-weight: 500;
}
.tag-badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.5rem;
    margin: 0.1rem;
}
.select2-container--default .select2-selection--multiple {
    border: 1px solid #ced4da;
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
                    <form method="GET" action="{{ route('ai.prompts.index') }}" class="row g-3" id="filterForm">
                        <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select" onchange="this.form.submit()">
                                <option value="all" {{ request('category') == 'all' || !request('category') ? 'selected' : '' }}>All Categories</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" onchange="this.form.submit()">
                                <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Types</option>
                                <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>System</option>
                                <option value="user" {{ request('type') == 'user' ? 'selected' : '' }}>User</option>
                                <option value="assistant" {{ request('type') == 'assistant' ? 'selected' : '' }}>Assistant</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="active" class="form-select" onchange="this.form.submit()">
                                <option value="">All</option>
                                <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search by name or description..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="ri-search-line"></i>
                                </button>
                                @if(request()->hasAny(['category', 'type', 'active', 'search', 'tags']))
                                <a href="{{ route('ai.prompts.index') }}" class="btn btn-secondary">
                                    <i class="ri-refresh-line"></i> Clear
                                </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Filter by Tags</label>
                            <select name="tags[]" class="form-select select2-tags" multiple>
                                @foreach($tags as $tag)
                                <option value="{{ $tag->id }}" {{ in_array($tag->id, (array)request('tags', [])) ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                                @endforeach
                            </select>
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
            <div class="card prompt-card {{ $prompt->category ? 'has-category' : '' }}" 
                 style="border-left-color: {{ $prompt->category->color ?? '#5b73e8' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title mb-0" style="word-break: break-word; max-width: 75%; font-size: 0.9rem;">
                            <code style="word-break: break-all;">{{ $prompt->name }}</code>
                        </h5>
                        <span class="badge bg-{{ $prompt->is_active ? 'success' : 'secondary' }}">
                            {{ $prompt->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    @if($prompt->category)
                    <div class="mb-2">
                        <span class="category-badge" style="background: {{ $prompt->category->color }}20; color: {{ $prompt->category->color }}">
                            <i class="{{ $prompt->category->icon }}"></i>
                            {{ $prompt->category->name }}
                        </span>
                    </div>
                    @endif

                    {{-- Extract UI Location from description (📍 line) --}}
                    @php
                        $description = $prompt->description ?? '';
                        $uiLocation = '';
                        $purpose = '';
                        
                        // Extract 📍 line for UI location
                        if (preg_match('/📍\s*(.+?)(?:\n|🎯|$)/s', $description, $locationMatch)) {
                            $uiLocation = trim($locationMatch[1]);
                        }
                        
                        // Extract 🎯 line for purpose
                        if (preg_match('/🎯\s*PURPOSE:\s*(.+?)(?:\n|📤|$)/s', $description, $purposeMatch)) {
                            $purpose = trim($purposeMatch[1]);
                        }
                        
                        // Fallback to original description if no special format
                        if (empty($uiLocation) && empty($purpose)) {
                            $purpose = Str::limit($description, 100);
                        }
                    @endphp

                    @if($uiLocation)
                    <div class="mb-2">
                        <span class="badge bg-soft-warning text-warning" style="font-size: 0.7rem; padding: 0.3rem 0.5rem; display: inline-block; white-space: normal; word-wrap: break-word; text-align: left; max-width: 100%;">
                            <i class="ri-map-pin-line"></i> {{ Str::limit($uiLocation, 50) }}
                        </span>
                    </div>
                    @endif

                    @if($purpose)
                    <p class="text-muted small mb-2">
                        {{ Str::limit($purpose, 100) }}
                    </p>
                    @endif

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
                        @if($prompt->is_system)
                        <span class="badge bg-danger text-white">
                            <i class="ri-shield-star-line"></i> CORE SYSTEM
                        </span>
                        @endif
                    </div>

                    @if($prompt->tags->count() > 0)
                    <div class="mb-3">
                        @foreach($prompt->tags->take(3) as $tag)
                        <span class="badge tag-badge" style="background: {{ $tag->color }}20; color: {{ $tag->color }}">
                            {{ $tag->name }}
                        </span>
                        @endforeach
                        @if($prompt->tags->count() > 3)
                        <span class="badge tag-badge bg-light text-dark">
                            +{{ $prompt->tags->count() - 3 }} more
                        </span>
                        @endif
                    </div>
                    @endif

                    <div class="d-flex gap-2">
                        <a href="{{ route('ai.prompts.show', $prompt->id) }}" class="btn btn-sm btn-soft-primary flex-fill">
                            <i class="ri-eye-line"></i> View
                        </a>
                        @can('manage-ai-prompts')
                        <a href="{{ route('ai.prompts.edit', $prompt->id) }}" class="btn btn-sm btn-soft-info">
                            <i class="ri-edit-line"></i>
                        </a>
                        @if(!$prompt->is_system)
                        <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmDelete({{ $prompt->id }}, '{{ $prompt->name }}')">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                        @else
                        <button type="button" class="btn btn-sm btn-secondary" disabled title="System prompts cannot be deleted">
                            <i class="ri-lock-line"></i>
                        </button>
                        @endif
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
                    <p class="text-muted">Try adjusting your filters or search criteria</p>
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

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// Delete confirmation function (MUST be in global scope for onclick to work)
function confirmDelete(promptId, promptName) {
    Swal.fire({
        title: 'Delete Prompt?',
        html: `Are you sure you want to delete <strong>${promptName}</strong>?<br><small class="text-muted">This action cannot be undone.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="ri-delete-bin-line"></i> Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create and submit delete form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/ai/prompts/${promptId}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

$(document).ready(function() {
    // Initialize Select2 for tags
    $('.select2-tags').select2({
        placeholder: 'Select tags to filter...',
        allowClear: true,
        width: '100%'
    }).on('change', function() {
        $('#filterForm').submit();
    });
});
</script>
@endsection
