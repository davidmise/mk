@extends('admin.layouts.app')

@section('title', 'Role - ' . $role->name)

@section('breadcrumb')
    <a href="{{ route('admin.roles.index') }}">Roles</a>
    <i class="fas fa-chevron-right"></i>
    <span>{{ $role->name }}</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $role->name }}</h1>
        <p class="page-subtitle">{{ $role->description ?? 'Role configuration and permissions' }}</p>
    </div>
    <div class="d-flex gap-2">
        @if(!$role->is_system)
            @can('roles.edit')
            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Role
            </a>
            @endcan
        @endif
        <a href="{{ route('admin.users.index', ['role' => $role->id]) }}" class="btn btn-secondary">
            <i class="fas fa-users"></i> View Users
        </a>
    </div>
</div>

<div class="row">
    <!-- Main Content -->
    <div class="col-8">
        <!-- Role Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Role Information</h3>
                <div class="d-flex gap-2">
                    @if($role->is_system)
                        <span class="badge badge-info"><i class="fas fa-lock"></i> System Role</span>
                    @endif
                    @if($role->slug === 'super-admin')
                        <span class="badge badge-dark"><i class="fas fa-shield-alt"></i> Super Admin</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="role-header">
                    <div class="role-icon" style="background: {{ $role->color ?? '#6366f1' }};">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="role-info">
                        <h2>{{ $role->name }}</h2>
                        <p class="text-muted">{{ $role->description }}</p>
                    </div>
                </div>

                <div class="detail-grid mt-4">
                    <div class="detail-item">
                        <label>Slug</label>
                        <span><code>{{ $role->slug }}</code></span>
                    </div>
                    <div class="detail-item">
                        <label>Level</label>
                        <span>{{ $role->level }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Total Users</label>
                        <span>{{ $role->users->count() }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Permissions</label>
                        <span>{{ $role->permissions->count() }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Created</label>
                        <span>{{ $role->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Updated</label>
                        <span>{{ $role->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Assigned Permissions</h3>
                <span class="badge badge-primary">{{ $role->permissions->count() }} permissions</span>
            </div>
            <div class="card-body">
                @if($role->slug === 'super-admin')
                    <div class="text-center py-4">
                        <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                        <h4>Full Access</h4>
                        <p class="text-muted">Super Admin has unrestricted access to all system features and permissions.</p>
                    </div>
                @elseif($role->permissions->count())
                    @php
                        $groupedPermissions = $role->permissions->groupBy('group');
                    @endphp

                    <div class="permissions-by-module">
                        @foreach($groupedPermissions as $group => $permissions)
                            <div class="permission-module">
                                <h4 class="module-title">
                                    <i class="fas fa-folder"></i>
                                    {{ ucfirst(str_replace('_', ' ', $group)) }}
                                    <span class="badge badge-secondary">{{ $permissions->count() }}</span>
                                </h4>
                                <div class="permission-tags">
                                    @foreach($permissions as $permission)
                                        <span class="permission-tag" title="{{ $permission->description }}">
                                            <i class="fas fa-check"></i> {{ $permission->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-ban fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                        <p class="text-muted">No permissions assigned to this role.</p>
                        @can('roles.edit')
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Permissions
                        </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>

        <!-- Users with this Role -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Users with this Role</h3>
                <a href="{{ route('admin.users.index', ['role' => $role->id]) }}" class="btn btn-sm btn-secondary">
                    View All
                </a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($role->users()->take(10)->get() as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-center gap-2">
                                            <div class="user-avatar-sm">
                                                @if($user->avatar)
                                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                                                @else
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                @endif
                                            </div>
                                            <span>{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @switch($user->status)
                                            @case('active')
                                                <span class="badge badge-success">Active</span>
                                                @break
                                            @case('suspended')
                                                <span class="badge badge-warning">Suspended</span>
                                                @break
                                            @case('locked')
                                                <span class="badge badge-danger">Locked</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-icon btn-secondary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No users with this role
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-4">
        <!-- Role Stats -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Statistics</h3>
            </div>
            <div class="card-body">
                <div class="stat-list">
                    <div class="stat-list-item">
                        <span class="stat-list-label">Total Users</span>
                        <span class="stat-list-value">{{ $role->users->count() }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Active Users</span>
                        <span class="stat-list-value text-success">{{ $role->users->where('status', 'active')->count() }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Permissions</span>
                        <span class="stat-list-value">{{ $role->permissions->count() }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Access Level</span>
                        <span class="stat-list-value">{{ $role->level }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permission Coverage -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Permission Coverage</h3>
            </div>
            <div class="card-body">
                @php
                    $totalPermissions = \App\Models\Permission::count();
                    $assignedPermissions = $role->permissions->count();
                    $coverage = $totalPermissions > 0 ? round(($assignedPermissions / $totalPermissions) * 100) : 0;
                @endphp

                <div class="coverage-display">
                    <div class="coverage-circle" style="--coverage: {{ $coverage }}%;">
                        <span class="coverage-value">{{ $coverage }}%</span>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <p class="text-muted mb-0">
                        {{ $assignedPermissions }} of {{ $totalPermissions }} permissions assigned
                    </p>
                </div>

                @if($coverage < 100 && !$role->is_system)
                    <div class="mt-3">
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-plus"></i> Add More Permissions
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Actions</h3>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-2">
                    @if(!$role->is_system)
                        @can('roles.edit')
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary btn-block">
                            <i class="fas fa-edit"></i> Edit Role
                        </a>
                        @endcan
                    @endif

                    <a href="{{ route('admin.users.index', ['role' => $role->id]) }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-users"></i> View All Users
                    </a>

                    <a href="{{ route('admin.users.create') }}?role={{ $role->id }}" class="btn btn-success btn-block">
                        <i class="fas fa-user-plus"></i> Add User to Role
                    </a>

                    @if(!$role->is_system && $role->users->count() === 0)
                        @can('roles.delete')
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this role?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-trash"></i> Delete Role
                            </button>
                        </form>
                        @endcan
                    @endif
                </div>

                @if($role->is_system)
                    <div class="alert alert-info mt-3 mb-0">
                        <small>
                            <i class="fas fa-info-circle"></i>
                            System roles cannot be edited or deleted.
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.role-header {
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
}

.role-icon {
    width: 64px;
    height: 64px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.role-info h2 {
    font-size: 1.5rem;
    margin-bottom: 0.25rem;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.detail-item label {
    font-size: 0.75rem;
    color: var(--gray-500);
    text-transform: uppercase;
    font-weight: 600;
}

.detail-item span {
    font-weight: 500;
    color: var(--gray-800);
}

.permissions-by-module {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.permission-module {
    border-bottom: 1px solid var(--gray-100);
    padding-bottom: 1rem;
}

.permission-module:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.module-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.module-title i {
    color: var(--gray-400);
}

.permission-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.permission-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.25rem 0.75rem;
    background: var(--gray-100);
    color: var(--gray-700);
    border-radius: 999px;
    font-size: 0.8125rem;
    cursor: help;
}

.permission-tag i {
    color: var(--success);
    font-size: 0.75rem;
}

.user-avatar-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
    overflow: hidden;
}

.user-avatar-sm img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.stat-list {
    display: flex;
    flex-direction: column;
}

.stat-list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--gray-100);
}

.stat-list-item:last-child {
    border-bottom: none;
}

.stat-list-label {
    color: var(--gray-600);
    font-size: 0.875rem;
}

.stat-list-value {
    font-weight: 600;
    color: var(--gray-800);
}

.coverage-display {
    display: flex;
    justify-content: center;
    padding: 1rem 0;
}

.coverage-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: conic-gradient(var(--primary) var(--coverage), var(--gray-200) 0);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.coverage-circle::before {
    content: '';
    position: absolute;
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: white;
}

.coverage-value {
    position: relative;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
}

.btn-block {
    width: 100%;
    text-align: center;
}
</style>
@endpush
