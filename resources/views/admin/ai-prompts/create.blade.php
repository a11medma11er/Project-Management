@extends('layouts.master')

@section('title') Create AI Prompt @endsection

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    <i class="ri-add-line"></i> Create New Prompt
                </h4>

                <div class="page-title-right">
                    <a href="{{ route('ai.prompts.index') }}" class="btn btn-secondary">
                        <i class="ri-arrow-left-line"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('ai.prompts.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Prompt Details</h5>

                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Name <span class="text-danger">*</span>
                                <small class="text-muted">(lowercase, alphanumeric, dash, and underscore only)</small>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   placeholder="task_analysis_prompt"
                                   pattern="[a-z0-9_-]+"
                                   required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Unique identifier for this prompt</small>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select Type...</option>
                                <option value="system" {{ old('type') == 'system' ? 'selected' : '' }}>System</option>
                                <option value="user" {{ old('type') == 'user' ? 'selected' : '' }}>User</option>
                                <option value="assistant" {{ old('type') == 'assistant' ? 'selected' : '' }}>Assistant</option>
                            </select>
                            @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3"
                                      maxlength="1000"
                                      placeholder="Brief description of what this prompt does...">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="template" class="form-label">
                                Template <span class="text-danger">*</span>
                                <small class="text-muted">(Use {{variable}} syntax for variables)</small>
                            </label>
                            <textarea class="form-control @error('template') is-invalid @enderror" 
                                      id="template" 
                                      name="template" 
                                      rows="15"
                                      required>{{ old('template', 'Analyze the following task:\n\nTitle: {{task_title}}\nDescription: {{task_description}}\nStatus: {{status}}\nPriority: {{priority}}\nDue Date: {{due_date}}\n\nProvide analysis including:\n1. Urgency assessment\n2. Suggested priority level\n3. Required actions\n4. Potential blockers') }}</textarea>
                            @error('template')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="variables-list" class="mt-2"></div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line"></i> Create Prompt
                            </button>
                            <button type="button" id="test-btn" class="btn btn-soft-info">
                                <i class="ri-flask-line"></i> Quick Test
                            </button>
                            <a href="{{ route('ai.prompts.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/markdown/markdown.min.js"></script>
<script>
let editor = CodeMirror.fromTextArea(document.getElementById('template'), {
    mode: 'markdown',
    theme: 'monokai',
    lineNumbers: true,
    lineWrapping: true,
    height: '400px'
});

// Extract and display variables
function updateVariables() {
    const template = editor.getValue();
    const regex = /\{\{([^}]+)\}\}/g;
    const matches = [...template.matchAll(regex)];
    const variables = [...new Set(matches.map(m => m[1]))];
    
    const list = document.getElementById('variables-list');
    if (variables.length > 0) {
        list.innerHTML = '<div class="alert alert-info"><strong>Detected Variables:</strong> ' + 
            variables.map(v => '<code>{{' + v + '}}</code>').join(', ') + '</div>';
    } else {
        list.innerHTML = '';
    }
}

editor.on('change', updateVariables);
updateVariables();

// Quick test
document.getElementById('test-btn').addEventListener('click', function() {
    alert('Test functionality coming in next version!');
});
</script>
@endsection
