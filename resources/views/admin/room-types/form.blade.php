@extends('admin.layouts.app')

@section('title', isset($roomType) ? 'Edit Room Type' : 'Add Room Type')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <a href="{{ route('admin.room-types.index') }}">Room Types</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>{{ isset($roomType) ? 'Edit Room Type' : 'Add Room Type' }}</span>
@endsection

@section('content')
<form action="{{ isset($roomType) ? route('admin.room-types.update', $roomType) : route('admin.room-types.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($roomType))
        @method('PUT')
    @endif

    <div class="page-header">
        <div>
            <h1 class="page-title">{{ isset($roomType) ? 'Edit Room Type' : 'Add Room Type' }}</h1>
            <p class="page-subtitle">{{ isset($roomType) ? 'Update room type details and pricing' : 'Create a new room category' }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.room-types.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ isset($roomType) ? 'Update Room Type' : 'Create Room Type' }}
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
                        <div class="col-8">
                            <div class="form-group">
                                <label class="form-label">Room Type Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $roomType->name ?? '') }}"
                                       placeholder="e.g., Deluxe Suite" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Code/SKU</label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                       value="{{ old('code', $roomType->code ?? '') }}"
                                       placeholder="e.g., DLX-S">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Short Description</label>
                        <input type="text" name="short_description" class="form-control @error('short_description') is-invalid @enderror"
                               value="{{ old('short_description', $roomType->short_description ?? '') }}"
                               placeholder="Brief tagline for listings">
                        @error('short_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Full Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="4" placeholder="Detailed room description">{{ old('description', $roomType->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Room Specifications -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Room Specifications</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Max Occupancy <span class="text-danger">*</span></label>
                                <input type="number" name="max_occupancy" class="form-control @error('max_occupancy') is-invalid @enderror"
                                       value="{{ old('max_occupancy', $roomType->max_occupancy ?? 2) }}"
                                       min="1" max="20" required>
                                @error('max_occupancy')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Max Adults</label>
                                <input type="number" name="max_adults" class="form-control @error('max_adults') is-invalid @enderror"
                                       value="{{ old('max_adults', $roomType->max_adults ?? 2) }}"
                                       min="1" max="10">
                                @error('max_adults')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Max Children</label>
                                <input type="number" name="max_children" class="form-control @error('max_children') is-invalid @enderror"
                                       value="{{ old('max_children', $roomType->max_children ?? 2) }}"
                                       min="0" max="10">
                                @error('max_children')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Bed Type</label>
                                <select name="bed_type" class="form-control @error('bed_type') is-invalid @enderror">
                                    <option value="">Select Bed Type</option>
                                    <option value="single" {{ old('bed_type', $roomType->bed_type ?? '') == 'single' ? 'selected' : '' }}>Single</option>
                                    <option value="double" {{ old('bed_type', $roomType->bed_type ?? '') == 'double' ? 'selected' : '' }}>Double</option>
                                    <option value="queen" {{ old('bed_type', $roomType->bed_type ?? '') == 'queen' ? 'selected' : '' }}>Queen</option>
                                    <option value="king" {{ old('bed_type', $roomType->bed_type ?? '') == 'king' ? 'selected' : '' }}>King</option>
                                    <option value="twin" {{ old('bed_type', $roomType->bed_type ?? '') == 'twin' ? 'selected' : '' }}>Twin</option>
                                </select>
                                @error('bed_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Number of Beds</label>
                                <input type="number" name="bed_count" class="form-control @error('bed_count') is-invalid @enderror"
                                       value="{{ old('bed_count', $roomType->bed_count ?? 1) }}"
                                       min="1" max="10">
                                @error('bed_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Room Size (m²)</label>
                                <input type="number" name="size" class="form-control @error('size') is-invalid @enderror"
                                       value="{{ old('size', $roomType->size ?? '') }}"
                                       min="1" step="0.1">
                                @error('size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">View Type</label>
                        <select name="view_type" class="form-control @error('view_type') is-invalid @enderror">
                            <option value="">Select View</option>
                            <option value="city" {{ old('view_type', $roomType->view_type ?? '') == 'city' ? 'selected' : '' }}>City View</option>
                            <option value="garden" {{ old('view_type', $roomType->view_type ?? '') == 'garden' ? 'selected' : '' }}>Garden View</option>
                            <option value="pool" {{ old('view_type', $roomType->view_type ?? '') == 'pool' ? 'selected' : '' }}>Pool View</option>
                            <option value="ocean" {{ old('view_type', $roomType->view_type ?? '') == 'ocean' ? 'selected' : '' }}>Ocean View</option>
                            <option value="mountain" {{ old('view_type', $roomType->view_type ?? '') == 'mountain' ? 'selected' : '' }}>Mountain View</option>
                            <option value="courtyard" {{ old('view_type', $roomType->view_type ?? '') == 'courtyard' ? 'selected' : '' }}>Courtyard View</option>
                        </select>
                        @error('view_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Pricing</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Base Price/Night <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="base_price" class="form-control @error('base_price') is-invalid @enderror"
                                           value="{{ old('base_price', $roomType->base_price ?? '') }}"
                                           min="0" step="0.01" required>
                                </div>
                                @error('base_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Weekend Price/Night</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="weekend_price" class="form-control @error('weekend_price') is-invalid @enderror"
                                           value="{{ old('weekend_price', $roomType->weekend_price ?? '') }}"
                                           min="0" step="0.01">
                                </div>
                                @error('weekend_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Extra Person Fee</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="extra_person_fee" class="form-control @error('extra_person_fee') is-invalid @enderror"
                                           value="{{ old('extra_person_fee', $roomType->extra_person_fee ?? 0) }}"
                                           min="0" step="0.01">
                                </div>
                                @error('extra_person_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amenities -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Amenities</h3>
                </div>
                <div class="card-body">
                    <div class="amenities-grid">
                        @foreach($amenities ?? [] as $amenity)
                        <label class="form-check amenity-item">
                            <input type="checkbox"
                                   name="amenities[]"
                                   value="{{ $amenity->id }}"
                                   class="form-check-input"
                                   {{ in_array($amenity->id, old('amenities', isset($roomType) ? $roomType->amenities->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                            <span class="form-check-label">
                                @if($amenity->icon)
                                    <i class="{{ $amenity->icon }}"></i>
                                @endif
                                {{ $amenity->name }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                    @if(empty($amenities) || count($amenities) === 0)
                    <p class="text-muted mb-0">
                        No amenities available. <a href="{{ route('admin.amenities.create') }}">Create amenities</a> first.
                    </p>
                    @endif
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
                    <div class="form-group mb-0">
                        <label class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input"
                                   {{ old('is_active', $roomType->is_active ?? true) ? 'checked' : '' }}>
                            <span class="form-check-label">
                                <strong>Active</strong>
                                <p class="text-muted mb-0" style="font-size: 0.75rem;">Room type is available for booking</p>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Images -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Images</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Featured Image</label>
                        @if(isset($roomType) && $roomType->featured_image)
                        <div class="mb-2">
                            <img src="{{ $roomType->featured_image }}" alt="Current Image"
                                 style="width: 100%; max-height: 150px; object-fit: cover; border-radius: 4px;">
                        </div>
                        @endif
                        <input type="file" name="featured_image" class="form-control @error('featured_image') is-invalid @enderror"
                               accept="image/*">
                        <small class="text-muted">Recommended: 800x600px, JPG/PNG</small>
                        @error('featured_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Gallery Images</label>
                        <input type="file" name="gallery[]" class="form-control @error('gallery') is-invalid @enderror"
                               accept="image/*" multiple>
                        <small class="text-muted">Select multiple images for the gallery</small>
                        @error('gallery')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Display Order -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Display Settings</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror"
                               value="{{ old('sort_order', $roomType->sort_order ?? 0) }}"
                               min="0">
                        <small class="text-muted">Lower numbers appear first</small>
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-check">
                            <input type="checkbox" name="is_featured" value="1" class="form-check-input"
                                   {{ old('is_featured', $roomType->is_featured ?? false) ? 'checked' : '' }}>
                            <span class="form-check-label">
                                <strong>Featured Room Type</strong>
                                <p class="text-muted mb-0" style="font-size: 0.75rem;">Show on homepage and promotions</p>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            @if(isset($roomType))
            <!-- Room Type Stats -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Statistics</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                        <span class="text-muted">Total Rooms</span>
                        <strong>{{ $roomType->rooms_count ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                        <span class="text-muted">Total Bookings</span>
                        <strong>{{ $roomType->bookings_count ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-between py-2">
                        <span class="text-muted">Created</span>
                        <span>{{ $roomType->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</form>

<style>
.amenities-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
}

.amenity-item {
    padding: 0.5rem;
    border: 1px solid var(--gray-200);
    border-radius: 4px;
    transition: all 0.15s;
}

.amenity-item:hover {
    background: var(--gray-50);
}

.amenity-item i {
    margin-right: 0.5rem;
    color: var(--gray-500);
}

.input-group {
    display: flex;
}

.input-group-text {
    padding: 0.5rem 0.75rem;
    background: var(--gray-100);
    border: 1px solid var(--gray-300);
    border-right: none;
    border-radius: 0.375rem 0 0 0.375rem;
    color: var(--gray-600);
}

.input-group .form-control {
    border-radius: 0 0.375rem 0.375rem 0;
}
</style>
@endsection
