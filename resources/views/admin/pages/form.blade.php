@extends('admin.layouts.app')

@section('title', isset($page) ? 'Edit Page' : 'Create Page')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <a href="{{ route('admin.pages.index') }}">Pages</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>{{ isset($page) ? 'Edit Page' : 'Create Page' }}</span>
@endsection

@section('content')
<form action="{{ isset($page) ? route('admin.pages.update', $page) : route('admin.pages.store') }}" method="POST">
    @csrf
    @if(isset($page))
        @method('PUT')
    @endif

    <div class="page-header">
        <div>
            <h1 class="page-title">{{ isset($page) ? 'Edit Page' : 'Create Page' }}</h1>
            <p class="page-subtitle">{{ isset($page) ? 'Update page content and settings' : 'Create a new website page' }}</p>
        </div>
        <div class="page-actions">
            @if(isset($page))
            <a href="{{ url($page->slug) }}" target="_blank" class="btn btn-secondary">
                <i class="fas fa-external-link-alt"></i> View Page
            </a>
            @endif
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ isset($page) ? 'Update Page' : 'Create Page' }}
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-8">
            <!-- Main Content -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Page Content</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Page Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="pageTitle" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $page->title ?? '') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">URL Slug <span class="text-danger">*</span></label>
                        <div class="d-flex align-center gap-2">
                            <span class="text-muted">{{ url('/') }}/</span>
                            <input type="text" name="slug" id="pageSlug" class="form-control @error('slug') is-invalid @enderror"
                                   value="{{ old('slug', $page->slug ?? '') }}" required style="flex: 1;">
                        </div>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Content</label>
                        <textarea name="content" id="pageContent" class="form-control @error('content') is-invalid @enderror"
                                  rows="20" style="font-family: monospace;">{{ old('content', $page->content ?? '') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">You can use HTML and Blade syntax for dynamic content</small>
                    </div>
                </div>
            </div>

            <!-- Sections -->
            <div class="card mb-4">
                <div class="card-header d-flex align-center justify-between">
                    <h3 class="card-title">Page Sections</h3>
                    <button type="button" class="btn btn-sm btn-primary" id="addSection">
                        <i class="fas fa-plus"></i> Add Section
                    </button>
                </div>
                <div class="card-body">
                    <div id="sections-container">
                        @foreach(old('sections', $page->sections ?? []) as $index => $section)
                        <div class="section-item card mb-3" data-index="{{ $index }}">
                            <div class="card-header d-flex align-center justify-between" style="padding: 0.75rem;">
                                <div class="d-flex align-center gap-2">
                                    <i class="fas fa-grip-vertical text-muted" style="cursor: grab;"></i>
                                    <span class="section-title">{{ $section['title'] ?? 'Section ' . ($index + 1) }}</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-danger remove-section">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="card-body" style="padding: 1rem;">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label">Section Title</label>
                                            <input type="text" name="sections[{{ $index }}][title]" class="form-control section-title-input"
                                                   value="{{ $section['title'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label">Section Type</label>
                                            <select name="sections[{{ $index }}][type]" class="form-control">
                                                <option value="content" {{ ($section['type'] ?? '') == 'content' ? 'selected' : '' }}>Content</option>
                                                <option value="hero" {{ ($section['type'] ?? '') == 'hero' ? 'selected' : '' }}>Hero</option>
                                                <option value="gallery" {{ ($section['type'] ?? '') == 'gallery' ? 'selected' : '' }}>Gallery</option>
                                                <option value="testimonials" {{ ($section['type'] ?? '') == 'testimonials' ? 'selected' : '' }}>Testimonials</option>
                                                <option value="cta" {{ ($section['type'] ?? '') == 'cta' ? 'selected' : '' }}>Call to Action</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="form-label">Section Content</label>
                                    <textarea name="sections[{{ $index }}][content]" class="form-control" rows="4">{{ $section['content'] ?? '' }}</textarea>
                                </div>
                                <input type="hidden" name="sections[{{ $index }}][order]" value="{{ $section['order'] ?? $index }}">
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <p class="text-muted mb-0" id="no-sections" style="{{ count(old('sections', $page->sections ?? [])) > 0 ? 'display: none;' : '' }}">
                        <i class="fas fa-info-circle"></i> No sections added. Click "Add Section" to create modular content blocks.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-4">
            <!-- Publish Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Publish Settings</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-control">
                            <option value="1" {{ old('is_active', $page->is_active ?? true) ? 'selected' : '' }}>Published</option>
                            <option value="0" {{ !old('is_active', $page->is_active ?? true) ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Page Template</label>
                        <select name="template" class="form-control">
                            <option value="default" {{ old('template', $page->template ?? 'default') == 'default' ? 'selected' : '' }}>Default</option>
                            <option value="full_width" {{ old('template', $page->template ?? '') == 'full_width' ? 'selected' : '' }}>Full Width</option>
                            <option value="sidebar" {{ old('template', $page->template ?? '') == 'sidebar' ? 'selected' : '' }}>With Sidebar</option>
                            <option value="landing" {{ old('template', $page->template ?? '') == 'landing' ? 'selected' : '' }}>Landing Page</option>
                            <option value="contact" {{ old('template', $page->template ?? '') == 'contact' ? 'selected' : '' }}>Contact Page</option>
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-check">
                            <input type="checkbox" name="is_homepage" value="1" class="form-check-input"
                                   {{ old('is_homepage', $page->is_homepage ?? false) ? 'checked' : '' }}>
                            <span class="form-check-label">Set as Homepage</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- SEO Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">SEO Settings</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control"
                               value="{{ old('meta_title', $page->meta_title ?? '') }}"
                               placeholder="Leave blank to use page title">
                        <small class="text-muted"><span id="metaTitleCount">0</span>/60 characters</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="3"
                                  placeholder="Brief description for search engines">{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
                        <small class="text-muted"><span id="metaDescCount">0</span>/160 characters</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control"
                               value="{{ old('meta_keywords', $page->meta_keywords ?? '') }}"
                               placeholder="keyword1, keyword2, keyword3">
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-check">
                            <input type="checkbox" name="noindex" value="1" class="form-check-input"
                                   {{ old('noindex', $page->noindex ?? false) ? 'checked' : '' }}>
                            <span class="form-check-label">Hide from search engines (noindex)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Featured Image</h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-0">
                        <div id="featuredImagePreview" class="mb-2" style="{{ isset($page->featured_image) ? '' : 'display: none;' }}">
                            <img src="{{ $page->featured_image ?? '' }}" alt="Featured Image" style="max-width: 100%; border-radius: 4px;">
                        </div>
                        <input type="text" name="featured_image" id="featuredImage" class="form-control"
                               value="{{ old('featured_image', $page->featured_image ?? '') }}"
                               placeholder="Image URL or path">
                        <small class="text-muted">Enter image URL or use media library</small>
                    </div>
                </div>
            </div>

            <!-- Advanced -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Advanced</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Custom CSS</label>
                        <textarea name="custom_css" class="form-control" rows="3" style="font-family: monospace; font-size: 0.75rem;"
                                  placeholder=".custom-class { ... }">{{ old('custom_css', $page->custom_css ?? '') }}</textarea>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Custom JavaScript</label>
                        <textarea name="custom_js" class="form-control" rows="3" style="font-family: monospace; font-size: 0.75rem;"
                                  placeholder="// Custom JS code">{{ old('custom_js', $page->custom_js ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Section Template -->
<template id="section-template">
    <div class="section-item card mb-3" data-index="__INDEX__">
        <div class="card-header d-flex align-center justify-between" style="padding: 0.75rem;">
            <div class="d-flex align-center gap-2">
                <i class="fas fa-grip-vertical text-muted" style="cursor: grab;"></i>
                <span class="section-title">New Section</span>
            </div>
            <button type="button" class="btn btn-sm btn-danger remove-section">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="card-body" style="padding: 1rem;">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label class="form-label">Section Title</label>
                        <input type="text" name="sections[__INDEX__][title]" class="form-control section-title-input">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label class="form-label">Section Type</label>
                        <select name="sections[__INDEX__][type]" class="form-control">
                            <option value="content">Content</option>
                            <option value="hero">Hero</option>
                            <option value="gallery">Gallery</option>
                            <option value="testimonials">Testimonials</option>
                            <option value="cta">Call to Action</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group mb-0">
                <label class="form-label">Section Content</label>
                <textarea name="sections[__INDEX__][content]" class="form-control" rows="4"></textarea>
            </div>
            <input type="hidden" name="sections[__INDEX__][order]" value="__INDEX__">
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate slug from title
    const titleInput = document.getElementById('pageTitle');
    const slugInput = document.getElementById('pageSlug');
    let slugManuallyEdited = {{ isset($page) ? 'true' : 'false' }};

    titleInput.addEventListener('input', function() {
        if (!slugManuallyEdited) {
            slugInput.value = this.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
        }
    });

    slugInput.addEventListener('input', function() {
        slugManuallyEdited = true;
    });

    // Meta character counts
    const metaTitleInput = document.querySelector('input[name="meta_title"]');
    const metaDescInput = document.querySelector('textarea[name="meta_description"]');
    const metaTitleCount = document.getElementById('metaTitleCount');
    const metaDescCount = document.getElementById('metaDescCount');

    function updateCounts() {
        metaTitleCount.textContent = metaTitleInput.value.length;
        metaDescCount.textContent = metaDescInput.value.length;
    }

    metaTitleInput.addEventListener('input', updateCounts);
    metaDescInput.addEventListener('input', updateCounts);
    updateCounts();

    // Sections management
    const sectionsContainer = document.getElementById('sections-container');
    const sectionTemplate = document.getElementById('section-template');
    const noSections = document.getElementById('no-sections');
    let sectionIndex = sectionsContainer.querySelectorAll('.section-item').length;

    // Make sections sortable
    new Sortable(sectionsContainer, {
        animation: 150,
        handle: '.fa-grip-vertical',
        onEnd: function() {
            updateSectionOrders();
        }
    });

    function updateSectionOrders() {
        sectionsContainer.querySelectorAll('.section-item').forEach((item, index) => {
            item.querySelector('input[name$="[order]"]').value = index;
        });
    }

    // Add section
    document.getElementById('addSection').addEventListener('click', function() {
        const html = sectionTemplate.innerHTML.replace(/__INDEX__/g, sectionIndex);
        sectionsContainer.insertAdjacentHTML('beforeend', html);
        sectionIndex++;
        noSections.style.display = 'none';
    });

    // Remove section
    sectionsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-section')) {
            e.target.closest('.section-item').remove();
            if (sectionsContainer.querySelectorAll('.section-item').length === 0) {
                noSections.style.display = '';
            }
            updateSectionOrders();
        }
    });

    // Update section title in header
    sectionsContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('section-title-input')) {
            const sectionItem = e.target.closest('.section-item');
            const titleSpan = sectionItem.querySelector('.section-title');
            titleSpan.textContent = e.target.value || 'Untitled Section';
        }
    });

    // Featured image preview
    const featuredImageInput = document.getElementById('featuredImage');
    const featuredImagePreview = document.getElementById('featuredImagePreview');

    featuredImageInput.addEventListener('input', function() {
        if (this.value) {
            featuredImagePreview.querySelector('img').src = this.value;
            featuredImagePreview.style.display = '';
        } else {
            featuredImagePreview.style.display = 'none';
        }
    });
});
</script>
@endpush
