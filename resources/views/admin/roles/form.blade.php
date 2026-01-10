@extends('admin.layouts.app')

@section('title', isset($role) ? 'Edit Role' : 'Create Role')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <a href="{{ route('admin.roles.index') }}">Roles</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>{{ isset($role) ? 'Edit Role' : 'Create Role' }}</span>
@endsection

@section('content')
<form action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}" method="POST">
    @csrf
    @if(isset($role))
        @method('PUT')
    @endif

    <div class="page-header">
        <div>
            <h1 class="page-title">{{ isset($role) ? 'Edit Role' : 'Create Role' }}</h1>
            <p class="page-subtitle">{{ isset($role) ? 'Modify role settings and permissions' : 'Create a new user role with custom permissions' }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ isset($role) ? 'Update Role' : 'Create Role' }}
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <!-- Role Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Role Details</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $role->name ?? '') }}"
                               placeholder="e.g., content_manager"
                               pattern="[a-z_]+"
                               {{ isset($role) && $role->is_system ? 'readonly' : '' }}
                               required>
                        <small class="text-muted">Lowercase letters and underscores only</small>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Display Name <span class="text-danger">*</span></label>
                        <input type="text" name="display_name" class="form-control @error('display_name') is-invalid @enderror"
                               value="{{ old('display_name', $role->display_name ?? '') }}"
                               placeholder="e.g., Content Manager"
                               required>
                        @error('display_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3" placeholder="Describe this role's purpose">{{ old('description', $role->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Role Level</label>
                        <input type="number" name="level" class="form-control @error('level') is-invalid @enderror"
                               value="{{ old('level', $role->level ?? 1) }}"
                               min="1" max="100">
                        <small class="text-muted">Higher level = more authority (1-100)</small>
                        @error('level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if(isset($role))
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i>
                        <strong>{{ $role->users_count ?? 0 }}</strong> users have this role
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-secondary mb-2" style="width: 100%;" onclick="selectAll()">
                        <i class="fas fa-check-double"></i> Select All Permissions
                    </button>
                    <button type="button" class="btn btn-secondary mb-2" style="width: 100%;" onclick="deselectAll()">
                        <i class="fas fa-times"></i> Deselect All
                    </button>
                    <button type="button" class="btn btn-secondary" style="width: 100%;" onclick="selectViewOnly()">
                        <i class="fas fa-eye"></i> Select View Only
                    </button>
                </div>
            </div>
        </div>

        <div class="col-8">
            <!-- Permissions -->
            <div class="card">
                <div class="card-header d-flex align-center justify-between">
                    <h3 class="card-title">Permissions</h3>
                    <input type="text" id="permissionSearch" class="form-control" placeholder="Search permissions..." style="max-width: 250px;">
                </div>
                <div class="card-body">
                    @foreach($permissions ?? [] as $group => $groupPermissions)
                    <div class="permission-group mb-4" data-group="{{ $group }}">
                        <div class="permission-group-header d-flex align-center justify-between mb-2">
                            <label class="form-check mb-0">
                                <input type="checkbox" class="form-check-input group-checkbox" data-group="{{ $group }}">
                                <span class="form-check-label">
                                    <strong>{{ ucwords(str_replace(['_', '-'], ' ', $group)) }}</strong>
                                </span>
                            </label>
                            <span class="badge badge-secondary permission-count">0/{{ count($groupPermissions) }}</span>
                        </div>
                        <div class="permission-group-body" style="padding-left: 1.5rem;">
                            <div class="row">
                                @foreach($groupPermissions as $permission)
                                <div class="col-6 permission-item" data-name="{{ strtolower($permission->name . ' ' . ($permission->description ?? '')) }}">
                                    <label class="form-check">
                                        <input type="checkbox"
                                               name="permissions[]"
                                               value="{{ $permission->id }}"
                                               class="form-check-input permission-checkbox"
                                               data-group="{{ $group }}"
                                               {{ in_array($permission->id, old('permissions', $rolePermissions ?? [])) ? 'checked' : '' }}>
                                        <span class="form-check-label">
                                            {{ $permission->name }}
                                        </span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.permission-group {
    border: 1px solid var(--gray-200);
    border-radius: 0.5rem;
    padding: 1rem;
}

.permission-group-header {
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--gray-200);
    margin-bottom: 0.75rem;
}

.permission-item {
    padding: 0.25rem 0;
}

.permission-item.hidden {
    display: none;
}

.permission-group.hidden {
    display: none;
}
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update group checkbox state and count
    function updateGroupState(group) {
        const checkboxes = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
        const groupCheckbox = document.querySelector(`.group-checkbox[data-group="${group}"]`);
        const countBadge = document.querySelector(`[data-group="${group}"]`).closest('.permission-group').querySelector('.permission-count');

        const total = checkboxes.length;
        const checked = Array.from(checkboxes).filter(cb => cb.checked).length;

        if (groupCheckbox) {
            groupCheckbox.checked = checked === total;
            groupCheckbox.indeterminate = checked > 0 && checked < total;
        }

        if (countBadge) {
            countBadge.textContent = `${checked}/${total}`;
        }
    }

    // Initialize all groups
    document.querySelectorAll('.group-checkbox').forEach(cb => {
        updateGroupState(cb.dataset.group);
    });

    // Group checkbox toggle
    document.querySelectorAll('.group-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const group = this.dataset.group;
            const checkboxes = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            updateGroupState(group);
        });
    });

    // Individual permission checkbox
    document.querySelectorAll('.permission-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            updateGroupState(this.dataset.group);
        });
    });

    // Search permissions
    document.getElementById('permissionSearch').addEventListener('input', function() {
        const query = this.value.toLowerCase();

        document.querySelectorAll('.permission-item').forEach(item => {
            const name = item.dataset.name;
            item.classList.toggle('hidden', query && !name.includes(query));
        });

        document.querySelectorAll('.permission-group').forEach(group => {
            const visibleItems = group.querySelectorAll('.permission-item:not(.hidden)');
            group.classList.toggle('hidden', visibleItems.length === 0);
        });
    });
});

function selectAll() {
    document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
    document.querySelectorAll('.group-checkbox').forEach(cb => {
        cb.checked = true;
        cb.indeterminate = false;
    });
    document.querySelectorAll('.permission-group').forEach(group => {
        const total = group.querySelectorAll('.permission-checkbox').length;
        group.querySelector('.permission-count').textContent = `${total}/${total}`;
    });
}

function deselectAll() {
    document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
    document.querySelectorAll('.group-checkbox').forEach(cb => {
        cb.checked = false;
        cb.indeterminate = false;
    });
    document.querySelectorAll('.permission-group').forEach(group => {
        const total = group.querySelectorAll('.permission-checkbox').length;
        group.querySelector('.permission-count').textContent = `0/${total}`;
    });
}

function selectViewOnly() {
    document.querySelectorAll('.permission-checkbox').forEach(cb => {
        cb.checked = cb.value.includes('view') || cb.value.includes('read') || cb.value.includes('list');
    });
    document.querySelectorAll('.group-checkbox').forEach(cb => {
        const group = cb.dataset.group;
        const checkboxes = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
        const checked = Array.from(checkboxes).filter(c => c.checked).length;
        cb.checked = checked === checkboxes.length;
        cb.indeterminate = checked > 0 && checked < checkboxes.length;

        const countBadge = cb.closest('.permission-group').querySelector('.permission-count');
        countBadge.textContent = `${checked}/${checkboxes.length}`;
    });
}
</script>
@endpush
