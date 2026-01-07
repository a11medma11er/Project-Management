@extends('layouts.master')

@section('title') Create Permission @endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Management @endslot
@slot('li_2') Permissions @endslot
@slot('title') Create Permission @endslot
@endcomponent

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Add New Permission</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('management.permissions.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Permission Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required
                               placeholder="e.g., view-reports">
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <small class="text-muted">Use lowercase with hyphens. Format: action-module (e.g., view-users, edit-projects)</small>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('management.permissions.index') }}" class="btn btn-secondary">
                            <i class="ri-close-line align-middle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line align-middle"></i> Create Permission
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
