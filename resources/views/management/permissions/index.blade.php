@extends('layouts.master')

@section('title') Permissions Management @endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Management @endslot
@slot('title') Permissions @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Permissions List</h5>
                    @can('create-permissions')
                        <a href="{{ route('management.permissions.create') }}" class="btn btn-primary">
                            <i class="ri-add-line align-middle"></i> Add Permission
                        </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="accordion" id="permissionsAccordion">
                    @foreach($permissions as $module => $modulePermissions)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-{{ $module }}">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#collapse-{{ $module }}">
                                    <strong class="text-uppercase">{{ ucfirst($module) }} Management</strong>
                                    <span class="badge bg-info ms-2">{{ count($modulePermissions) }} permissions</span>
                                </button>
                            </h2>
                            <div id="collapse-{{ $module }}" class="accordion-collapse collapse show">
                                <div class="accordion-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Permission Name</th>
                                                    <th>Assigned to Roles</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($modulePermissions as $permission)
                                                    <tr>
                                                        <td>
                                                            <code>{{ $permission->name }}</code>
                                                        </td>
                                                        <td>
                                                            @foreach($permission->roles as $role)
                                                                <span class="badge bg-primary me-1">{{ $role->name }}</span>
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                @can('edit-permissions')
                                                                    <a href="{{ route('management.permissions.edit', $permission) }}" 
                                                                       class="btn btn-soft-info">
                                                                        <i class="ri-pencil-fill"></i>
                                                                    </a>
                                                                @endcan
                                                                @can('delete-permissions')
                                                                    <form action="{{ route('management.permissions.destroy', $permission) }}" 
                                                                          method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-soft-danger"
                                                                                onclick="return confirm('Delete this permission?')">
                                                                            <i class="ri-delete-bin-fill"></i>
                                                                        </button>
                                                                    </form>
                                                                @endcan
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
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
