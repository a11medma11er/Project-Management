@extends('layouts.master')
@section('title')
    {{ isset($task) ? 'Edit Task' : 'Create Task' }}
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Tasks
        @endslot
        @slot('title')
            {{ isset($task) ? 'Edit Task' : 'Create Task' }}
        @endslot
    @endcomponent
    
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Validation Errors:</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form action="{{ isset($task) ? route('management.tasks.update', $task) : route('management.tasks.store') }}" 
          method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($task))
            @method('PUT')
        @endif
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="task-title-input">Task Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="task-title-input" name="title" 
                               value="{{ old('title', $task->title ?? '') }}" 
                               placeholder="Enter task title" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Task Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" rows="5" 
                                  placeholder="Enter task description">{{ old('description', $task->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Project</label>
                                <select class="form-select @error('project_id') is-invalid @enderror" name="project_id">
                                    <option value="">Select Project (Optional)</option>
                                    @foreach($projects as $project)
                                    <option value="{{ $project->id }}" 
                                        {{ old('project_id', $task->project_id ?? '') == $project->id ? 'selected' : '' }}>
                                        {{ $project->title }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Client Name</label>
                                <input type="text" class="form-control @error('client_name') is-invalid @enderror" 
                                       name="client_name" 
                                       value="{{ old('client_name', $task->client_name ?? '') }}" 
                                       placeholder="Enter client name">
                                @error('client_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Due Date</label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                       name="due_date" 
                                       value="{{ old('due_date', isset($task) ? $task->due_date->format('Y-m-d') : '') }}" 
                                       required>
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                @php
                                    $statuses = App\Enums\TaskStatus::cases();
                                    $currentStatus = isset($task) ? $task->status->value : App\Enums\TaskStatus::NEW->value;
                                @endphp
                                @foreach($statuses as $status)
                                    <option value="{{ $status->value }}" 
                                        {{ (old('status', $currentStatus) == $status->value) ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                        <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                            @php
                                $priorities = App\Enums\TaskPriority::cases();
                                $currentPriority = isset($task) ? $task->priority->value : App\Enums\TaskPriority::MEDIUM->value;
                            @endphp
                            @foreach($priorities as $priority)
                                <option value="{{ $priority->value }}" 
                                    {{ (old('priority', $currentPriority) == $priority->value) ? 'selected' : '' }}>
                                    {{ $priority->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Assign Users</h5>
                </div>
                <div class="card-body">
                    @foreach($users as $user)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="assigned_users[]" 
                               value="{{ $user->id }}" id="user-{{ $user->id }}"
                               {{ isset($task) && $task->assignedUsers->contains($user->id) ? 'checked' : '' }}>
                        <label class="form-check-label" for="user-{{ $user->id }}">
                            <div class="d-flex align-items-center">
                                @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="avatar-xs rounded-circle me-2">
                                @else
                                <div class="avatar-xs me-2">
                                    <div class="avatar-title rounded-circle bg-primary">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                </div>
                                @endif
                                <span>{{ $user->name }}</span>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tags</h5>
                </div>
                <div class="card-body">
                    <input type="text" class="form-control" name="tags[]" placeholder="Enter tags (comma separated)">
                    <small class="text-muted">Example: UI/UX, Design, Dashboard</small>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-attachment-line"></i> Attachments</h5>
                </div>
                <div class="card-body">
                    <input type="file" class="form-control @error('attachments.*') is-invalid @enderror" 
                           name="attachments[]" multiple accept=".pdf,.doc,.docx,.zip,.png,.jpg,.jpeg">
                    <small class="text-muted">Max 5 files, 5MB each</small>
                    @error('attachments.*')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="ri-task-line"></i> Sub-Tasks</h5>
                    <button type="button" class="btn btn-sm btn-soft-primary" onclick="addSubTask()">
                        <i class="ri-add-line"></i> Add Sub-Task
                    </button>
                </div>
                <div class="card-body">
                    <div id="subTasksContainer">
                        <div class="input-group mb-2 subtask-item">
                            <input type="text" class="form-control" name="sub_tasks[]" placeholder="Enter sub-task title">
                            <button type="button" class="btn btn-danger" onclick="removeSubTask(this)">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                    <small class="text-muted">Add checklist items for this task</small>
                </div>
            </div>

            <div class="text-end mb-3">
                <button type="submit" class="btn btn-success w-100">
                    <i class="ri-save-line align-bottom me-1"></i> {{ isset($task) ? 'Update Task' : 'Create Task' }}
                </button>
            </div>
        </div>
    </div>
    </form>
@endsection

@section('script')
<script>
// Sub-Tasks Management
function addSubTask() {
    const container = document.getElementById('subTasksContainer');
    const newItem = document.createElement('div');
    newItem.className = 'input-group mb-2 subtask-item';
    newItem.innerHTML = `
        <input type="text" class="form-control" name="sub_tasks[]" placeholder="Enter sub-task title">
        <button type="button" class="btn btn-danger" onclick="removeSubTask(this)">
            <i class="ri-delete-bin-line"></i>
        </button>
    `;
    container.appendChild(newItem);
}

function removeSubTask(button) {
    const container = document.getElementById('subTasksContainer');
    if (container.children.length > 1) {
        button.closest('.subtask-item').remove();
    } else {
        alert('At least one sub-task field is required');
    }
}
</script>
@endsection
