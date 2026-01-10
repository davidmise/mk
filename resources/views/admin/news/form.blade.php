@extends('admin.layouts.app')

@section('title', isset($article) ? 'Edit Article' : 'New Article')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <a href="{{ route('admin.news.index') }}">News & Blog</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>{{ isset($article) ? 'Edit Article' : 'New Article' }}</span>
@endsection

@section('content')
<form action="{{ isset($article) ? route('admin.news.update', $article) : route('admin.news.store') }}"
      method="POST" enctype="multipart/form-data" id="articleForm">
    @csrf
    @if(isset($article))
        @method('PUT')
    @endif

    <div class="page-header">
        <div>
            <h1 class="page-title">{{ isset($article) ? 'Edit Article' : 'New Article' }}</h1>
            <p class="page-subtitle">{{ isset($article) ? 'Update article content and settings' : 'Create a new blog post or announcement' }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" name="action" value="draft" class="btn btn-secondary">
                <i class="fas fa-save"></i> Save Draft
            </button>
            <button type="submit" name="action" value="publish" class="btn btn-primary">
                <i class="fas fa-check"></i> {{ isset($article) && $article->status == 'published' ? 'Update' : 'Publish' }}
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-8">
            <!-- Title & Content -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror"
                               value="{{ old('title', $article->title ?? '') }}" required
                               placeholder="Enter article title...">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Slug</label>
                        <div class="input-group">
                            <span class="input-group-text">/news/</span>
                            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                                   value="{{ old('slug', $article->slug ?? '') }}"
                                   placeholder="auto-generated-from-title">
                        </div>
                        <small class="text-muted">Leave blank to auto-generate from title</small>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Excerpt</label>
                        <textarea name="excerpt" class="form-control @error('excerpt') is-invalid @enderror" rows="2"
                                  placeholder="Brief summary for listings and SEO...">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
                        @error('excerpt')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Content <span class="text-danger">*</span></label>
                        <div class="editor-toolbar">
                            <button type="button" data-format="bold" title="Bold"><i class="fas fa-bold"></i></button>
                            <button type="button" data-format="italic" title="Italic"><i class="fas fa-italic"></i></button>
                            <button type="button" data-format="underline" title="Underline"><i class="fas fa-underline"></i></button>
                            <span class="separator"></span>
                            <button type="button" data-format="h2" title="Heading 2">H2</button>
                            <button type="button" data-format="h3" title="Heading 3">H3</button>
                            <span class="separator"></span>
                            <button type="button" data-format="ul" title="Bullet List"><i class="fas fa-list-ul"></i></button>
                            <button type="button" data-format="ol" title="Numbered List"><i class="fas fa-list-ol"></i></button>
                            <button type="button" data-format="quote" title="Quote"><i class="fas fa-quote-right"></i></button>
                            <span class="separator"></span>
                            <button type="button" data-format="link" title="Insert Link"><i class="fas fa-link"></i></button>
                            <button type="button" data-format="image" title="Insert Image"><i class="fas fa-image"></i></button>
                            <span class="separator"></span>
                            <button type="button" data-format="code" title="Code"><i class="fas fa-code"></i></button>
                        </div>
                        <textarea name="content" id="contentEditor" class="form-control @error('content') is-invalid @enderror"
                                  rows="15" required
                                  placeholder="Write your article content here...">{{ old('content', $article->content ?? '') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- SEO Settings -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">SEO Settings</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control @error('meta_title') is-invalid @enderror"
                               value="{{ old('meta_title', $article->meta_title ?? '') }}"
                               placeholder="SEO title (defaults to article title)">
                        <small class="text-muted">Recommended: 50-60 characters</small>
                        @error('meta_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror" rows="2"
                                  placeholder="SEO description (defaults to excerpt)">{{ old('meta_description', $article->meta_description ?? '') }}</textarea>
                        <small class="text-muted">Recommended: 150-160 characters</small>
                        @error('meta_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control @error('meta_keywords') is-invalid @enderror"
                               value="{{ old('meta_keywords', $article->meta_keywords ?? '') }}"
                               placeholder="keyword1, keyword2, keyword3">
                        @error('meta_keywords')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-4">
            <!-- Publishing -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Publishing</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror">
                            <option value="draft" {{ old('status', $article->status ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $article->status ?? '') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="scheduled" {{ old('status', $article->status ?? '') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group" id="scheduledDateGroup" style="display: none;">
                        <label class="form-label">Publish Date</label>
                        <input type="datetime-local" name="published_at" class="form-control @error('published_at') is-invalid @enderror"
                               value="{{ old('published_at', isset($article) && $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '') }}">
                        @error('published_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="is_featured" value="1" class="form-check-input"
                                   {{ old('is_featured', $article->is_featured ?? false) ? 'checked' : '' }}>
                            <span class="form-check-label">Featured Article</span>
                        </label>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-check">
                            <input type="checkbox" name="allow_comments" value="1" class="form-check-input"
                                   {{ old('allow_comments', $article->allow_comments ?? true) ? 'checked' : '' }}>
                            <span class="form-check-label">Allow Comments</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Category & Tags -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Category & Tags</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-control @error('category_id') is-invalid @enderror">
                            <option value="">Uncategorized</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $article->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Tags</label>
                        <input type="text" name="tags" class="form-control @error('tags') is-invalid @enderror"
                               value="{{ old('tags', isset($article) ? $article->tags->pluck('name')->implode(', ') : '') }}"
                               placeholder="hotel, travel, vacation">
                        <small class="text-muted">Separate tags with commas</small>
                        @error('tags')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Featured Image</h3>
                </div>
                <div class="card-body">
                    <div class="featured-image-preview" id="featuredImagePreview">
                        @if(isset($article) && $article->featured_image)
                            <img src="{{ asset('storage/' . $article->featured_image) }}" alt="">
                            <button type="button" class="remove-image" id="removeFeaturedImage">
                                <i class="fas fa-times"></i>
                            </button>
                        @else
                            <div class="upload-placeholder">
                                <i class="fas fa-image"></i>
                                <span>Click to upload image</span>
                            </div>
                        @endif
                    </div>
                    <input type="file" name="featured_image" id="featuredImageInput" accept="image/*" style="display: none;">
                    <input type="hidden" name="remove_featured_image" id="removeFeaturedImageInput" value="0">

                    <div class="form-group mt-3 mb-0">
                        <label class="form-label">Image Caption</label>
                        <input type="text" name="featured_image_caption" class="form-control"
                               value="{{ old('featured_image_caption', $article->featured_image_caption ?? '') }}"
                               placeholder="Photo credit or description">
                    </div>
                </div>
            </div>

            <!-- Author -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Author</h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-0">
                        <label class="form-label">Author</label>
                        <select name="author_id" class="form-control @error('author_id') is-invalid @enderror">
                            @foreach($authors ?? [auth()->user()] as $author)
                                <option value="{{ $author->id }}" {{ old('author_id', $article->author_id ?? auth()->id()) == $author->id ? 'selected' : '' }}>
                                    {{ $author->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('author_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if(isset($article))
                    <div class="mt-3 pt-3" style="border-top: 1px solid var(--gray-100);">
                        <div class="d-flex justify-between mb-2">
                            <span class="text-muted">Created</span>
                            <span>{{ $article->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="d-flex justify-between mb-2">
                            <span class="text-muted">Updated</span>
                            <span>{{ $article->updated_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="d-flex justify-between">
                            <span class="text-muted">Views</span>
                            <span>{{ number_format($article->views ?? 0) }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.form-control-lg {
    font-size: 1.25rem;
    padding: 0.75rem 1rem;
}

.editor-toolbar {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem;
    border: 1px solid var(--gray-200);
    border-bottom: none;
    border-radius: 0.375rem 0.375rem 0 0;
    background: var(--gray-50);
}

.editor-toolbar button {
    width: 2rem;
    height: 2rem;
    border: none;
    background: transparent;
    border-radius: 0.25rem;
    cursor: pointer;
    color: var(--gray-600);
    font-size: 0.875rem;
}

.editor-toolbar button:hover {
    background: var(--gray-200);
    color: var(--gray-900);
}

.editor-toolbar .separator {
    width: 1px;
    height: 1.5rem;
    background: var(--gray-200);
    margin: 0 0.25rem;
}

#contentEditor {
    border-radius: 0 0 0.375rem 0.375rem;
    font-family: inherit;
    line-height: 1.6;
}

.featured-image-preview {
    position: relative;
    aspect-ratio: 16/9;
    border: 2px dashed var(--gray-300);
    border-radius: 0.5rem;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.15s;
}

.featured-image-preview:hover {
    border-color: var(--primary);
}

.featured-image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.featured-image-preview .upload-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: var(--gray-400);
}

.featured-image-preview .upload-placeholder i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.featured-image-preview .remove-image {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    width: 2rem;
    height: 2rem;
    border: none;
    border-radius: 50%;
    background: rgba(0,0,0,0.5);
    color: white;
    cursor: pointer;
}

.featured-image-preview .remove-image:hover {
    background: var(--danger);
}
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.querySelector('select[name="status"]');
    const scheduledGroup = document.getElementById('scheduledDateGroup');
    const featuredImagePreview = document.getElementById('featuredImagePreview');
    const featuredImageInput = document.getElementById('featuredImageInput');
    const removeFeaturedImage = document.getElementById('removeFeaturedImage');
    const removeFeaturedImageInput = document.getElementById('removeFeaturedImageInput');

    // Show/hide scheduled date field
    statusSelect.addEventListener('change', function() {
        scheduledGroup.style.display = this.value === 'scheduled' ? 'block' : 'none';
    });

    // Trigger on page load
    if (statusSelect.value === 'scheduled') {
        scheduledGroup.style.display = 'block';
    }

    // Featured image upload
    featuredImagePreview.addEventListener('click', function(e) {
        if (!e.target.closest('.remove-image')) {
            featuredImageInput.click();
        }
    });

    featuredImageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                featuredImagePreview.innerHTML = `
                    <img src="${e.target.result}" alt="">
                    <button type="button" class="remove-image" id="removeFeaturedImage">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                bindRemoveHandler();
            };
            reader.readAsDataURL(this.files[0]);
            removeFeaturedImageInput.value = '0';
        }
    });

    function bindRemoveHandler() {
        const btn = document.getElementById('removeFeaturedImage');
        if (btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                featuredImagePreview.innerHTML = `
                    <div class="upload-placeholder">
                        <i class="fas fa-image"></i>
                        <span>Click to upload image</span>
                    </div>
                `;
                featuredImageInput.value = '';
                removeFeaturedImageInput.value = '1';
            });
        }
    }

    bindRemoveHandler();

    // Auto-generate slug from title
    const titleInput = document.querySelector('input[name="title"]');
    const slugInput = document.querySelector('input[name="slug"]');
    let slugManuallyEdited = slugInput.value !== '';

    titleInput.addEventListener('input', function() {
        if (!slugManuallyEdited) {
            slugInput.value = this.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }
    });

    slugInput.addEventListener('input', function() {
        slugManuallyEdited = this.value !== '';
    });

    // Simple editor toolbar
    document.querySelectorAll('.editor-toolbar button').forEach(btn => {
        btn.addEventListener('click', function() {
            const format = this.dataset.format;
            const textarea = document.getElementById('contentEditor');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            const selected = text.substring(start, end);

            let replacement = '';

            switch(format) {
                case 'bold':
                    replacement = `**${selected || 'bold text'}**`;
                    break;
                case 'italic':
                    replacement = `*${selected || 'italic text'}*`;
                    break;
                case 'underline':
                    replacement = `<u>${selected || 'underlined text'}</u>`;
                    break;
                case 'h2':
                    replacement = `\n## ${selected || 'Heading 2'}\n`;
                    break;
                case 'h3':
                    replacement = `\n### ${selected || 'Heading 3'}\n`;
                    break;
                case 'ul':
                    replacement = `\n- ${selected || 'List item'}\n`;
                    break;
                case 'ol':
                    replacement = `\n1. ${selected || 'List item'}\n`;
                    break;
                case 'quote':
                    replacement = `\n> ${selected || 'Quote text'}\n`;
                    break;
                case 'link':
                    const url = prompt('Enter URL:', 'https://');
                    if (url) {
                        replacement = `[${selected || 'link text'}](${url})`;
                    } else {
                        return;
                    }
                    break;
                case 'image':
                    const imgUrl = prompt('Enter image URL:', 'https://');
                    if (imgUrl) {
                        replacement = `![${selected || 'alt text'}](${imgUrl})`;
                    } else {
                        return;
                    }
                    break;
                case 'code':
                    replacement = '`' + (selected || 'code') + '`';
                    break;
            }

            textarea.value = text.substring(0, start) + replacement + text.substring(end);
            textarea.focus();
            textarea.setSelectionRange(start + replacement.length, start + replacement.length);
        });
    });
});
</script>
@endpush
