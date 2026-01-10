@extends('admin.layouts.app')

@section('title', 'Hero Slides')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <a href="{{ route('admin.settings.index') }}">Settings</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>Hero Slides</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Hero Slides</h1>
        <p class="page-subtitle">Manage homepage hero slider images and content</p>
    </div>
    <div class="page-actions">
        <button type="button" class="btn btn-primary" onclick="openModal('addSlideModal')">
            <i class="fas fa-plus"></i> Add Slide
        </button>
    </div>
</div>

<div class="row">
    <div class="col-3">
        @include('admin.settings.partials.sidebar')
    </div>

    <div class="col-9">
        <!-- Slides List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Slides</h3>
                <p class="text-muted mb-0" style="font-size: 0.75rem;">Drag to reorder slides</p>
            </div>
            <div class="card-body p-0">
                <div class="slides-list" id="slidesList">
                    @forelse($slides ?? [] as $slide)
                    <div class="slide-item" data-id="{{ $slide->id }}">
                        <div class="slide-drag-handle">
                            <i class="fas fa-grip-vertical"></i>
                        </div>
                        <div class="slide-image">
                            @if($slide->image)
                                <img src="{{ asset('storage/' . $slide->image) }}" alt="">
                            @else
                                <div class="slide-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </div>
                        <div class="slide-content">
                            <h4>{{ $slide->title ?? 'Untitled Slide' }}</h4>
                            @if($slide->subtitle)
                                <p class="text-muted mb-1">{{ $slide->subtitle }}</p>
                            @endif
                            @if($slide->button_text && $slide->button_url)
                                <span class="badge badge-secondary">
                                    <i class="fas fa-link"></i> {{ $slide->button_text }}
                                </span>
                            @endif
                        </div>
                        <div class="slide-status">
                            <label class="toggle-switch">
                                <input type="checkbox" class="slide-toggle" data-id="{{ $slide->id }}"
                                       {{ $slide->is_active ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="slide-actions">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="editSlide({{ $slide->id }})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteSlide({{ $slide->id }})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state py-5">
                        <i class="fas fa-images" style="font-size: 3rem; color: var(--gray-300);"></i>
                        <p class="text-muted mt-3 mb-2">No hero slides yet</p>
                        <button type="button" class="btn btn-primary btn-sm" onclick="openModal('addSlideModal')">
                            <i class="fas fa-plus"></i> Add First Slide
                        </button>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Slider Settings -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">Slider Settings</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.hero-slides.settings') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Autoplay Interval (seconds)</label>
                                <input type="number" name="slider_interval" class="form-control" min="1" max="30"
                                       value="{{ old('slider_interval', $sliderSettings['interval'] ?? '5') }}">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Transition Effect</label>
                                <select name="slider_effect" class="form-control">
                                    <option value="fade" {{ ($sliderSettings['effect'] ?? 'fade') == 'fade' ? 'selected' : '' }}>Fade</option>
                                    <option value="slide" {{ ($sliderSettings['effect'] ?? '') == 'slide' ? 'selected' : '' }}>Slide</option>
                                    <option value="zoom" {{ ($sliderSettings['effect'] ?? '') == 'zoom' ? 'selected' : '' }}>Zoom</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Transition Speed (ms)</label>
                                <input type="number" name="slider_speed" class="form-control" min="100" max="2000" step="100"
                                       value="{{ old('slider_speed', $sliderSettings['speed'] ?? '500') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="slider_autoplay" value="1" class="form-check-input"
                                   {{ old('slider_autoplay', $sliderSettings['autoplay'] ?? true) ? 'checked' : '' }}>
                            <span class="form-check-label">Enable autoplay</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="slider_navigation" value="1" class="form-check-input"
                                   {{ old('slider_navigation', $sliderSettings['navigation'] ?? true) ? 'checked' : '' }}>
                            <span class="form-check-label">Show navigation arrows</span>
                        </label>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-check">
                            <input type="checkbox" name="slider_pagination" value="1" class="form-check-input"
                                   {{ old('slider_pagination', $sliderSettings['pagination'] ?? true) ? 'checked' : '' }}>
                            <span class="form-check-label">Show pagination dots</span>
                        </label>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Slider Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Slide Modal -->
<div class="modal-overlay" id="addSlideModal">
    <div class="modal-container" style="max-width: 700px;">
        <div class="modal-header">
            <h3 class="modal-title" id="slideModalTitle">Add New Slide</h3>
            <button type="button" class="modal-close" onclick="closeModal('addSlideModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="slideForm" action="{{ route('admin.settings.hero-slides.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="slideMethod" value="POST">
            <input type="hidden" name="slide_id" id="slideId">

            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Slide Image <span class="text-danger">*</span></label>
                    <div class="slide-upload" id="slideUpload">
                        <div class="slide-upload-preview" id="slidePreview">
                            <div class="upload-placeholder">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Click or drag to upload image</span>
                                <small>Recommended: 1920x1080px, Max 5MB</small>
                            </div>
                        </div>
                        <input type="file" name="image" id="slideImageInput" accept="image/*" style="display: none;">
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" id="slideTitle" class="form-control"
                                   placeholder="Welcome to MK Hotel">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">Subtitle</label>
                            <input type="text" name="subtitle" id="slideSubtitle" class="form-control"
                                   placeholder="Experience luxury like never before">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">Button Text</label>
                            <input type="text" name="button_text" id="slideButtonText" class="form-control"
                                   placeholder="Book Now">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">Button URL</label>
                            <input type="text" name="button_url" id="slideButtonUrl" class="form-control"
                                   placeholder="/booking">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">Text Position</label>
                            <select name="text_position" id="slideTextPosition" class="form-control">
                                <option value="center">Center</option>
                                <option value="left">Left</option>
                                <option value="right">Right</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">Overlay Opacity</label>
                            <input type="range" name="overlay_opacity" id="slideOverlay" class="form-range"
                                   min="0" max="100" value="40">
                            <div class="d-flex justify-between">
                                <small>0%</small>
                                <small id="overlayValue">40%</small>
                                <small>100%</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-0">
                    <label class="form-check">
                        <input type="checkbox" name="is_active" id="slideActive" value="1" class="form-check-input" checked>
                        <span class="form-check-label">Active</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addSlideModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <span id="slideSubmitText">Add Slide</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.slides-list {
    min-height: 200px;
}

.slide-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--gray-100);
    transition: background 0.15s;
}

.slide-item:hover {
    background: var(--gray-50);
}

.slide-item:last-child {
    border-bottom: none;
}

.slide-drag-handle {
    cursor: grab;
    color: var(--gray-400);
    padding: 0.5rem;
}

.slide-drag-handle:active {
    cursor: grabbing;
}

.slide-image {
    width: 160px;
    height: 90px;
    border-radius: 0.375rem;
    overflow: hidden;
    flex-shrink: 0;
}

.slide-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.slide-placeholder {
    width: 100%;
    height: 100%;
    background: var(--gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-400);
    font-size: 1.5rem;
}

.slide-content {
    flex: 1;
}

.slide-content h4 {
    margin: 0 0 0.25rem;
    font-size: 1rem;
}

.slide-status {
    padding: 0 1rem;
}

.slide-actions {
    display: flex;
    gap: 0.5rem;
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--gray-300);
    transition: 0.2s;
    border-radius: 24px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.2s;
    border-radius: 50%;
}

.toggle-switch input:checked + .toggle-slider {
    background-color: var(--success);
}

.toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(20px);
}

.slide-upload {
    border: 2px dashed var(--gray-300);
    border-radius: 0.5rem;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.15s;
}

.slide-upload:hover {
    border-color: var(--primary);
}

.slide-upload-preview {
    aspect-ratio: 16/9;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gray-50);
}

