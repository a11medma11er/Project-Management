@extends('layouts.master')

@section('title') Create Role @endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Management @endslot
@slot('li_2') Roles @endslot
@slot('title') Create Role @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Add New Role</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('management.roles.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <div class="border rounded p-3">
                            @foreach($permissions as $module => $modulePermissions)
                                <div class="mb-3">
                                    <h6 class="text-uppercase text-muted">{{ ucfirst($module) }} Management</h6>
                                    <div class="row">
                                        @foreach($modulePermissions as $permission)
                                            <div class="col-md-3">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" 
                                                           value="{{ $permission->name }}" id="perm-{{ $permission->id }}"
                                                           {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm-{{ $permission->id }}">
                                                        {{ str_replace('-', ' ', ucfirst($permission->name)) }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('management.roles.index') }}" class="btn btn-secondary">
                            <i class="ri-close-line align-middle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line align-middle"></i> Create Role
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
