@extends('admin.layouts.app')

@section('title', 'My Profile')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>My Profile</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">My Profile</h1>
        <p class="page-subtitle">Manage your account settings and preferences</p>
    </div>
</div>

<div class="row">
    <div class="col-4">
        <!-- Profile Card -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="profile-avatar mb-3">
                    @if($user->avatar)
                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}">
                    @else
                        <span>{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                    @endif
                </div>
                <h3 class="profile-name">{{ $user->name }}</h3>
                <p class="text-muted">{{ $user->email }}</p>

                @if($user->roles->isNotEmpty())
                <div class="profile-roles mb-3">
                    @foreach($user->roles as $role)
                        <span class="badge badge-primary">{{ $role->display_name ?? $role->name }}</span>
                    @endforeach
                </div>
                @endif

                <div class="profile-stats">
                    <div class="stat">
                        <span class="stat-value">{{ $user->created_at->diffForHumans(null, true) }}</span>
                        <span class="stat-label">Member Since</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</span>
                        <span class="stat-label">Last Login</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Security -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Account Security</h3>
            </div>
            <div class="card-body">
                <div class="security-item d-flex align-center justify-between py-2">
                    <div>
                        <strong>Two-Factor Authentication</strong>
                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Add extra security to your account</p>
                    </div>
                    <span class="badge badge-{{ $user->two_factor_enabled ? 'success' : 'secondary' }}">
                        {{ $user->two_factor_enabled ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>

                <div class="security-item d-flex align-center justify-between py-2" style="border-top: 1px solid var(--gray-100);">
                    <div>
                        <strong>Login Sessions</strong>
                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Manage your active sessions</p>
                    </div>
                    <a href="#" class="btn btn-sm btn-secondary">Manage</a>
                </div>

                <div class="security-item d-flex align-center justify-between py-2" style="border-top: 1px solid var(--gray-100);">
                    <div>
                        <strong>Activity Log</strong>
                        <p class="text-muted mb-0" style="font-size: 0.75rem;">View your recent activity</p>
                    </div>
                    <a href="#" class="btn btn-sm btn-secondary">View</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-8">
        <!-- Profile Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Profile Information</h3>
            </div>
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}" required>
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
                                       value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Job Title</label>
                                <input type="text" name="job_title" class="form-control @error('job_title') is-invalid @enderror"
                                       value="{{ old('job_title', $user->job_title) }}">
                                @error('job_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Profile Picture</label>
                        <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror"
                               accept="image/*">
                        <small class="text-muted">JPG, PNG, or GIF. Max 2MB.</small>
                        @error('avatar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Bio</label>
                        <textarea name="bio" class="form-control @error('bio') is-invalid @enderror"
                                  rows="3" placeholder="Tell us a bit about yourself">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer d-flex justify-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Change Password</h3>
            </div>
            <form action="{{ route('admin.profile.password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Current Password <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">New Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="password-requirements">
                        <small class="text-muted">Password must:</small>
                        <ul class="text-muted" style="font-size: 0.75rem; margin: 0.25rem 0 0; padding-left: 1.25rem;">
                            <li>Be at least 8 characters long</li>
                            <li>Include at least one uppercase letter</li>
                            <li>Include at least one number</li>
                            <li>Include at least one special character</li>
                        </ul>
                    </div>
                </div>
                <div class="card-footer d-flex justify-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Preferences -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Preferences</h3>
            </div>
            <form action="{{ route('admin.profile.preferences') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Timezone</label>
                                <select name="timezone" class="form-control">
                                    @foreach($timezones ?? [] as $tz => $label)
                                        <option value="{{ $tz }}" {{ ($user->timezone ?? 'UTC') == $tz ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Date Format</label>
                                <select name="date_format" class="form-control">
                                    <option value="M d, Y" {{ ($user->date_format ?? 'M d, Y') == 'M d, Y' ? 'selected' : '' }}>Jan 15, 2025</option>
                                    <option value="d/m/Y" {{ ($user->date_format ?? '') == 'd/m/Y' ? 'selected' : '' }}>15/01/2025</option>
                                    <option value="Y-m-d" {{ ($user->date_format ?? '') == 'Y-m-d' ? 'selected' : '' }}>2025-01-15</option>
                                    <option value="d.m.Y" {{ ($user->date_format ?? '') == 'd.m.Y' ? 'selected' : '' }}>15.01.2025</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <h5 style="font-size: 0.875rem; margin-bottom: 1rem;">Notifications</h5>

                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="email_notifications" value="1" class="form-check-input"
                                   {{ $user->email_notifications ?? true ? 'checked' : '' }}>
                            <span class="form-check-label">
                                <strong>Email Notifications</strong>
                                <p class="text-muted mb-0" style="font-size: 0.75rem;">Receive email notifications for important updates</p>
                            </span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="booking_notifications" value="1" class="form-check-input"
                                   {{ $user->booking_notifications ?? true ? 'checked' : '' }}>
                            <span class="form-check-label">
                                <strong>Booking Notifications</strong>
                                <p class="text-muted mb-0" style="font-size: 0.75rem;">Get notified about new bookings and check-ins</p>
                            </span>
                        </label>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-check">
                            <input type="checkbox" name="system_notifications" value="1" class="form-check-input"
                                   {{ $user->system_notifications ?? true ? 'checked' : '' }}>
                            <span class="form-check-label">
                                <strong>System Notifications</strong>
                                <p class="text-muted mb-0" style="font-size: 0.75rem;">Receive notifications about system updates and maintenance</p>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="card-footer d-flex justify-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Preferences
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.profile-avatar {
    width: 120px;
    height: 120px;
    margin: 0 auto;
    border-radius: 50%;
    background: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: 600;
    color: white;
    overflow: hidden;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-name {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.profile-roles {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

.profile-stats {
    display: flex;
    gap: 2rem;
    justify-content: center;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--gray-200);
}

.profile-stats .stat {
    text-align: center;
}

.profile-stats .stat-value {
    display: block;
    font-weight: 600;
    color: var(--gray-900);
}

.profile-stats .stat-label {
    font-size: 0.75rem;
    color: var(--gray-500);
}
</style>
@endsection
