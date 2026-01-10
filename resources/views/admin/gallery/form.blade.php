@extends('admin.layouts.app')

@section('title', isset($image) ? 'Edit Image' : 'Add Image')

@section('breadcrumb')
    <a href="{{ route('admin.gallery.index') }}">Gallery</a>
    <i class="fas fa-chevron-right"></i>
    <span>{{ isset($image) ? 'Edit' : 'Add' }}</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ isset($image) ? 'Edit Image' : 'Add New Image' }}</h1>
        <p class="page-subtitle">{{ isset($image) ? 'Update image details' : 'Upload a new image to the gallery' }}</p>
    </div>
</div>

<form action="{{ isset($image) ? route('admin.gallery.update', $image) : route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($image))
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-8">
            <!-- Image Upload -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Image</h3>
                </div>
                <div class="card-body">
                    @if(isset($image))
                        <div class="current-image mb-4">
                            <label class="form-label">Current Image</label>
                            <div class="image-preview-large">
                                <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $image->title }}">
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="image" class="form-label {{ isset($image) ? '' : 'required' }}">
                            {{ isset($image) ? 'Replace Image' : 'Upload Image' }}
                        </label>
                        <div class="image-upload-zone" id="uploadZone">
                            <input type="file" name="image" id="image" accept="image/*"
                                   class="@error('image') is-invalid @enderror" {{ isset($image) ? '' : 'required' }}>
                            <div class="upload-placeholder">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Drag & drop an image here or click to browse</p>
                                <small>Supported formats: JPG, PNG, GIF, WebP (max 5MB)</small>
                            </div>
                            <div class="upload-preview" style="display: none;">
                                <img src="" alt="Preview">
                            </div>
                        </div>
                        @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Image Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Image Details</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $image->title ?? '') }}" placeholder="Image title">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="alt_text" class="form-label">Alt Text</label>
                        <input type="text" name="alt_text" id="alt_text" class="form-control @error('alt_text') is-invalid @enderror"
                               value="{{ old('alt_text', $image->alt_text ?? '') }}" placeholder="Descriptive alt text for accessibility">
                        <small class="form-text text-muted">Important for SEO and accessibility</small>
                        @error('alt_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3" placeholder="Optional description...">{{ old('description', $image->description ?? '') }}</textarea>
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
                                    <option value="rooms" {{ old('category', $image->category ?? '') === 'rooms' ? 'selected' : '' }}>Rooms</option>
                                    <option value="facilities" {{ old('category', $image->category ?? '') === 'facilities' ? 'selected' : '' }}>Facilities</option>
                                    <option value="dining" {{ old('category', $image->category ?? '') === 'dining' ? 'selected' : '' }}>Dining</option>
                                    <option value="exterior" {{ old('category', $image->category ?? '') === 'exterior' ? 'selected' : '' }}>Exterior</option>
                                    <option value="lobby" {{ old('category', $image->category ?? '') === 'lobby' ? 'selected' : '' }}>Lobby</option>
                                    <option value="events" {{ old('category', $image->category ?? '') === 'events' ? 'selected' : '' }}>Events</option>
                                    <option value="spa" {{ old('category', $image->category ?? '') === 'spa' ? 'selected' : '' }}>Spa & Wellness</option>
                                    <option value="other" {{ old('category', $image->category ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="room_type_id" class="form-label">Associated Room Type</label>
                                <select name="room_type_id" id="room_type_id" class="form-control @error('room_type_id') is-invalid @enderror">
                                    <option value="">-- None --</option>
                                    @foreach($roomTypes ?? [] as $roomType)
                                        <option value="{{ $roomType->id }}" {{ old('room_type_id', $image->room_type_id ?? '') == $roomType->id ? 'selected' : '' }}>
                                            {{ $roomType->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-4">
            <!-- Display Options -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Display Options</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_slider" value="1"
                                   {{ old('is_slider', $image->is_slider ?? false) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                            <span class="toggle-label">Show in Slider</span>
                        </label>
                        <small class="form-text text-muted d-block mt-2">
                            Include this image in the homepage slider
                        </small>
                    </div>

                    <div class="form-group mt-3">
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_featured" value="1"
                                   {{ old('is_featured', $image->is_featured ?? false) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                            <span class="toggle-label">Featured Image</span>
                        </label>
                        <small class="form-text text-muted d-block mt-2">
                            Featured images are highlighted in gallery
                        </small>
                    </div>

                    <div class="form-group mt-3">
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', $image->is_active ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                            <span class="toggle-label">Active</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Slider Settings (conditional) -->
            <div class="card mb-4" id="sliderSettings" style="{{ old('is_slider', $image->is_slider ?? false) ? '' : 'display:none;' }}">
                <div class="card-header">
                    <h3 class="card-title">Slider Settings</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="slider_title" class="form-label">Slider Title</label>
                        <input type="text" name="slider_title" id="slider_title" class="form-control"
                               value="{{ old('slider_title', $image->slider_title ?? '') }}" placeholder="Text overlay title">
                    </div>

                    <div class="form-group">
                        <label for="slider_subtitle" class="form-label">Slider Subtitle</label>
                        <input type="text" name="slider_subtitle" id="slider_subtitle" class="form-control"
                               value="{{ old('slider_subtitle', $image->slider_subtitle ?? '') }}" placeholder="Text overlay subtitle">
                    </div>

                    <div class="form-group">
                        <label for="slider_link" class="form-label">Button Link</label>
                        <input type="text" name="slider_link" id="slider_link" class="form-control"
                               value="{{ old('slider_link', $image->slider_link ?? '') }}" placeholder="/rooms">
                    </div>

                    <div class="form-group">
                        <label for="slider_button_text" class="form-label">Button Text</label>
                        <input type="text" name="slider_button_text" id="slider_button_text" class="form-control"
                               value="{{ old('slider_button_text', $image->slider_button_text ?? '') }}" placeholder="View Rooms">
                    </div>
                </div>
            </div>

            <!-- Sort Order -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Sort Order</h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-0">
                        <label for="sort_order" class="form-label">Display Order</label>
                        <input type="number" name="sort_order" id="sort_order" class="form-control"
                               value="{{ old('sort_order', $image->sort_order ?? 0) }}" min="0">
                        <small class="form-text text-muted">Lower numbers appear first</small>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column gap-2">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> {{ isset($image) ? 'Update Image' : 'Upload Image' }}
                        </button>
                        <a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary btn-block">
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
.image-upload-zone {
    border: 2px dashed var(--gray-300);
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s;
    position: relative;
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.image-upload-zone:hover,
.image-upload-zone.dragover {
    border-color: var(--primary);
    background: var(--gray-50);
}

.image-upload-zone input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
}

.upload-placeholder i {
    font-size: 3rem;
    color: var(--gray-400);
    margin-bottom: 1rem;
}

.upload-placeholder p {
    margin: 0.5rem 0;
    color: var(--gray-600);
}

.upload-placeholder small {
    color: var(--gray-500);
}

.upload-preview {
    width: 100%;
}

.upload-preview img {
    max-width: 100%;
    max-height: 300px;
    border-radius: 8px;
}

.image-preview-large {
    border-radius: 8px;
    overflow: hidden;
    background: var(--gray-100);
}

.image-preview-large img {
    width: 100%;
    height: auto;
    display: block;
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
    flex-shrink: 0;
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
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = uploadZone.querySelector('input[type="file"]');
    const placeholder = uploadZone.querySelector('.upload-placeholder');
    const preview = uploadZone.querySelector('.upload-preview');
    const previewImg = preview.querySelector('img');

    // Drag and drop
    uploadZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });

    uploadZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });

    uploadZone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            showPreview(e.dataTransfer.files[0]);
        }
    });

    // File input change
    fileInput.addEventListener('change', function() {
        if (this.files.length) {
            showPreview(this.files[0]);
        }
    });

    function showPreview(file) {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                placeholder.style.display = 'none';
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    // Slider settings toggle
    const sliderCheckbox = document.querySelector('input[name="is_slider"]');
    const sliderSettings = document.getElementById('sliderSettings');

    sliderCheckbox.addEventListener('change', function() {
        sliderSettings.style.display = this.checked ? 'block' : 'none';
    });
});
</script>
@endpush
