@extends('admin.layouts.app')

@section('title', 'Amenities')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>Amenities</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Amenities</h1>
        <p class="page-subtitle">Manage hotel amenities and features</p>
    </div>
    <div class="page-actions">
        @can('amenities.create')
        <button type="button" class="btn btn-primary" data-modal="createModal">
            <i class="fas fa-plus"></i> Add Amenity
        </button>
        @endcan
    </div>
</div>

<!-- Amenities Grid -->
<div class="amenities-grid">
    @forelse($amenities ?? [] as $amenity)
    <div class="card amenity-card">
        <div class="card-body">
            <div class="amenity-icon">
                <i class="{{ $amenity->icon ?? 'fas fa-check' }}"></i>
            </div>
            <h4 class="amenity-name">{{ $amenity->name }}</h4>
            @if($amenity->description)
                <p class="amenity-description">{{ Str::limit($amenity->description, 60) }}</p>
            @endif
            <div class="amenity-meta">
                <span class="badge badge-{{ $amenity->is_active ? 'success' : 'secondary' }}">
                    {{ $amenity->is_active ? 'Active' : 'Inactive' }}
                </span>
                @if($amenity->is_featured)
                    <span class="badge badge-warning">Featured</span>
                @endif
            </div>
            <div class="amenity-stats">
                <span class="text-muted" style="font-size: 0.75rem;">
                    Used in {{ $amenity->room_types_count ?? 0 }} room types
                </span>
            </div>
        </div>
        <div class="card-footer d-flex justify-between">
            @can('amenities.edit')
            <button type="button" class="btn btn-sm btn-secondary" data-edit="{{ $amenity->id }}"
                    data-name="{{ $amenity->name }}"
                    data-description="{{ $amenity->description }}"
                    data-icon="{{ $amenity->icon }}"
                    data-category="{{ $amenity->category }}"
                    data-is-active="{{ $amenity->is_active }}"
                    data-is-featured="{{ $amenity->is_featured }}">
                <i class="fas fa-edit"></i> Edit
            </button>
            @endcan
            @can('amenities.delete')
            <form action="{{ route('admin.amenities.destroy', $amenity) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Are you sure you want to delete this amenity?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
            @endcan
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-concierge-bell fa-3x text-muted mb-3"></i>
        <p class="text-muted">No amenities found</p>
        @can('amenities.create')
        <button type="button" class="btn btn-primary" data-modal="createModal">
            <i class="fas fa-plus"></i> Add First Amenity
        </button>
        @endcan
    </div>
    @endforelse
</div>

<!-- Create/Edit Modal -->
<div id="createModal" class="modal" style="display: none;">
    <div class="modal-dialog" style="max-width: 500px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalTitle">Add Amenity</h4>
                <button type="button" class="modal-close" data-modal-close>&times;</button>
            </div>
            <form id="amenityForm" action="{{ route('admin.amenities.store') }}" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="amenityName" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="amenityDescription" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Icon</label>
                        <div class="icon-selector">
                            <input type="text" name="icon" id="amenityIcon" class="form-control" placeholder="e.g., fas fa-wifi">
                            <div class="icon-preview">
                                <i id="iconPreview" class="fas fa-check"></i>
                            </div>
                        </div>
                        <small class="text-muted">Use Font Awesome class names</small>

                        <div class="icon-suggestions mt-2">
                            @foreach(['fas fa-wifi', 'fas fa-tv', 'fas fa-snowflake', 'fas fa-coffee', 'fas fa-parking', 'fas fa-swimming-pool', 'fas fa-utensils', 'fas fa-dumbbell', 'fas fa-spa', 'fas fa-concierge-bell', 'fas fa-cocktail', 'fas fa-door-open'] as $icon)
                            <button type="button" class="icon-btn" data-icon="{{ $icon }}">
                                <i class="{{ $icon }}"></i>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category" id="amenityCategory" class="form-control">
                            <option value="">Select Category</option>
                            <option value="room">Room Amenities</option>
                            <option value="bathroom">Bathroom</option>
                            <option value="entertainment">Entertainment</option>
                            <option value="connectivity">Connectivity</option>
                            <option value="food">Food & Beverage</option>
                            <option value="facility">Hotel Facilities</option>
                            <option value="service">Services</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <label class="form-check">
                                    <input type="checkbox" name="is_active" id="amenityActive" value="1" class="form-check-input" checked>
                                    <span class="form-check-label">Active</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <label class="form-check">
                                    <input type="checkbox" name="is_featured" id="amenityFeatured" value="1" class="form-check-input">
                                    <span class="form-check-label">Featured</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> Save Amenity
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.amenities-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
}

