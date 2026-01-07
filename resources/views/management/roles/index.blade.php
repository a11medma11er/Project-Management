@extends('layouts.master')

@section('title') Roles Management @endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Management @endslot
@slot('title') Roles @endslot
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
                    <h5 class="card-title mb-0">Roles List</h5>
                    @can('create-roles')
                        <a href="{{ route('management.roles.create') }}" class="btn btn-primary">
                            <i class="ri-add-line align-middle"></i> Add Role
                        </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-nowrap align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Role Name</th>
                                <th scope="col">Permissions Count</th>
                                <th scope="col">Users Count</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                                <tr>
                                    <td>
                                        <span class="badge {{ $role->name === 'Super Admin' ? 'bg-danger' : 'bg-primary' }} fs-12">
                                            {{ $role->name }}
                                        </span>
                                    </td>
                                    <td>{{ $role->permissions_count }}</td>
                                    <td>{{ $role->users_count }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="ri-more-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                @if($role->name !== 'Super Admin')
                                                    @can('edit-roles')
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('management.roles.edit', $role) }}">
                                                                <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                                            </a>
                                                        </li>
                                                    @endcan
                                                    @can('delete-roles')
                                                        <li>
                                                            <form action="{{ route('management.roles.destroy', $role) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger" 
                                                                        onclick="return confirm('Are you sure you want to delete this role?')">
                                                                    <i class="ri-delete-bin-fill align-bottom me-2"></i> Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endcan
                                                @else
                                                    <li><span class="dropdown-item text-muted">Protected Role</span></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No roles found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
