@extends('admin.layouts.app')

@section('title', 'Roles & Permissions')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>Roles & Permissions</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Roles & Permissions</h1>
        <p class="page-subtitle">Manage user roles and access permissions</p>
    </div>
    <div class="page-actions">
        @can('roles.create')
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Role
        </a>
        @endcan
    </div>
</div>

<!-- Roles List -->
<div class="row">
    @forelse($roles ?? [] as $role)
    <div class="col-4">
        <div class="card mb-4">
            <div class="card-header d-flex align-center justify-between">
                <div>
                    <h3 class="card-title mb-0">{{ $role->display_name ?? $role->name }}</h3>
                    <small class="text-muted">{{ $role->name }}</small>
                </div>
                @if($role->is_system)
                    <span class="badge badge-secondary">System</span>
                @endif
            </div>
            <div class="card-body">
                @if($role->description)
                    <p class="text-muted mb-3">{{ $role->description }}</p>
                @endif

                <div class="d-flex justify-between mb-3">
                    <span class="text-muted">Users with this role:</span>
                    <strong>{{ $role->users_count ?? 0 }}</strong>
                </div>

                <div class="d-flex justify-between mb-3">
                    <span class="text-muted">Permissions:</span>
                    <strong>{{ $role->permissions_count ?? $role->permissions->count() }}</strong>
                </div>

                <div class="permission-preview mb-3">
                    @php
                        $permissionGroups = collect($role->permissions ?? [])->groupBy(function($p) {
                            return explode('.', $p->name)[0] ?? 'other';
                        })->take(3);
                    @endphp
                    @foreach($permissionGroups as $group => $perms)
                        <span class="badge badge-secondary me-1 mb-1" style="font-size: 0.65rem;">
                            {{ ucfirst($group) }} ({{ $perms->count() }})
                        </span>
                    @endforeach
                    @if($role->permissions->count() > 3)
                        <span class="text-muted" style="font-size: 0.75rem;">+{{ $role->permissions->count() - 3 }} more</span>
                    @endif
                </div>
            </div>
            <div class="card-footer d-flex justify-between">
                <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-eye"></i> View
                </a>
                @can('roles.edit')
                @if(!$role->is_system || auth()->user()->hasRole('super_admin'))
                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @endif
                @endcan
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-4">
                <i class="fas fa-user-shield fa-3x text-muted mb-3"></i>
                <p class="text-muted">No roles found</p>
                @can('roles.create')
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create First Role
                </a>
                @endcan
            </div>
        </div>
    </div>
    @endforelse
</div>

<!-- Permissions Matrix -->
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Permissions Matrix</h3>
    </div>
    <div class="card-body" style="padding: 0; overflow-x: auto;">
        <table class="table permissions-matrix">
            <thead>
                <tr>
                    <th style="position: sticky; left: 0; background: var(--gray-50); min-width: 200px;">Permission</th>
                    @foreach($roles ?? [] as $role)
                        <th class="text-center" style="min-width: 100px;">{{ $role->display_name ?? $role->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($permissionGroups ?? [] as $group => $permissions)
                    <tr class="group-header">
                        <td colspan="{{ count($roles ?? []) + 1 }}" style="background: var(--gray-100); font-weight: 600;">
                            <i class="fas fa-folder me-2"></i>{{ ucwords(str_replace(['_', '-'], ' ', $group)) }}
                        </td>
                    </tr>
                    @foreach($permissions as $permission)
                        <tr>
                            <td style="position: sticky; left: 0; background: white; padding-left: 2rem;">
                                {{ $permission->display_name ?? ucwords(str_replace(['_', '-', '.'], ' ', $permission->name)) }}
                            </td>
                            @foreach($roles ?? [] as $role)
                                <td class="text-center">
                                    @if($role->permissions->contains('id', $permission->id))
                                        <i class="fas fa-check text-success"></i>
                                    @else
                                        <i class="fas fa-times text-muted" style="opacity: 0.3;"></i>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
.permissions-matrix {
    font-size: 0.875rem;
}

.permissions-matrix th,
.permissions-matrix td {
    padding: 0.5rem 0.75rem;
    border: 1px solid var(--gray-200);
}

.permissions-matrix .group-header td {
    padding: 0.75rem 1rem;
}

.permission-preview {
    min-height: 28px;
}
</style>
@endsection
