@extends('admin.layouts.app')

@section('title', isset($room) ? 'Edit Room' : 'Add Room')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <a href="{{ route('admin.rooms.index') }}">Rooms</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>{{ isset($room) ? 'Edit Room' : 'Add Room' }}</span>
@endsection

@section('content')
<form action="{{ isset($room) ? route('admin.rooms.update', $room) : route('admin.rooms.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($room))
        @method('PUT')
    @endif

    <div class="page-header">
        <div>
            <h1 class="page-title">{{ isset($room) ? 'Edit Room' : 'Add Room' }}</h1>
            <p class="page-subtitle">{{ isset($room) ? 'Update room details and availability' : 'Add a new room to inventory' }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.rooms.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ isset($room) ? 'Update Room' : 'Create Room' }}
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-8">
            <!-- Basic Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Room Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Room Number <span class="text-danger">*</span></label>
                                <input type="text" name="room_number" class="form-control @error('room_number') is-invalid @enderror"
                                       value="{{ old('room_number', $room->room_number ?? '') }}" required
                                       placeholder="e.g., 101, A-201">
                                @error('room_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Room Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $room->name ?? '') }}"
                                       placeholder="e.g., Ocean View Suite">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Floor</label>
                                <select name="floor" class="form-control @error('floor') is-invalid @enderror">
                                    @for($i = 1; $i <= 20; $i++)
                                        <option value="{{ $i }}" {{ old('floor', $room->floor ?? 1) == $i ? 'selected' : '' }}>
                                            Floor {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                @error('floor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Room Type <span class="text-danger">*</span></label>
                                <select name="room_type_id" class="form-control @error('room_type_id') is-invalid @enderror" required>
                                    <option value="">Select Room Type</option>
                                    @foreach($roomTypes ?? [] as $roomType)
                                        <option value="{{ $roomType->id }}" {{ old('room_type_id', $room->room_type_id ?? '') == $roomType->id ? 'selected' : '' }}>
                                            {{ $roomType->name }} - ${{ number_format($roomType->base_price, 2) }}/night
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">View Type</label>
                                <select name="view_type" class="form-control @error('view_type') is-invalid @enderror">
                                    <option value="">No specific view</option>
                                    <option value="ocean" {{ old('view_type', $room->view_type ?? '') == 'ocean' ? 'selected' : '' }}>Ocean View</option>
                                    <option value="garden" {{ old('view_type', $room->view_type ?? '') == 'garden' ? 'selected' : '' }}>Garden View</option>
                                    <option value="pool" {{ old('view_type', $room->view_type ?? '') == 'pool' ? 'selected' : '' }}>Pool View</option>
                                    <option value="city" {{ old('view_type', $room->view_type ?? '') == 'city' ? 'selected' : '' }}>City View</option>
                                    <option value="mountain" {{ old('view_type', $room->view_type ?? '') == 'mountain' ? 'selected' : '' }}>Mountain View</option>
                                    <option value="courtyard" {{ old('view_type', $room->view_type ?? '') == 'courtyard' ? 'selected' : '' }}>Courtyard View</option>
                                </select>
                                @error('view_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3"
                                  placeholder="Special features or notes about this specific room">{{ old('description', $room->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Capacity & Beds -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Capacity & Bedding</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Max Occupancy <span class="text-danger">*</span></label>
                                <select name="max_occupancy" class="form-control @error('max_occupancy') is-invalid @enderror" required>
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ old('max_occupancy', $room->max_occupancy ?? 2) == $i ? 'selected' : '' }}>
                                            {{ $i }} {{ $i == 1 ? 'Guest' : 'Guests' }}
                                        </option>
                                    @endfor
                                </select>
                                @error('max_occupancy')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Size (sqm)</label>
                                <input type="number" name="size" class="form-control @error('size') is-invalid @enderror"
                                       value="{{ old('size', $room->size ?? '') }}" min="0" step="0.5"
                                       placeholder="e.g., 35">
                                @error('size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Bathroom Type</label>
                                <select name="bathroom_type" class="form-control @error('bathroom_type') is-invalid @enderror">
                                    <option value="private" {{ old('bathroom_type', $room->bathroom_type ?? 'private') == 'private' ? 'selected' : '' }}>Private Bathroom</option>
                                    <option value="shared" {{ old('bathroom_type', $room->bathroom_type ?? '') == 'shared' ? 'selected' : '' }}>Shared Bathroom</option>
                                </select>
                                @error('bathroom_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Bed Configuration</label>
                        <div class="beds-config" id="bedsConfig">
                            @php
                                $beds = old('beds', isset($room) ? $room->beds : [['type' => 'king', 'count' => 1]]);
                            @endphp
                            @foreach($beds as $index => $bed)
                            <div class="bed-row d-flex gap-3 align-items-end mb-2">
                                <div style="flex: 1;">
                                    <select name="beds[{{ $index }}][type]" class="form-control">
                                        <option value="king" {{ ($bed['type'] ?? '') == 'king' ? 'selected' : '' }}>King Bed</option>
                                        <option value="queen" {{ ($bed['type'] ?? '') == 'queen' ? 'selected' : '' }}>Queen Bed</option>
                                        <option value="double" {{ ($bed['type'] ?? '') == 'double' ? 'selected' : '' }}>Double Bed</option>
                                        <option value="single" {{ ($bed['type'] ?? '') == 'single' ? 'selected' : '' }}>Single Bed</option>
                                        <option value="twin" {{ ($bed['type'] ?? '') == 'twin' ? 'selected' : '' }}>Twin Beds</option>
                                        <option value="sofa" {{ ($bed['type'] ?? '') == 'sofa' ? 'selected' : '' }}>Sofa Bed</option>
                                        <option value="bunk" {{ ($bed['type'] ?? '') == 'bunk' ? 'selected' : '' }}>Bunk Bed</option>
                                    </select>
                                </div>
                                <div style="width: 100px;">
                                    <input type="number" name="beds[{{ $index }}][count]" class="form-control"
                                           value="{{ $bed['count'] ?? 1 }}" min="1" max="10" placeholder="Count">
                                </div>
                                <button type="button" class="btn btn-danger btn-sm remove-bed" {{ count($beds) == 1 ? 'disabled' : '' }}>
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm mt-2" id="addBed">
                            <i class="fas fa-plus"></i> Add Bed
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pricing Override -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Pricing Override</h3>
                    <p class="text-muted mb-0" style="font-size: 0.75rem;">Leave blank to use room type default pricing</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Price Adjustment</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="price_adjustment" class="form-control @error('price_adjustment') is-invalid @enderror"
                                           value="{{ old('price_adjustment', $room->price_adjustment ?? '') }}"
                                           step="0.01" placeholder="0.00">
                                </div>
                                <small class="text-muted">Additional charge for this room</small>
                                @error('price_adjustment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Custom Rate</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="custom_rate" class="form-control @error('custom_rate') is-invalid @enderror"
                                           value="{{ old('custom_rate', $room->custom_rate ?? '') }}"
                                           step="0.01" placeholder="0.00">
                                </div>
                                <small class="text-muted">Override base price entirely</small>
                                @error('custom_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Extra Guest Fee</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="extra_guest_fee" class="form-control @error('extra_guest_fee') is-invalid @enderror"
                                           value="{{ old('extra_guest_fee', $room->extra_guest_fee ?? '') }}"
                                           step="0.01" placeholder="0.00">
                                </div>
                                <small class="text-muted">Per extra guest/night</small>
                                @error('extra_guest_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Room Images -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Room Images</h3>
                </div>
                <div class="card-body">
                    <div class="upload-zone" id="uploadZone">
                        <input type="file" name="images[]" id="imageInput" multiple accept="image/*" style="display: none;">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: var(--gray-400);"></i>
                        <p class="mb-1">Drop images here or click to upload</p>
                        <small class="text-muted">Supports JPG, PNG, WebP up to 5MB each</small>
                    </div>

                    <div class="preview-images mt-3" id="previewImages">
                        @if(isset($room) && $room->images)
                            @foreach($room->images as $image)
                            <div class="preview-image">
                                <img src="{{ asset('storage/' . $image->path) }}" alt="">
                                <button type="button" class="remove-image" data-id="{{ $image->id }}">
                                    <i class="fas fa-times"></i>
                                </button>
                                <input type="hidden" name="existing_images[]" value="{{ $image->id }}">
                            </div>
                            @endforeach
                        @endif
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
                        <label class="form-label">Room Status</label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror">
                            <option value="available" {{ old('status', $room->status ?? 'available') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="occupied" {{ old('status', $room->status ?? '') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                            <option value="reserved" {{ old('status', $room->status ?? '') == 'reserved' ? 'selected' : '' }}>Reserved</option>
                            <option value="maintenance" {{ old('status', $room->status ?? '') == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                            <option value="cleaning" {{ old('status', $room->status ?? '') == 'cleaning' ? 'selected' : '' }}>Being Cleaned</option>
                            <option value="out_of_service" {{ old('status', $room->status ?? '') == 'out_of_service' ? 'selected' : '' }}>Out of Service</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Cleanliness Status</label>
                        <select name="cleanliness" class="form-control @error('cleanliness') is-invalid @enderror">
                            <option value="clean" {{ old('cleanliness', $room->cleanliness ?? 'clean') == 'clean' ? 'selected' : '' }}>Clean</option>
                            <option value="dirty" {{ old('cleanliness', $room->cleanliness ?? '') == 'dirty' ? 'selected' : '' }}>Dirty</option>
                            <option value="inspected" {{ old('cleanliness', $room->cleanliness ?? '') == 'inspected' ? 'selected' : '' }}>Inspected</option>
                        </select>
                        @error('cleanliness')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input"
                                   {{ old('is_active', isset($room) ? $room->is_active : true) ? 'checked' : '' }}>
                            <span class="form-check-label">Active in Inventory</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Room Features</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="is_smoking" value="1" class="form-check-input"
                                   {{ old('is_smoking', $room->is_smoking ?? false) ? 'checked' : '' }}>
                            <span class="form-check-label">Smoking Allowed</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="is_accessible" value="1" class="form-check-input"
                                   {{ old('is_accessible', $room->is_accessible ?? false) ? 'checked' : '' }}>
                            <span class="form-check-label">Wheelchair Accessible</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="pets_allowed" value="1" class="form-check-input"
                                   {{ old('pets_allowed', $room->pets_allowed ?? false) ? 'checked' : '' }}>
                            <span class="form-check-label">Pets Allowed</span>
                        </label>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-check">
                            <input type="checkbox" name="has_balcony" value="1" class="form-check-input"
                                   {{ old('has_balcony', $room->has_balcony ?? false) ? 'checked' : '' }}>
                            <span class="form-check-label">Has Balcony/Terrace</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Maintenance Notes -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Maintenance Notes</h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-0">
                        <textarea name="maintenance_notes" class="form-control @error('maintenance_notes') is-invalid @enderror" rows="4"
                                  placeholder="Any maintenance or housekeeping notes...">{{ old('maintenance_notes', $room->maintenance_notes ?? '') }}</textarea>
                        @error('maintenance_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            @if(isset($room))
            <!-- Room Stats -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Room Statistics</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                        <span class="text-muted">Total Bookings</span>
                        <span>{{ $room->bookings_count ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                        <span class="text-muted">Total Revenue</span>
                        <span>${{ number_format($room->total_revenue ?? 0, 2) }}</span>
                    </div>
                    <div class="d-flex justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                        <span class="text-muted">Avg. Rating</span>
                        <span>
                            @if($room->avg_rating)
                                {{ number_format($room->avg_rating, 1) }}
                                <i class="fas fa-star" style="color: #ffc107; font-size: 0.75rem;"></i>
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                    <div class="d-flex justify-between py-2">
                        <span class="text-muted">Last Booked</span>
                        <span>{{ $room->last_booking_date ? $room->last_booking_date->format('M d, Y') : 'Never' }}</span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</form>

<style>
.upload-zone {
    border: 2px dashed var(--gray-300);
    border-radius: 0.5rem;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.15s;
}

.upload-zone:hover,
.upload-zone.dragover {
    border-color: var(--primary);
    background: var(--primary-light);
}

.preview-images {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
}

.preview-image {
    position: relative;
    aspect-ratio: 4/3;
    border-radius: 0.375rem;
    overflow: hidden;
}

.preview-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-image .remove-image {
    position: absolute;
    top: 0.25rem;
    right: 0.25rem;
    width: 1.5rem;
    height: 1.5rem;
    border: none;
    border-radius: 50%;
    background: rgba(0,0,0,0.5);
    color: white;
    cursor: pointer;
    font-size: 0.625rem;
}

.preview-image .remove-image:hover {
    background: var(--danger);
}

.bed-row {
    background: var(--gray-50);
    padding: 0.75rem;
    border-radius: 0.375rem;
}
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadZone = document.getElementById('uploadZone');
    const imageInput = document.getElementById('imageInput');
    const previewImages = document.getElementById('previewImages');
    const bedsConfig = document.getElementById('bedsConfig');
    let bedIndex = {{ count(old('beds', isset($room) ? $room->beds : [['type' => 'king', 'count' => 1]])) }};

    // Upload zone click
    uploadZone.addEventListener('click', () => imageInput.click());

    // Drag and drop
    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.classList.add('dragover');
    });

    uploadZone.addEventListener('dragleave', () => {
        uploadZone.classList.remove('dragover');
    });

    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });

    imageInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    function handleFiles(files) {
        Array.from(files).forEach(file => {
            if (!file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                const div = document.createElement('div');
                div.className = 'preview-image';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="">
                    <button type="button" class="remove-image">
                        <i class="fas fa-times"></i>
                    </button>
                `;

                div.querySelector('.remove-image').addEventListener('click', () => {
                    div.remove();
                });

                previewImages.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }

    // Remove existing images
    document.querySelectorAll('.preview-image .remove-image[data-id]').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.preview-image').remove();
        });
    });

    // Add bed
    document.getElementById('addBed').addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'bed-row d-flex gap-3 align-items-end mb-2';
        row.innerHTML = `
            <div style="flex: 1;">
                <select name="beds[${bedIndex}][type]" class="form-control">
                    <option value="king">King Bed</option>
                    <option value="queen">Queen Bed</option>
                    <option value="double">Double Bed</option>
                    <option value="single">Single Bed</option>
                    <option value="twin">Twin Beds</option>
                    <option value="sofa">Sofa Bed</option>
                    <option value="bunk">Bunk Bed</option>
                </select>
            </div>
            <div style="width: 100px;">
                <input type="number" name="beds[${bedIndex}][count]" class="form-control" value="1" min="1" max="10" placeholder="Count">
            </div>
            <button type="button" class="btn btn-danger btn-sm remove-bed">
                <i class="fas fa-times"></i>
            </button>
        `;

        row.querySelector('.remove-bed').addEventListener('click', () => {
            row.remove();
            updateRemoveButtons();
        });

        bedsConfig.appendChild(row);
        bedIndex++;
        updateRemoveButtons();
    });

    // Remove bed
    document.querySelectorAll('.remove-bed').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.bed-row').remove();
            updateRemoveButtons();
        });
    });

    function updateRemoveButtons() {
        const rows = bedsConfig.querySelectorAll('.bed-row');
        rows.forEach(row => {
            row.querySelector('.remove-bed').disabled = rows.length === 1;
        });
    }
});
</script>
@endpush
