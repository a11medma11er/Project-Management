@extends('layouts.master')

@section('title') Edit {{ $prompt->name }} @endsection

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
                    <i class="ri-edit-line"></i> Edit Prompt: <code>{{ $prompt->name }}</code>
                </h4>

                <div class="page-title-right">
                    <a href="{{ route('ai.prompts.show', $prompt->id) }}" class="btn btn-secondary">
                        <i class="ri-arrow-left-line"></i> Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="ri-information-line"></i>
        <strong>Note:</strong> Editing this prompt will create a new version (current: v{{ $prompt->version }})
    </div>

    <form action="{{ route('ai.prompts.update', $prompt->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Template</h5>

                        <div class="mb-3">
                            <label for="template" class="form-label">
                                Template Content <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('template') is-invalid @enderror" 
                                      id="template" 
                                      name="template" 
                                      rows="15"
                                      required>{{ old('template', $prompt->template) }}</textarea>
                            @error('template')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="variables-list" class="mt-2"></div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3"
                                      maxlength="1000">{{ old('description', $prompt->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="version_type" class="form-label">Version Increment <span class="text-danger">*</span></label>
                            <select class="form-select @error('version_type') is-invalid @enderror" id="version_type" name="version_type" required>
                                <option value="patch" {{ old('version_type') == 'patch' ? 'selected' : '' }}>Patch (Minor bug fixes)</option>
                                <option value="minor" {{ old('version_type', 'minor') == 'minor' ? 'selected' : '' }}>Minor (New features)</option>
                                <option value="major" {{ old('version_type') == 'major' ? 'selected' : '' }}>Major (Breaking changes)</option>
                            </select>
                            @error('version_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Current version: <code>{{ $prompt->version }}</code>
                            </small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line"></i> Save New Version
                            </button>
                            <a href="{{ route('ai.prompts.show', $prompt->id) }}" class="btn btn-secondary">
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
</script>
@endsection
