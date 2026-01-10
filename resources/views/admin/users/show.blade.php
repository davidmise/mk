@extends('admin.layouts.app')

@section('title', 'User Details - ' . $user->name)

@section('breadcrumb')
    <a href="{{ route('admin.users.index') }}">Users</a>
    <i class="fas fa-chevron-right"></i>
    <span>{{ $user->name }}</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $user->name }}</h1>
        <p class="page-subtitle">{{ $user->getPrimaryRole()?->name ?? 'No Role' }} &bull; Member since {{ $user->created_at->format('M d, Y') }}</p>
    </div>
    <div class="d-flex gap-2">
        @can('users.edit')
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit User
        </a>
        @endcan
        @if($user->id !== auth()->id())
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button">
                <i class="fas fa-cog"></i> Actions
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                @if($user->status === 'active')
                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('suspendForm').submit();">
                        <i class="fas fa-ban text-warning"></i> Suspend User
                    </a>
                @elseif($user->status === 'suspended')
                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('activateForm').submit();">
                        <i class="fas fa-check text-success"></i> Activate User
                    </a>
                @endif
                @if($user->status === 'locked')
                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('unlockForm').submit();">
                        <i class="fas fa-unlock text-info"></i> Unlock Account
                    </a>
                @endif
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" onclick="event.preventDefault(); sendPasswordReset();">
                    <i class="fas fa-key text-primary"></i> Send Password Reset
                </a>
                @can('users.delete')
                <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); confirmDelete();">
                    <i class="fas fa-trash"></i> Delete User
                </a>
                @endcan
            </div>
        </div>
        @endif
    </div>
</div>

