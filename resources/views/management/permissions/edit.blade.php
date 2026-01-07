@extends('layouts.master')

@section('title') Edit Permission @endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Management @endslot
@slot('li_2') Permissions @endslot
@slot('title') Edit Permission @endslot
@endcomponent

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Permission</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('management.permissions.update', $permission) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Permission Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $permission->name) }}" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <small class="text-muted">Use lowercase with hyphens. Format: action-module</small>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('management.permissions.index') }}" class="btn btn-secondary">
                            <i class="ri-close-line align-middle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line align-middle"></i> Update Permission
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
