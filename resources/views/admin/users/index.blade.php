@extends('admin.layouts.app')

@section('title', 'Users')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>Users</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">User Management</h1>
        <p class="page-subtitle">Manage administrator accounts and access</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add User
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex gap-3 align-center" style="flex-wrap: wrap;">
            <div class="form-group mb-0" style="min-width: 200px;">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Search by name or email..."
                    value="{{ request('search') }}"
                >
            </div>

            <div class="form-group mb-0">
                <select name="role" class="form-control">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-0">
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Filter
            </button>

            @if(request()->hasAny(['search', 'role', 'status']))
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-center gap-3">
                                <div class="user-avatar" style="width: 40px; height: 40px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <strong>{{ $user->name }}</strong>
                                    <div class="text-muted" style="font-size: 0.8125rem;">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-primary">{{ $user->getPrimaryRole()?->name ?? 'No Role' }}</span>
                        </td>
                        <td>
                            @switch($user->status)
                                @case('active')
                                    <span class="badge badge-success">Active</span>
                                    @break
                                @case('suspended')
                                    <span class="badge badge-danger">Suspended</span>
                                    @break
                                @case('pending')
                                    <span class="badge badge-warning">Pending</span>
                                    @break
                            @endswitch
                            @if($user->isLocked())
                                <span class="badge badge-danger"><i class="fas fa-lock"></i> Locked</span>
                            @endif
                        </td>
                        <td>
                            @if($user->last_login_at)
                                {{ $user->last_login_at->diffForHumans() }}
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $user->last_login_ip }}</div>
                            @else
                                <span class="text-muted">Never</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-secondary btn-icon" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-secondary btn-icon" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                @if($user->id !== auth()->id())
                                    @if($user->status === 'active')
                                        <form action="{{ route('admin.users.suspend', $user) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning btn-icon" title="Suspend" onclick="return confirm('Suspend this user?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @elseif($user->status === 'suspended')
                                        <form action="{{ route('admin.users.activate', $user) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success btn-icon" title="Activate">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($user->isLocked())
                                        <form action="{{ route('admin.users.unlock', $user) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-info btn-icon" title="Unlock">
                                                <i class="fas fa-unlock"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted" style="padding: 3rem;">
                            <i class="fas fa-users" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                            No users found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
    <div class="card-body" style="border-top: 1px solid var(--gray-200);">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