<div class="row">
    <!-- Main Content -->
    <div class="col-8">
        <!-- User Profile Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Profile Information</h3>
            </div>
            <div class="card-body">
                <div class="user-profile-header">
                    <div class="user-avatar-large">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                        @else
                            <div class="avatar-initials">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <div class="user-profile-info">
                        <h2>{{ $user->name }}</h2>
                        <p class="text-muted">{{ $user->email }}</p>
                        <div class="user-badges">
                            @if($user->getPrimaryRole())
                                <span class="badge" style="background: {{ $user->getPrimaryRole()->color ?? '#6366f1' }}; color: white;">
                                    {{ $user->getPrimaryRole()->name }}
                                </span>
                            @endif
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
                            @if($user->isSuperAdmin())
                                <span class="badge badge-dark"><i class="fas fa-shield-alt"></i> Super Admin</span>
                            @endif
                        </div>
                    </div>
                </div>

                <hr>

                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Full Name</label>
                        <span>{{ $user->name }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Email</label>
                        <span>{{ $user->email }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Phone</label>
                        <span>{{ $user->phone ?? 'Not provided' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Department</label>
                        <span>{{ $user->department ?? 'Not assigned' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Position</label>
                        <span>{{ $user->position ?? 'Not specified' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Role</label>
                        <span>{{ $user->getPrimaryRole()?->name ?? 'No Role' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Created</label>
                        <span>{{ $user->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Last Updated</label>
                        <span>{{ $user->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Permissions</h3>
            </div>
            <div class="card-body">
                @if($user->role && $user->role->permissions->count())
                    <div class="permissions-by-module">
                        @php
                            $groupedPermissions = $user->role->permissions->groupBy('module');
                        @endphp

                        @foreach($groupedPermissions as $module => $permissions)
                            <div class="permission-module">
                                <h4 class="module-title">{{ ucfirst(str_replace('_', ' ', $module)) }}</h4>
                                <div class="permission-tags">
                                    @foreach($permissions as $permission)
                                        <span class="permission-tag">
                                            <i class="fas fa-check"></i> {{ $permission->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif($user->isSuperAdmin())
                    <div class="text-center py-4">
                        <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                        <h4>Super Admin</h4>
                        <p class="text-muted">This user has full access to all system features and permissions.</p>
                    </div>
                @else
                    <p class="text-muted text-center">No permissions assigned to this user's role.</p>
                @endif
            </div>
        </div>

        <!-- Activity Log -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Activity</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Target</th>
                                <th>IP Address</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user->activityLogs()->latest()->take(15)->get() as $log)
                                <tr>
                                    <td>
                                        <span class="badge badge-{{ $log->action_color }}">{{ ucfirst($log->action) }}</span>
                                    </td>
                                    <td>
                                        <small>{{ $log->description }}</small>
                                    </td>
                                    <td><code>{{ $log->ip_address }}</code></td>
                                    <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No activity recorded</td>
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
        <!-- Account Status -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Account Status</h3>
            </div>
            <div class="card-body">
                <div class="status-indicator {{ $user->status }}">
                    @switch($user->status)
                        @case('active')
                            <i class="fas fa-check-circle"></i>
                            <span>Active</span>
                            @break
                        @case('suspended')
                            <i class="fas fa-pause-circle"></i>
                            <span>Suspended</span>
                            @break
                        @case('locked')
                            <i class="fas fa-lock"></i>
                            <span>Locked</span>
                            @break
                    @endswitch
                </div>

                @if($user->status === 'locked' && $user->locked_until)
                    <div class="alert alert-warning mt-3">
                        <small>
                            <strong>Locked until:</strong><br>
                            {{ $user->locked_until->format('M d, Y H:i') }}<br>
                            ({{ $user->locked_until->diffForHumans() }})
                        </small>
                    </div>
                @endif

                <div class="stat-list mt-3">
                    <div class="stat-list-item">
                        <span class="stat-list-label">Last Login</span>
                        <span class="stat-list-value">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                        </span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Last IP</span>
                        <span class="stat-list-value">
                            <code>{{ $user->last_login_ip ?? 'N/A' }}</code>
                        </span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Failed Attempts</span>
                        <span class="stat-list-value">
                            {{ $user->failed_login_attempts }}
                        </span>
                    </div>
                    @if($user->email_verified_at)
                        <div class="stat-list-item">
                            <span class="stat-list-label">Email Verified</span>
                            <span class="stat-list-value text-success">
                                <i class="fas fa-check-circle"></i> Yes
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Statistics</h3>
            </div>
            <div class="card-body">
                <div class="stat-list">
                    <div class="stat-list-item">
                        <span class="stat-list-label">Bookings Created</span>
                        <span class="stat-list-value">{{ $user->bookingsCreated()->count() }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Articles Written</span>
                        <span class="stat-list-value">{{ $user->articles()->count() }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Activity Logs</span>
                        <span class="stat-list-value">{{ $user->activityLogs()->count() }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Days Active</span>
                        <span class="stat-list-value">{{ $user->created_at->diffInDays(now()) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Two-Factor Status -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Security</h3>
            </div>
            <div class="card-body">
                <div class="d-flex align-center justify-between mb-3">
                    <span>Two-Factor Auth</span>
                    @if($user->two_factor_enabled)
                        <span class="badge badge-success"><i class="fas fa-shield-alt"></i> Enabled</span>
                    @else
                        <span class="badge badge-secondary">Disabled</span>
                    @endif
                </div>
                <div class="d-flex align-center justify-between">
                    <span>Password Changed</span>
                    <span class="text-muted">
                        {{ $user->password_changed_at ? $user->password_changed_at->diffForHumans() : 'Never' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Forms -->
<form id="suspendForm" action="{{ route('admin.users.suspend', $user) }}" method="POST" style="display: none;">
    @csrf
</form>

<form id="activateForm" action="{{ route('admin.users.activate', $user) }}" method="POST" style="display: none;">
    @csrf
</form>

<form id="unlockForm" action="{{ route('admin.users.unlock', $user) }}" method="POST" style="display: none;">
    @csrf
</form>

<form id="deleteForm" action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('styles')
<style>
.user-profile-header {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding-bottom: 1.5rem;
}

.user-avatar-large {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.user-avatar-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-initials {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 600;
}

.user-profile-info h2 {
    font-size: 1.5rem;
    margin-bottom: 0.25rem;
}

.user-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.75rem;
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
}

.permission-tag i {
    color: var(--success);
    font-size: 0.75rem;
}

.status-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 1.5rem;
    border-radius: 12px;
    font-size: 1.25rem;
    font-weight: 600;
}

.status-indicator.active {
    background: rgba(34, 197, 94, 0.1);
    color: var(--success);
}

.status-indicator.suspended {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.status-indicator.locked {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.status-indicator i {
    font-size: 1.5rem;
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

.dropdown-menu {
    display: none;
    position: absolute;
    right: 0;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    min-width: 200px;
    z-index: 100;
}

.dropdown:hover .dropdown-menu,
.dropdown.active .dropdown-menu {
    display: block;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    color: var(--gray-700);
    text-decoration: none;
    transition: var(--transition);
}

.dropdown-item:hover {
    background: var(--gray-100);
}

.dropdown-divider {
    border-top: 1px solid var(--gray-200);
    margin: 0.25rem 0;
}
</style>
@endpush

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}

function sendPasswordReset() {
    if (confirm('Send password reset email to {{ $user->email }}?')) {
        // AJAX call to send password reset
        fetch('{{ route("admin.users.send-password-reset", $user) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Password reset email sent!');
        })
        .catch(error => {
            alert('Failed to send password reset email.');
        });
    }
}

// Dropdown toggle
document.querySelectorAll('.dropdown-toggle').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        this.closest('.dropdown').classList.toggle('active');
    });
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown.active').forEach(function(d) {
            d.classList.remove('active');
        });
    }
});
</script>
@endpush
