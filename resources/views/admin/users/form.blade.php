@extends('admin.layouts.app')

@section('title', isset($user) ? 'Edit User' : 'Add User')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <a href="{{ route('admin.users.index') }}">Users</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>{{ isset($user) ? 'Edit User' : 'Add User' }}</span>
@endsection

@section('content')
<form action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST">
    @csrf
    @if(isset($user))
        @method('PUT')
    @endif

    <div class="page-header">
        <div>
            <h1 class="page-title">{{ isset($user) ? 'Edit User' : 'Add User' }}</h1>
            <p class="page-subtitle">{{ isset($user) ? 'Update user account and permissions' : 'Create a new admin user' }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ isset($user) ? 'Update User' : 'Create User' }}
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-8">
            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Basic Information</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name ?? '') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email ?? '') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $user->phone ?? '') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Job Title</label>
                                <input type="text" name="job_title" class="form-control @error('job_title') is-invalid @enderror"
                                       value="{{ old('job_title', $user->job_title ?? '') }}"
                                       placeholder="e.g., Front Desk Manager">
                                @error('job_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Password</h3>
                </div>
                <div class="card-body">
                    @if(isset($user))
                    <p class="text-muted mb-3">Leave blank to keep current password</p>
                    @endif

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Password {{ isset($user) ? '' : '<span class="text-danger">*</span>' }}</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                       {{ isset($user) ? '' : 'required' }}>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Confirm Password {{ isset($user) ? '' : '<span class="text-danger">*</span>' }}</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                       {{ isset($user) ? '' : 'required' }}>
                            </div>
                        </div>
                    </div>

                    <div class="password-requirements">
                        <small class="text-muted">Password must be at least 8 characters with uppercase, number, and special character</small>
                    </div>
                </div>
            </div>

            <!-- Role & Permissions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Role & Permissions</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">User Role <span class="text-danger">*</span></label>
                        <div class="roles-grid">
                            @foreach($roles ?? [] as $role)
                            <label class="role-option {{ in_array($role->id, old('roles', isset($user) ? $user->roles->pluck('id')->toArray() : [])) ? 'selected' : '' }}">
                                <input type="radio" name="roles[]" value="{{ $role->id }}"
                                       {{ in_array($role->id, old('roles', isset($user) ? $user->roles->pluck('id')->toArray() : [])) ? 'checked' : '' }}
                                       {{ !isset($user) && $loop->first ? 'checked' : '' }}>
                                <div class="role-content">
                                    <strong>{{ $role->display_name ?? $role->name }}</strong>
                                    @if($role->description)
                                        <p class="text-muted mb-0" style="font-size: 0.75rem;">{{ $role->description }}</p>
                                    @endif
                                    <span class="badge badge-secondary mt-1">{{ $role->permissions_count ?? $role->permissions->count() }} permissions</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('roles')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-4">
            <!-- Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Account Status</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror">
                            <option value="active" {{ old('status', $user->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $user->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ old('status', $user->status ?? '') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-check">
                            <input type="checkbox" name="email_verified" value="1" class="form-check-input"
                                   {{ old('email_verified', isset($user) && $user->email_verified_at ? true : false) ? 'checked' : '' }}>
                            <span class="form-check-label">Email Verified</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Notifications</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="notify_new_bookings" value="1" class="form-check-input"
                                   {{ old('notify_new_bookings', $user->notify_new_bookings ?? true) ? 'checked' : '' }}>
                            <span class="form-check-label">New Booking Alerts</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="notify_check_ins" value="1" class="form-check-input"
                                   {{ old('notify_check_ins', $user->notify_check_ins ?? true) ? 'checked' : '' }}>
                            <span class="form-check-label">Check-in Notifications</span>
                        </label>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-check">
                            <input type="checkbox" name="notify_system" value="1" class="form-check-input"
                                   {{ old('notify_system', $user->notify_system ?? false) ? 'checked' : '' }}>
                            <span class="form-check-label">System Alerts</span>
                        </label>
                    </div>
                </div>
            </div>

            @if(isset($user))
            <!-- Account Info -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Account Information</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                        <span class="text-muted">Created</span>
                        <span>{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                        <span class="text-muted">Last Login</span>
                        <span>{{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}</span>
                    </div>
                    <div class="d-flex justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                        <span class="text-muted">Login Count</span>
                        <span>{{ $user->login_count ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-between py-2">
                        <span class="text-muted">Failed Logins</span>
                        <span>{{ $user->failed_login_attempts ?? 0 }}</span>
                    </div>

                    @if($user->failed_login_attempts > 0)
                    <form action="{{ route('admin.users.resetFailedLogins', $user) }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%;">
                            <i class="fas fa-unlock"></i> Reset Failed Login Attempts
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</form>

<style>
.roles-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.role-option {
    border: 2px solid var(--gray-200);
    border-radius: 0.5rem;
    padding: 1rem;
    cursor: pointer;
    transition: all 0.15s;
    position: relative;
}

.role-option:hover {
    border-color: var(--gray-400);
}

.role-option.selected,
.role-option:has(input:checked) {
    border-color: var(--primary);
    background: var(--primary-light);
}

.role-option input {
    position: absolute;
    opacity: 0;
}

.role-content {
    display: flex;
    flex-direction: column;
}
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update visual selection state for roles
    document.querySelectorAll('.role-option input').forEach(input => {
        input.addEventListener('change', function() {
            document.querySelectorAll('.role-option').forEach(opt => opt.classList.remove('selected'));
            if (this.checked) {
                this.closest('.role-option').classList.add('selected');
            }
        });
    });
});
</script>
@endpush