.amenity-card {
    text-align: center;
}

.amenity-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 1rem;
    background: var(--primary-light);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--primary);
}

.amenity-name {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.amenity-description {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin-bottom: 0.75rem;
}

.amenity-meta {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    margin-bottom: 0.5rem;
}

.amenity-stats {
    margin-top: 0.5rem;
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 0.5rem;
}

.icon-selector {
    display: flex;
    gap: 0.5rem;
}

.icon-selector .form-control {
    flex: 1;
}

.icon-preview {
    width: 42px;
    height: 42px;
    background: var(--gray-100);
    border-radius: 0.375rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: var(--gray-600);
}

.icon-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem;
}

.icon-btn {
    width: 36px;
    height: 36px;
    border: 1px solid var(--gray-200);
    background: white;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s;
}

.icon-btn:hover {
    background: var(--gray-100);
    border-color: var(--primary);
    color: var(--primary);
}

/* Modal styles */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-dialog {
    background: white;
    border-radius: 0.5rem;
    width: 100%;
    margin: 1rem;
    max-height: 90vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.modal-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-title {
    margin: 0;
    font-size: 1.125rem;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--gray-500);
}

.modal-body {
    padding: 1.5rem;
    overflow-y: auto;
}

.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--gray-200);
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('createModal');
    const form = document.getElementById('amenityForm');
    const methodField = document.getElementById('methodField');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('submitBtn');
    const iconInput = document.getElementById('amenityIcon');
    const iconPreview = document.getElementById('iconPreview');

    // Modal handlers
    document.querySelectorAll('[data-modal="createModal"]').forEach(trigger => {
        trigger.addEventListener('click', function() {
            // Reset form for create
            form.reset();
            form.action = '{{ route("admin.amenities.store") }}';
            methodField.innerHTML = '';
            modalTitle.textContent = 'Add Amenity';
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Save Amenity';
            document.getElementById('amenityActive').checked = true;
            iconPreview.className = 'fas fa-check';
            modal.style.display = 'flex';
        });
    });

    // Edit button handler
    document.querySelectorAll('[data-edit]').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.edit;
            form.action = `/admin/amenities/${id}`;
            methodField.innerHTML = '@method("PUT")';
            modalTitle.textContent = 'Edit Amenity';
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Update Amenity';

            document.getElementById('amenityName').value = this.dataset.name || '';
            document.getElementById('amenityDescription').value = this.dataset.description || '';
            document.getElementById('amenityIcon').value = this.dataset.icon || '';
            document.getElementById('amenityCategory').value = this.dataset.category || '';
            document.getElementById('amenityActive').checked = this.dataset.isActive === '1';
            document.getElementById('amenityFeatured').checked = this.dataset.isFeatured === '1';

            if (this.dataset.icon) {
                iconPreview.className = this.dataset.icon;
            }

            modal.style.display = 'flex';
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach(closer => {
        closer.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    });

    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });

    // Icon preview
    iconInput.addEventListener('input', function() {
        iconPreview.className = this.value || 'fas fa-check';
    });

    // Icon suggestions
    document.querySelectorAll('.icon-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const icon = this.dataset.icon;
            iconInput.value = icon;
            iconPreview.className = icon;
        });
    });
});
</script>
@endpush
