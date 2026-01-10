@extends('admin.layouts.app')

@section('title', 'Maintenance')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <a href="{{ route('admin.settings.index') }}">Settings</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>Maintenance</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">System Maintenance</h1>
        <p class="page-subtitle">Cache management, logs, and system health</p>
    </div>
</div>

<div class="row">
    <div class="col-3">
        @include('admin.settings.partials.sidebar')
    </div>

    <div class="col-9">
        <!-- System Health -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">System Health</h3>
            </div>
            <div class="card-body">
                <div class="health-checks">
                    <div class="health-item">
                        <div class="health-icon {{ ($health['database'] ?? false) ? 'success' : 'danger' }}">
                            <i class="fas fa-{{ ($health['database'] ?? false) ? 'check' : 'times' }}"></i>
                        </div>
                        <div class="health-info">
                            <strong>Database Connection</strong>
                            <span class="text-muted">{{ ($health['database'] ?? false) ? 'Connected' : 'Failed' }}</span>
                        </div>
                    </div>

                    <div class="health-item">
                        <div class="health-icon {{ ($health['cache'] ?? false) ? 'success' : 'danger' }}">
                            <i class="fas fa-{{ ($health['cache'] ?? false) ? 'check' : 'times' }}"></i>
                        </div>
                        <div class="health-info">
                            <strong>Cache System</strong>
                            <span class="text-muted">{{ config('cache.default') }} driver</span>
                        </div>
                    </div>

                    <div class="health-item">
                        <div class="health-icon {{ ($health['storage'] ?? false) ? 'success' : 'warning' }}">
                            <i class="fas fa-{{ ($health['storage'] ?? false) ? 'check' : 'exclamation' }}"></i>
                        </div>
                        <div class="health-info">
                            <strong>Storage</strong>
                            <span class="text-muted">{{ $diskUsage ?? 'N/A' }} used</span>
                        </div>
                    </div>

                    <div class="health-item">
                        <div class="health-icon {{ ($health['queue'] ?? false) ? 'success' : 'warning' }}">
                            <i class="fas fa-{{ ($health['queue'] ?? false) ? 'check' : 'exclamation' }}"></i>
                        </div>
                        <div class="health-info">
                            <strong>Queue System</strong>
                            <span class="text-muted">{{ config('queue.default') }} driver</span>
                        </div>
                    </div>

                    <div class="health-item">
                        <div class="health-icon {{ ($health['mail'] ?? false) ? 'success' : 'warning' }}">
                            <i class="fas fa-{{ ($health['mail'] ?? false) ? 'check' : 'exclamation' }}"></i>
                        </div>
                        <div class="health-info">
                            <strong>Mail System</strong>
                            <span class="text-muted">{{ config('mail.default') }} driver</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cache Management -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Cache Management</h3>
            </div>
            <div class="card-body">
                <div class="cache-actions">
                    <div class="cache-action-item">
                        <div class="cache-info">
                            <i class="fas fa-database"></i>
                            <div>
                                <strong>Application Cache</strong>
                                <p class="text-muted mb-0">Clear cached data, views, and routes</p>
                            </div>
                        </div>
                        <form action="{{ route('admin.settings.cache.clear') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="type" value="application">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-trash"></i> Clear Cache
                            </button>
                        </form>
                    </div>

                    <div class="cache-action-item">
                        <div class="cache-info">
                            <i class="fas fa-code"></i>
                            <div>
                                <strong>View Cache</strong>
                                <p class="text-muted mb-0">Clear compiled Blade template files</p>
                            </div>
                        </div>
                        <form action="{{ route('admin.settings.cache.clear') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="type" value="views">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-trash"></i> Clear Views
                            </button>
                        </form>
                    </div>

                    <div class="cache-action-item">
                        <div class="cache-info">
                            <i class="fas fa-route"></i>
                            <div>
                                <strong>Route Cache</strong>
                                <p class="text-muted mb-0">Clear and rebuild route cache</p>
                            </div>
                        </div>
                        <form action="{{ route('admin.settings.cache.clear') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="type" value="routes">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-sync"></i> Rebuild Routes
                            </button>
                        </form>
                    </div>

                    <div class="cache-action-item">
                        <div class="cache-info">
                            <i class="fas fa-cogs"></i>
                            <div>
                                <strong>Config Cache</strong>
                                <p class="text-muted mb-0">Clear and rebuild configuration cache</p>
                            </div>
                        </div>
                        <form action="{{ route('admin.settings.cache.clear') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="type" value="config">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-sync"></i> Rebuild Config
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-4 pt-4" style="border-top: 1px solid var(--gray-100);">
                    <form action="{{ route('admin.settings.cache.clear') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="all">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-broom"></i> Clear All Caches
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Logs -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-between align-items-center">
                <h3 class="card-title mb-0">System Logs</h3>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.settings.logs.download') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-download"></i> Download Logs
                    </a>
                    <form action="{{ route('admin.settings.logs.clear') }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to clear all logs?')">
                            <i class="fas fa-trash"></i> Clear Logs
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="log-files">
                    @forelse($logFiles ?? [] as $file)
                    <div class="log-file-item">
                        <div class="log-file-info">
                            <i class="fas fa-file-alt"></i>
                            <div>
                                <strong>{{ $file['name'] }}</strong>
                                <span class="text-muted">{{ $file['size'] }} • {{ $file['modified'] }}</span>
                            </div>
                        </div>
                        <div class="log-file-actions">
                            <a href="{{ route('admin.settings.logs.view', $file['name']) }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('admin.settings.logs.download', $file['name']) }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-file-alt" style="font-size: 2rem; opacity: 0.3;"></i>
                        <p class="mt-2 mb-0">No log files found</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Maintenance Mode -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Maintenance Mode</h3>
            </div>
            <div class="card-body">
                <div class="maintenance-status mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="maintenance-indicator {{ $maintenanceMode ? 'active' : '' }}">
                            <i class="fas fa-{{ $maintenanceMode ? 'lock' : 'lock-open' }}"></i>
                        </div>
                        <div>
                            <strong>Status: {{ $maintenanceMode ? 'Maintenance Mode Active' : 'Site is Live' }}</strong>
                            <p class="text-muted mb-0">
                                {{ $maintenanceMode ? 'Only admins can access the site' : 'Site is accessible to all visitors' }}
                            </p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.settings.maintenance.toggle') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Maintenance Message</label>
                        <textarea name="message" class="form-control" rows="3"
                                  placeholder="We're currently performing scheduled maintenance. We'll be back shortly!">{{ old('message', $maintenanceMessage ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Allowed IPs (one per line)</label>
                        <textarea name="allowed_ips" class="form-control" rows="3"
                                  placeholder="127.0.0.1">{{ old('allowed_ips', $allowedIps ?? '') }}</textarea>
                        <small class="text-muted">These IP addresses can bypass maintenance mode</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Secret Bypass Token</label>
                        <input type="text" name="secret" class="form-control"
                               value="{{ old('secret', $maintenanceSecret ?? '') }}"
                               placeholder="Leave blank for no secret">
                        <small class="text-muted">Access site with ?secret=token to bypass maintenance</small>
                    </div>

                    <button type="submit" class="btn {{ $maintenanceMode ? 'btn-success' : 'btn-warning' }}">
                        <i class="fas fa-{{ $maintenanceMode ? 'unlock' : 'lock' }}"></i>
                        {{ $maintenanceMode ? 'Disable Maintenance Mode' : 'Enable Maintenance Mode' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- System Info -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">System Information</h3>
            </div>
            <div class="card-body">
                <div class="system-info-grid">
                    <div class="system-info-item">
                        <span class="system-info-label">Laravel Version</span>
                        <span class="system-info-value">{{ app()->version() }}</span>
                    </div>
                    <div class="system-info-item">
                        <span class="system-info-label">PHP Version</span>
                        <span class="system-info-value">{{ phpversion() }}</span>
                    </div>
                    <div class="system-info-item">
                        <span class="system-info-label">Server</span>
                        <span class="system-info-value">{{ $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' }}</span>
                    </div>
                    <div class="system-info-item">
                        <span class="system-info-label">Database</span>
                        <span class="system-info-value">{{ config('database.default') }}</span>
                    </div>
                    <div class="system-info-item">
                        <span class="system-info-label">Environment</span>
                        <span class="system-info-value">{{ app()->environment() }}</span>
                    </div>
                    <div class="system-info-item">
                        <span class="system-info-label">Debug Mode</span>
                        <span class="system-info-value">{{ config('app.debug') ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                    <div class="system-info-item">
                        <span class="system-info-label">Timezone</span>
                        <span class="system-info-value">{{ config('app.timezone') }}</span>
                    </div>
                    <div class="system-info-item">
                        <span class="system-info-label">Memory Limit</span>
                        <span class="system-info-value">{{ ini_get('memory_limit') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.health-checks {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}

.health-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: var(--gray-50);
    border-radius: 0.5rem;
}

.health-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.health-icon.success {
    background: var(--success);
}

.health-icon.danger {
    background: var(--danger);
}

.health-icon.warning {
    background: var(--warning);
}

.health-info {
    display: flex;
    flex-direction: column;
}

.health-info strong {
    font-size: 0.875rem;
}

.health-info span {
    font-size: 0.75rem;
}

.cache-action-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid var(--gray-100);
}

.cache-action-item:last-child {
    border-bottom: none;
}

.cache-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.cache-info i {
    font-size: 1.5rem;
    color: var(--gray-400);
    width: 40px;
    text-align: center;
}

.log-file-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--gray-100);
}

.log-file-item:last-child {
    border-bottom: none;
}

.log-file-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.log-file-info i {
    color: var(--gray-400);
}

.log-file-info span {
    font-size: 0.75rem;
    display: block;
}

.log-file-actions {
    display: flex;
    gap: 0.5rem;
}

.maintenance-indicator {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--success);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.maintenance-indicator.active {
    background: var(--warning);
}

.system-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.system-info-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    background: var(--gray-50);
    border-radius: 0.375rem;
}

.system-info-label {
    color: var(--gray-600);
}

.system-info-value {
    font-weight: 500;
}
</style>
@endsection
