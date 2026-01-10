@extends('admin.layouts.app')

@section('title', 'News & Blog')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>News & Blog</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">News & Blog</h1>
        <p class="page-subtitle">Manage articles, announcements, and blog posts</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Article
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: #e3f2fd; color: #1976d2;">
                <i class="fas fa-newspaper"></i>
            </div>
            <div class="stats-info">
                <h3>{{ $stats['total'] ?? 0 }}</h3>
                <p>Total Articles</p>
            </div>
        </div>
    </div>
    <div class="col-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: #e8f5e9; color: #388e3c;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-info">
                <h3>{{ $stats['published'] ?? 0 }}</h3>
                <p>Published</p>
            </div>
        </div>
    </div>
    <div class="col-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: #fff3e0; color: #f57c00;">
                <i class="fas fa-edit"></i>
            </div>
            <div class="stats-info">
                <h3>{{ $stats['draft'] ?? 0 }}</h3>
                <p>Drafts</p>
            </div>
        </div>
    </div>
    <div class="col-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: #f3e5f5; color: #7b1fa2;">
                <i class="fas fa-eye"></i>
            </div>
            <div class="stats-info">
                <h3>{{ number_format($stats['total_views'] ?? 0) }}</h3>
                <p>Total Views</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.news.index') }}" method="GET" class="filters-form">
            <div class="row align-items-end gap-3">
                <div class="col-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search articles..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-2">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="">All Categories</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    </select>
                </div>
                <div class="col-2">
                    <label class="form-label">Author</label>
                    <select name="author" class="form-control">
                        <option value="">All Authors</option>
                        @foreach($authors ?? [] as $author)
                            <option value="{{ $author->id }}" {{ request('author') == $author->id ? 'selected' : '' }}>
                                {{ $author->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Articles List -->
<div class="card">
    <div class="card-header d-flex justify-between align-items-center">
        <h3 class="card-title mb-0">Articles</h3>
        <div class="bulk-actions d-none" id="bulkActions">
            <span class="selected-count">0 selected</span>
            <button type="button" class="btn btn-secondary btn-sm" data-action="publish">
                <i class="fas fa-check"></i> Publish
            </button>
            <button type="button" class="btn btn-secondary btn-sm" data-action="draft">
                <i class="fas fa-edit"></i> Draft
            </button>
            <button type="button" class="btn btn-danger btn-sm" data-action="delete">
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40px;">
                        <input type="checkbox" id="selectAll" class="form-check-input">
                    </th>
                    <th>Article</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Views</th>
                    <th>Date</th>
                    <th style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($articles ?? [] as $article)
                <tr>
                    <td>
                        <input type="checkbox" class="form-check-input row-select" value="{{ $article->id }}">
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            @if($article->featured_image)
                            <img src="{{ asset('storage/' . $article->featured_image) }}"
                                 alt="{{ $article->title }}"
                                 style="width: 80px; height: 60px; object-fit: cover; border-radius: 0.375rem;">
                            @else
                            <div style="width: 80px; height: 60px; background: var(--gray-100); border-radius: 0.375rem; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-image" style="color: var(--gray-400);"></i>
                            </div>
                            @endif
                            <div>
                                <strong>{{ $article->title }}</strong>
                                @if($article->is_featured)
                                    <span class="badge badge-warning" style="font-size: 0.625rem;">Featured</span>
                                @endif
                                <p class="text-muted mb-0" style="font-size: 0.75rem;">
                                    {{ Str::limit($article->excerpt, 80) }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($article->category)
                            <span class="badge badge-secondary">{{ $article->category->name }}</span>
                        @else
                            <span class="text-muted">Uncategorized</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="user-avatar" style="width: 28px; height: 28px; font-size: 0.625rem;">
                                {{ strtoupper(substr($article->author->name ?? 'U', 0, 1)) }}
                            </div>
                            <span>{{ $article->author->name ?? 'Unknown' }}</span>
                        </div>
                    </td>
                    <td>
                        @if($article->status == 'published')
                            <span class="badge badge-success">Published</span>
                        @elseif($article->status == 'draft')
                            <span class="badge badge-warning">Draft</span>
                        @elseif($article->status == 'scheduled')
                            <span class="badge badge-info">Scheduled</span>
                        @endif
                    </td>
                    <td>{{ number_format($article->views ?? 0) }}</td>
                    <td>
                        @if($article->published_at)
                            {{ $article->published_at->format('M d, Y') }}
                        @else
                            <span class="text-muted">Not published</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group">
                            <a href="{{ route('admin.news.edit', $article) }}" class="btn btn-sm btn-secondary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('news.show', $article->slug) }}" class="btn btn-sm btn-secondary" target="_blank" title="View">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger" title="Delete"
                                    onclick="confirmDelete({{ $article->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="empty-state">
                            <i class="fas fa-newspaper" style="font-size: 2rem; color: var(--gray-300);"></i>
                            <p class="text-muted mt-2">No articles found</p>
                            <a href="{{ route('admin.news.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Create First Article
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(isset($articles) && $articles->hasPages())
    <div class="card-footer">
        {{ $articles->withQueryString()->links() }}
    </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-container" style="max-width: 400px;">
        <div class="modal-header">
            <h3 class="modal-title">Delete Article</h3>
            <button type="button" class="modal-close" onclick="closeModal('deleteModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body text-center">
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: var(--danger); margin-bottom: 1rem;"></i>
            <p>Are you sure you want to delete this article? This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
            <form id="deleteForm" action="" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete Article</button>
            </form>
        </div>
    </div>
</div>

<style>
.stats-card {
    background: white;
    border-radius: 0.5rem;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.stats-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.stats-info h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
}

.stats-info p {
    margin: 0;
    color: var(--gray-500);
    font-size: 0.875rem;
}

.bulk-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.selected-count {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin-right: 0.5rem;
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.75rem;
}
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-select');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = bulkActions.querySelector('.selected-count');

    // Select all
    selectAll.addEventListener('change', function() {
        rowCheckboxes.forEach(cb => cb.checked = this.checked);
        updateBulkActions();
    });

    // Individual selection
    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkActions);
    });

    function updateBulkActions() {
        const checked = document.querySelectorAll('.row-select:checked');
        if (checked.length > 0) {
            bulkActions.classList.remove('d-none');
            selectedCount.textContent = checked.length + ' selected';
        } else {
            bulkActions.classList.add('d-none');
        }

        // Update select all checkbox
        selectAll.checked = checked.length === rowCheckboxes.length;
        selectAll.indeterminate = checked.length > 0 && checked.length < rowCheckboxes.length;
    }

    // Bulk action buttons
    bulkActions.querySelectorAll('[data-action]').forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.dataset.action;
            const ids = Array.from(document.querySelectorAll('.row-select:checked')).map(cb => cb.value);

            if (action === 'delete') {
                if (!confirm('Are you sure you want to delete ' + ids.length + ' articles?')) return;
            }

            // Submit bulk action
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.news.bulk") }}';
            form.innerHTML = `
                @csrf
                <input type="hidden" name="action" value="${action}">
                ${ids.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('')}
            `;
            document.body.appendChild(form);
            form.submit();
        });
    });
});

function confirmDelete(id) {
    document.getElementById('deleteForm').action = '/admin/news/' + id;
    document.getElementById('deleteModal').classList.add('active');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}
</script>
@endpush
