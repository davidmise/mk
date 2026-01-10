@extends('admin.layouts.app')

@section('title', 'Gallery')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>Gallery</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Gallery</h1>
        <p class="page-subtitle">Manage hotel images and photo gallery</p>
    </div>
    <div class="page-actions">
        @can('gallery.create')
        <button type="button" class="btn btn-primary" data-modal="uploadModal">
            <i class="fas fa-upload"></i> Upload Images
        </button>
        @endcan
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex align-center justify-between">
            <div class="d-flex align-center gap-3">
                <select id="categoryFilter" class="form-control" style="min-width: 150px;">
                    <option value="">All Categories</option>
                    @foreach($categories ?? [] as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ ucwords(str_replace(['_', '-'], ' ', $category)) }}
                        </option>
                    @endforeach
                </select>

                <div class="btn-group">
                    <button type="button" class="btn btn-secondary active" data-view="grid" title="Grid View">
                        <i class="fas fa-th"></i>
                    </button>
                    <button type="button" class="btn btn-secondary" data-view="list" title="List View">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex align-center gap-2">
                <span class="text-muted">{{ $images->total() ?? 0 }} images</span>
            </div>
        </div>
    </div>
</div>

<!-- Gallery Grid View -->
<div id="gridView" class="gallery-grid">
    @forelse($images ?? [] as $image)
    <div class="gallery-item" data-id="{{ $image->id }}" data-category="{{ $image->category }}">
        <div class="gallery-image">
            <img src="{{ $image->thumbnail_url ?? $image->url }}" alt="{{ $image->alt_text ?? $image->title }}">
            <div class="gallery-overlay">
                <div class="gallery-actions">
                    <button type="button" class="btn btn-sm btn-light" data-preview="{{ $image->url }}" title="Preview">
                        <i class="fas fa-eye"></i>
                    </button>
                    @can('gallery.edit')
                    <button type="button" class="btn btn-sm btn-light" data-edit="{{ $image->id }}" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    @endcan
                    @can('gallery.delete')
                    <form action="{{ route('admin.gallery.destroy', $image) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Are you sure you want to delete this image?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
        <div class="gallery-info">
            <span class="gallery-title">{{ $image->title ?? 'Untitled' }}</span>
            <span class="gallery-category badge badge-secondary">{{ ucwords(str_replace(['_', '-'], ' ', $image->category ?? 'general')) }}</span>
        </div>
    </div>
    @empty
    <div class="gallery-empty">
        <i class="fas fa-images fa-3x text-muted mb-3"></i>
        <p class="text-muted">No images found</p>
        @can('gallery.create')
        <button type="button" class="btn btn-primary" data-modal="uploadModal">
            <i class="fas fa-upload"></i> Upload First Image
        </button>
        @endcan
    </div>
    @endforelse
</div>

