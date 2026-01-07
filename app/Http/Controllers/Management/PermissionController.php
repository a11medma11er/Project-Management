<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index()
    {
        $permissions = Permission::with('roles')->get()->groupBy(function($permission) {
            return explode('-', $permission->name)[1] ?? 'other';
        });
        
        return view('management.permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        return view('management.permissions.create');
    }

    /**
     * Store a newly created permission.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name|regex:/^[a-z-]+$/',
        ], [
            'name.regex' => 'Permission name must be lowercase with hyphens only (e.g., view-users)',
        ]);

        Permission::create(['name' => $request->name]);

        return redirect()->route('management.permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission)
    {
        return view('management.permissions.edit', compact('permission'));
    }

    /**
     * Update the specified permission.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id . '|regex:/^[a-z-]+$/',
        ], [
            'name.regex' => 'Permission name must be lowercase with hyphens only (e.g., view-users)',
        ]);

        $permission->update(['name' => $request->name]);

        return redirect()->route('management.permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(Permission $permission)
    {
        // Check if permission is assigned to any role
        if ($permission->roles()->count() > 0) {
            return redirect()->route('management.permissions.index')
                ->with('error', 'Cannot delete permission assigned to roles. Please remove from roles first.');
        }

        $permission->delete();

        return redirect()->route('management.permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}