.slide-upload-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.upload-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: var(--gray-400);
    gap: 0.5rem;
}

.upload-placeholder i {
    font-size: 2rem;
}

.form-range {
    width: 100%;
    height: 6px;
    background: var(--gray-200);
    border-radius: 3px;
    outline: none;
    -webkit-appearance: none;
}

.form-range::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 18px;
    height: 18px;
    background: var(--primary);
    border-radius: 50%;
    cursor: pointer;
}

.sortable-ghost {
    opacity: 0.4;
    background: var(--primary-light);
}
</style>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize drag-and-drop sorting
    const slidesList = document.getElementById('slidesList');
    if (slidesList) {
        new Sortable(slidesList, {
            animation: 150,
            handle: '.slide-drag-handle',
            ghostClass: 'sortable-ghost',
            onEnd: function(evt) {
                const order = Array.from(slidesList.querySelectorAll('.slide-item')).map(item => item.dataset.id);

                fetch('{{ route("admin.settings.hero-slides.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ order: order })
                });
            }
        });
    }

    // Toggle slide status
    document.querySelectorAll('.slide-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const slideId = this.dataset.id;

            fetch(`/admin/settings/hero-slides/${slideId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ is_active: this.checked })
            });
        });
    });

    // Slide image upload
    const slideUpload = document.getElementById('slideUpload');
    const slideImageInput = document.getElementById('slideImageInput');
    const slidePreview = document.getElementById('slidePreview');

    slideUpload.addEventListener('click', () => slideImageInput.click());

    slideImageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                slidePreview.innerHTML = `<img src="${e.target.result}" alt="">`;
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Overlay opacity slider
    const overlaySlider = document.getElementById('slideOverlay');
    const overlayValue = document.getElementById('overlayValue');

    overlaySlider.addEventListener('input', function() {
        overlayValue.textContent = this.value + '%';
    });
});

function openModal(id) {
    document.getElementById(id).classList.add('active');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
    resetSlideForm();
}

function resetSlideForm() {
    document.getElementById('slideForm').reset();
    document.getElementById('slideModalTitle').textContent = 'Add New Slide';
    document.getElementById('slideSubmitText').textContent = 'Add Slide';
    document.getElementById('slideMethod').value = 'POST';
    document.getElementById('slideId').value = '';
    document.getElementById('slidePreview').innerHTML = `
        <div class="upload-placeholder">
            <i class="fas fa-cloud-upload-alt"></i>
            <span>Click or drag to upload image</span>
            <small>Recommended: 1920x1080px, Max 5MB</small>
        </div>
    `;
}

function editSlide(id) {
    fetch(`/admin/settings/hero-slides/${id}`)
        .then(response => response.json())
        .then(slide => {
            document.getElementById('slideModalTitle').textContent = 'Edit Slide';
            document.getElementById('slideSubmitText').textContent = 'Update Slide';
            document.getElementById('slideMethod').value = 'PUT';
            document.getElementById('slideId').value = slide.id;
            document.getElementById('slideTitle').value = slide.title || '';
            document.getElementById('slideSubtitle').value = slide.subtitle || '';
            document.getElementById('slideButtonText').value = slide.button_text || '';
            document.getElementById('slideButtonUrl').value = slide.button_url || '';
            document.getElementById('slideTextPosition').value = slide.text_position || 'center';
            document.getElementById('slideOverlay').value = slide.overlay_opacity || 40;
            document.getElementById('overlayValue').textContent = (slide.overlay_opacity || 40) + '%';
            document.getElementById('slideActive').checked = slide.is_active;

            if (slide.image) {
                document.getElementById('slidePreview').innerHTML = `<img src="/storage/${slide.image}" alt="">`;
            }

            openModal('addSlideModal');
        });
}

function deleteSlide(id) {
    if (confirm('Are you sure you want to delete this slide?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/settings/hero-slides/${id}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
