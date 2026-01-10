@extends('admin.layouts.app')

@section('title', isset($amenity) ? 'Edit Amenity - ' . $amenity->name : 'Add Amenity')

@section('breadcrumb')
    <a href="{{ route('admin.amenities.index') }}">Amenities</a>
    <i class="fas fa-chevron-right"></i>
    <span>{{ isset($amenity) ? 'Edit' : 'Add' }}</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ isset($amenity) ? 'Edit Amenity' : 'Add New Amenity' }}</h1>
        <p class="page-subtitle">{{ isset($amenity) ? 'Update amenity information' : 'Create a new hotel amenity' }}</p>
    </div>
</div>

<form action="{{ isset($amenity) ? route('admin.amenities.update', $amenity) : route('admin.amenities.store') }}" method="POST">
    @csrf
    @if(isset($amenity))
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-8">
            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Amenity Information</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name" class="form-label required">Amenity Name</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $amenity->name ?? '') }}" required placeholder="e.g., Free WiFi">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug', $amenity->slug ?? '') }}" placeholder="auto-generated-from-name">
                        <small class="form-text text-muted">Leave blank to auto-generate from name</small>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3" placeholder="Brief description of this amenity...">{{ old('description', $amenity->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="category" class="form-label">Category</label>
                                <select name="category" id="category" class="form-control @error('category') is-invalid @enderror">
                                    <option value="">-- Select Category --</option>
                                    <option value="room" {{ old('category', $amenity->category ?? '') === 'room' ? 'selected' : '' }}>Room</option>
                                    <option value="bathroom" {{ old('category', $amenity->category ?? '') === 'bathroom' ? 'selected' : '' }}>Bathroom</option>
                                    <option value="technology" {{ old('category', $amenity->category ?? '') === 'technology' ? 'selected' : '' }}>Technology</option>
                                    <option value="food" {{ old('category', $amenity->category ?? '') === 'food' ? 'selected' : '' }}>Food & Beverage</option>
                                    <option value="wellness" {{ old('category', $amenity->category ?? '') === 'wellness' ? 'selected' : '' }}>Wellness</option>
                                    <option value="services" {{ old('category', $amenity->category ?? '') === 'services' ? 'selected' : '' }}>Services</option>
                                    <option value="accessibility" {{ old('category', $amenity->category ?? '') === 'accessibility' ? 'selected' : '' }}>Accessibility</option>
                                    <option value="parking" {{ old('category', $amenity->category ?? '') === 'parking' ? 'selected' : '' }}>Parking</option>
                                    <option value="other" {{ old('category', $amenity->category ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="type" class="form-label">Type</label>
                                <select name="type" id="type" class="form-control @error('type') is-invalid @enderror">
                                    <option value="room" {{ old('type', $amenity->type ?? 'room') === 'room' ? 'selected' : '' }}>Room Amenity</option>
                                    <option value="property" {{ old('type', $amenity->type ?? '') === 'property' ? 'selected' : '' }}>Property Amenity</option>
                                    <option value="both" {{ old('type', $amenity->type ?? '') === 'both' ? 'selected' : '' }}>Both</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Icon Selection -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Icon Selection</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="icon" class="form-label">Icon Class</label>
                        <div class="input-group">
                            <span class="input-group-text" id="icon-preview">
                                <i class="{{ old('icon', $amenity->icon ?? 'fas fa-check') }}"></i>
                            </span>
                            <input type="text" name="icon" id="icon" class="form-control @error('icon') is-invalid @enderror"
                                   value="{{ old('icon', $amenity->icon ?? '') }}" placeholder="fas fa-wifi">
                        </div>
                        <small class="form-text text-muted">
                            Use Font Awesome icon classes (e.g., fas fa-wifi, fas fa-parking, fas fa-swimming-pool)
                        </small>
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="icon-picker mt-3">
                        <label class="form-label">Quick Select Icons</label>
                        <div class="icon-grid">
                            @php
                                $icons = [
                                    'fas fa-wifi' => 'WiFi',
                                    'fas fa-parking' => 'Parking',
                                    'fas fa-swimming-pool' => 'Pool',
                                    'fas fa-spa' => 'Spa',
                                    'fas fa-dumbbell' => 'Gym',
                                    'fas fa-tv' => 'TV',
                                    'fas fa-snowflake' => 'AC',
                                    'fas fa-coffee' => 'Coffee',
                                    'fas fa-utensils' => 'Restaurant',
                                    'fas fa-concierge-bell' => 'Concierge',
                                    'fas fa-bath' => 'Bath',
                                    'fas fa-shower' => 'Shower',
                                    'fas fa-bed' => 'Bed',
                                    'fas fa-phone' => 'Phone',
                                    'fas fa-safe' => 'Safe',
                                    'fas fa-iron' => 'Iron',
                                    'fas fa-fan' => 'Fan',
                                    'fas fa-hot-tub' => 'Hot Tub',
                                    'fas fa-wheelchair' => 'Accessible',
                                    'fas fa-suitcase' => 'Storage',
                                    'fas fa-wind' => 'Heating',
                                    'fas fa-fire' => 'Fireplace',
                                    'fas fa-couch' => 'Lounge',
                                    'fas fa-door-open' => 'Balcony',
                                ];
                            @endphp
                            @foreach($icons as $iconClass => $label)
                                <button type="button" class="icon-option" data-icon="{{ $iconClass }}" title="{{ $label }}">
                                    <i class="{{ $iconClass }}"></i>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-4">
            <!-- Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Status</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', $amenity->is_active ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                            <span class="toggle-label">Active</span>
                        </label>
                        <small class="form-text text-muted d-block mt-2">
                            Inactive amenities won't appear in room selections
                        </small>
                    </div>

                    <div class="form-group mt-3">
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_featured" value="1"
                                   {{ old('is_featured', $amenity->is_featured ?? false) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                            <span class="toggle-label">Featured</span>
                        </label>
                        <small class="form-text text-muted d-block mt-2">
                            Featured amenities are highlighted on the website
                        </small>
                    </div>
                </div>
            </div>

            <!-- Display Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Display Settings</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror"
                               value="{{ old('sort_order', $amenity->sort_order ?? 0) }}" min="0">
                        <small class="form-text text-muted">Lower numbers appear first</small>
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column gap-2">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> {{ isset($amenity) ? 'Update Amenity' : 'Create Amenity' }}
                        </button>
                        <a href="{{ route('admin.amenities.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('styles')
<style>
.icon-grid {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 0.5rem;
}

.icon-option {
    width: 40px;
    height: 40px;
    border: 1px solid var(--gray-200);
    border-radius: 6px;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.icon-option:hover {
    border-color: var(--primary);
    color: var(--primary);
}

.icon-option.selected {
    border-color: var(--primary);
    background: var(--primary);
    color: white;
}

.input-group-text {
    min-width: 42px;
    justify-content: center;
}

.toggle-switch {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
}

.toggle-switch input {
    display: none;
}

.toggle-slider {
    position: relative;
    width: 44px;
    height: 24px;
    background: var(--gray-300);
    border-radius: 12px;
    transition: 0.3s;
}

.toggle-slider:before {
    content: '';
    position: absolute;
    width: 18px;
    height: 18px;
    left: 3px;
    top: 3px;
    background: white;
    border-radius: 50%;
    transition: 0.3s;
}

.toggle-switch input:checked + .toggle-slider {
    background: var(--primary);
}

.toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(20px);
}

.toggle-label {
    font-weight: 500;
}

.btn-block {
    width: 100%;
    text-align: center;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const iconInput = document.getElementById('icon');
    const iconPreview = document.getElementById('icon-preview');
    const iconOptions = document.querySelectorAll('.icon-option');

    // Update preview on input change
    iconInput.addEventListener('input', function() {
        iconPreview.innerHTML = '<i class="' + this.value + '"></i>';
        updateSelectedIcon(this.value);
    });

    // Icon picker click
    iconOptions.forEach(function(option) {
        option.addEventListener('click', function() {
            const iconClass = this.getAttribute('data-icon');
            iconInput.value = iconClass;
            iconPreview.innerHTML = '<i class="' + iconClass + '"></i>';
            updateSelectedIcon(iconClass);
        });
    });

    // Initial selection
    updateSelectedIcon(iconInput.value);

    function updateSelectedIcon(value) {
        iconOptions.forEach(function(option) {
            if (option.getAttribute('data-icon') === value) {
                option.classList.add('selected');
            } else {
                option.classList.remove('selected');
            }
        });
    }

    // Auto-generate slug from name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    nameInput.addEventListener('blur', function() {
        if (!slugInput.value) {
            slugInput.value = this.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-|-$/g, '');
        }
    });
});
</script>
@endpush
