<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::withCount('users', 'permissions')->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy('group');
        return view('admin.roles.form', compact('permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
            'level' => 'required|integer|min:0|max:100',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Validate level (can't create role with level >= own level unless Super Admin)
        $this->validateRoleLevel($request->level);

        $role = Role::create([
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
            'description' => $request->description,
            'level' => $request->level,
            'is_system' => false,
        ]);

        // Sync permissions
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        ActivityLog::log('created', "Created role: {$role->name}", $role);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);
        $permissionsByModule = $role->permissions->groupBy('group');

        return view('admin.roles.show', compact('role', 'permissionsByModule'));
    }

    /**
     * Show the form for editing the role.
     */
    public function edit(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', 'System roles cannot be edited.');
        }

        $permissions = Permission::all()->groupBy('group');
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.form', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', 'System roles cannot be modified.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:500',
            'level' => 'required|integer|min:0|max:100',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Validate level
        $this->validateRoleLevel($request->level);

        $oldValues = $role->only(['name', 'description', 'level']);
        $oldPermissions = $role->permissions->pluck('id')->toArray();

        $role->update([
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
            'description' => $request->description,
            'level' => $request->level,
        ]);

        // Sync permissions
        $role->syncPermissions($request->permissions ?? []);

        ActivityLog::log('updated', "Updated role: {$role->name}", $role, [
            'role' => $oldValues,
            'permissions' => $oldPermissions,
        ], [
            'role' => $role->only(['name', 'description', 'level']),
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', 'System roles cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role with assigned users. Please reassign users first.');
        }

        ActivityLog::log('deleted', "Deleted role: {$role->name}", $role);

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Validate role level assignment.
     */
    protected function validateRoleLevel(int $level): void
    {
        $currentUser = auth()->user();

        if ($currentUser->isSuperAdmin()) {
            return;
        }

        $currentLevel = $currentUser->getPrimaryRole()?->level ?? 0;

        if ($level >= $currentLevel) {
            abort(403, 'You cannot create or modify roles with equal or higher access level than your own.');
        }
    }
}