<!-- Gallery List View -->
<div id="listView" class="card" style="display: none;">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th width="80">Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Dimensions</th>
                        <th>Size</th>
                        <th>Uploaded</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($images ?? [] as $image)
                    <tr>
                        <td>
                            <img src="{{ $image->thumbnail_url ?? $image->url }}" alt="{{ $image->alt_text }}"
                                 style="width: 60px; height: 45px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td>
                            <strong>{{ $image->title ?? 'Untitled' }}</strong>
                            @if($image->alt_text)
                                <div class="text-muted" style="font-size: 0.75rem;">Alt: {{ $image->alt_text }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-secondary">{{ ucwords(str_replace(['_', '-'], ' ', $image->category ?? 'general')) }}</span>
                        </td>
                        <td>{{ $image->width ?? '?' }} × {{ $image->height ?? '?' }}</td>
                        <td>{{ $image->formatted_size ?? 'Unknown' }}</td>
                        <td>{{ $image->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-secondary" data-preview="{{ $image->url }}" title="Preview">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @can('gallery.edit')
                                <button type="button" class="btn btn-sm btn-secondary" data-edit="{{ $image->id }}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @endcan
                                @can('gallery.delete')
                                <form action="{{ route('admin.gallery.destroy', $image) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this image?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-images fa-2x mb-2"></i>
                                <p>No images found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(isset($images) && $images->hasPages())
<div class="mt-4">
    {{ $images->withQueryString()->links() }}
</div>
@endif

<!-- Upload Modal -->
<div id="uploadModal" class="modal" style="display: none;">
    <div class="modal-dialog" style="max-width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Upload Images</h4>
                <button type="button" class="modal-close" data-modal-close>&times;</button>
            </div>
            <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Images <span class="text-danger">*</span></label>
                        <div class="upload-zone" id="uploadZone">
                            <input type="file" name="images[]" id="imageInput" multiple accept="image/*" style="display: none;">
                            <div class="upload-content">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                                <p class="mb-1">Drag & drop images here or click to browse</p>
                                <small class="text-muted">Supports: JPG, PNG, GIF, WebP (Max 5MB each)</small>
                            </div>
                        </div>
                        <div id="uploadPreview" class="upload-preview mt-3"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-control">
                            <option value="general">General</option>
                            <option value="rooms">Rooms</option>
                            <option value="amenities">Amenities</option>
                            <option value="dining">Dining</option>
                            <option value="spa">Spa & Wellness</option>
                            <option value="events">Events</option>
                            <option value="exterior">Exterior</option>
                            <option value="lobby">Lobby</option>
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-check">
                            <input type="checkbox" name="is_featured" value="1" class="form-check-input">
                            <span class="form-check-label">Mark as featured</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
                    <button type="submit" class="btn btn-primary" id="uploadBtn" disabled>
                        <i class="fas fa-upload"></i> Upload Images
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div id="previewModal" class="modal" style="display: none;">
    <div class="modal-dialog" style="max-width: 90vw; max-height: 90vh;">
        <div class="modal-content" style="background: transparent; box-shadow: none;">
            <button type="button" class="modal-close" data-modal-close style="position: absolute; top: -40px; right: 0; color: white; font-size: 2rem;">&times;</button>
            <img id="previewImage" src="" alt="Preview" style="max-width: 100%; max-height: 85vh; object-fit: contain;">
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal" style="display: none;">
    <div class="modal-dialog" style="max-width: 500px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Image</h4>
                <button type="button" class="modal-close" data-modal-close>&times;</button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <img id="editPreview" src="" alt="Preview" style="width: 100%; max-height: 200px; object-fit: contain; border-radius: 4px; background: var(--gray-100);">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" id="editTitle" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alt Text</label>
                        <input type="text" name="alt_text" id="editAltText" class="form-control"
                               placeholder="Describe the image for accessibility">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category" id="editCategory" class="form-control">
                            <option value="general">General</option>
                            <option value="rooms">Rooms</option>
                            <option value="amenities">Amenities</option>
                            <option value="dining">Dining</option>
                            <option value="spa">Spa & Wellness</option>
                            <option value="events">Events</option>
                            <option value="exterior">Exterior</option>
                            <option value="lobby">Lobby</option>
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-check">
                            <input type="checkbox" name="is_featured" id="editFeatured" value="1" class="form-check-input">
                            <span class="form-check-label">Featured Image</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}

.gallery-item {
    background: white;
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.2s;
}

.gallery-item:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.gallery-image {
    position: relative;
    aspect-ratio: 4/3;
    overflow: hidden;
}

.gallery-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s;
}

.gallery-item:hover .gallery-overlay {
    opacity: 1;
}

.gallery-actions {
    display: flex;
    gap: 0.5rem;
}

.gallery-info {
    padding: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}

.gallery-title {
    font-size: 0.875rem;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.gallery-category {
    font-size: 0.625rem;
    flex-shrink: 0;
}

.gallery-empty {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 0.5rem;
}

.upload-zone {
    border: 2px dashed var(--gray-300);
    border-radius: 0.5rem;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
}

.upload-zone:hover,
.upload-zone.dragover {
    border-color: var(--primary);
    background: #f8fafc;
}

.upload-preview {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.5rem;
}

.upload-preview-item {
    position: relative;
    aspect-ratio: 1;
    border-radius: 4px;
    overflow: hidden;
}

.upload-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.upload-preview-item .remove {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 20px;
    height: 20px;
    background: rgba(0,0,0,0.5);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    font-size: 0.75rem;
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
    // View toggle
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const viewButtons = document.querySelectorAll('[data-view]');

    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            viewButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            if (this.dataset.view === 'grid') {
                gridView.style.display = '';
                listView.style.display = 'none';
            } else {
                gridView.style.display = 'none';
                listView.style.display = '';
            }
        });
    });

    // Category filter
    document.getElementById('categoryFilter').addEventListener('change', function() {
        const url = new URL(window.location);
        if (this.value) {
            url.searchParams.set('category', this.value);
        } else {
            url.searchParams.delete('category');
        }
        window.location = url;
    });

    // Modal handlers
    document.querySelectorAll('[data-modal]').forEach(trigger => {
        trigger.addEventListener('click', function() {
            const modalId = this.dataset.modal;
            const modal = document.getElementById(modalId);
            if (modal) modal.style.display = 'flex';
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach(closer => {
        closer.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) modal.style.display = 'none';
        });
    });

    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) this.style.display = 'none';
        });
    });

    // Upload zone
    const uploadZone = document.getElementById('uploadZone');
    const imageInput = document.getElementById('imageInput');
    const uploadPreview = document.getElementById('uploadPreview');
    const uploadBtn = document.getElementById('uploadBtn');

    uploadZone.addEventListener('click', () => imageInput.click());

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
        imageInput.files = e.dataTransfer.files;
        handleFiles(imageInput.files);
    });

    imageInput.addEventListener('change', () => handleFiles(imageInput.files));

    function handleFiles(files) {
        uploadPreview.innerHTML = '';
        uploadBtn.disabled = files.length === 0;

        Array.from(files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'upload-preview-item';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="remove" data-index="${index}">&times;</button>
                `;
                uploadPreview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }

    // Preview modal
    document.querySelectorAll('[data-preview]').forEach(btn => {
        btn.addEventListener('click', function() {
            const previewModal = document.getElementById('previewModal');
            document.getElementById('previewImage').src = this.dataset.preview;
            previewModal.style.display = 'flex';
        });
    });

    // Edit modal
    document.querySelectorAll('[data-edit]').forEach(btn => {
        btn.addEventListener('click', function() {
            const imageId = this.dataset.edit;
            // Fetch image data and populate form
            fetch(`/admin/gallery/${imageId}/edit`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('editForm').action = `/admin/gallery/${imageId}`;
                    document.getElementById('editPreview').src = data.url;
                    document.getElementById('editTitle').value = data.title || '';
                    document.getElementById('editAltText').value = data.alt_text || '';
                    document.getElementById('editCategory').value = data.category || 'general';
                    document.getElementById('editFeatured').checked = data.is_featured;
                    document.getElementById('editModal').style.display = 'flex';
                });
        });
    });
});
</script>
@endpush
