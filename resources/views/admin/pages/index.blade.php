@extends('admin.layouts.app')

@section('title', 'Pages')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>Pages</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Pages</h1>
        <p class="page-subtitle">Manage website pages and content</p>
    </div>
    <div class="page-actions">
        @can('pages.create')
        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Page
        </a>
        @endcan
    </div>
</div>

<!-- Pages Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th width="40">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Template</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody id="sortable-pages">
                    @forelse($pages ?? [] as $page)
                    <tr data-id="{{ $page->id }}">
                        <td>
                            <input type="checkbox" class="form-check-input row-checkbox" value="{{ $page->id }}">
                        </td>
                        <td>
                            <div class="d-flex align-center gap-2">
                                <i class="fas fa-grip-vertical text-muted" style="cursor: grab;"></i>
                                <div>
                                    <strong>{{ $page->title }}</strong>
                                    @if($page->is_homepage)
                                        <span class="badge badge-primary" style="margin-left: 0.25rem;">Homepage</span>
                                    @endif
                                    @if($page->meta_description)
                                        <div class="text-muted" style="font-size: 0.75rem; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $page->meta_description }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <code style="font-size: 0.75rem; background: var(--gray-100); padding: 0.25rem 0.5rem; border-radius: 4px;">
                                /{{ $page->slug }}
                            </code>
                        </td>
                        <td>
                            <span class="badge badge-secondary">{{ ucwords(str_replace(['_', '-'], ' ', $page->template ?? 'default')) }}</span>
                        </td>
                        <td>
                            @if($page->is_active)
                                <span class="badge badge-success">Published</span>
                            @else
                                <span class="badge badge-secondary">Draft</span>
                            @endif
                        </td>
                        <td>
                            <span title="{{ $page->updated_at->format('M d, Y H:i') }}">
                                {{ $page->updated_at->diffForHumans() }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ url($page->slug) }}" target="_blank" class="btn btn-sm btn-secondary" title="View">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                @can('pages.edit')
                                <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-secondary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('pages.delete')
                                @if(!$page->is_homepage)
                                <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this page?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-file-alt fa-2x mb-2"></i>
                                <p>No pages found</p>
                                @can('pages.create')
                                <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create First Page
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(isset($pages) && $pages->hasPages())
    <div class="card-footer">
        {{ $pages->links() }}
    </div>
    @endif
</div>

<!-- Bulk Actions -->
@can('pages.delete')
<div id="bulkActions" class="card mt-3" style="display: none;">
    <div class="card-body d-flex align-center justify-between">
        <span><strong id="selectedCount">0</strong> pages selected</span>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-secondary" id="bulkPublish">
                <i class="fas fa-check"></i> Publish
            </button>
            <button type="button" class="btn btn-secondary" id="bulkUnpublish">
                <i class="fas fa-eye-slash"></i> Unpublish
            </button>
            <button type="button" class="btn btn-danger" id="bulkDelete">
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>
    </div>
</div>
@endcan

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sortable pages
    const sortable = new Sortable(document.getElementById('sortable-pages'), {
        animation: 150,
        handle: '.fa-grip-vertical',
        onEnd: function(evt) {
            const order = [];
            document.querySelectorAll('#sortable-pages tr').forEach((row, index) => {
                order.push({
                    id: row.dataset.id,
                    position: index
                });
            });

            fetch('{{ route("admin.pages.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: order })
            });
        }
    });

    // Select all checkbox
    const selectAll = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');

    function updateBulkActions() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        if (checked.length > 0) {
            bulkActions.style.display = 'block';
            selectedCount.textContent = checked.length;
        } else {
            bulkActions.style.display = 'none';
        }
    }

    selectAll.addEventListener('change', function() {
        rowCheckboxes.forEach(cb => cb.checked = this.checked);
        updateBulkActions();
    });

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkActions);
    });

    // Bulk actions
    document.getElementById('bulkDelete')?.addEventListener('click', function() {
        if (!confirm('Are you sure you want to delete the selected pages?')) return;

        const ids = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);

        fetch('{{ route("admin.pages.bulkDestroy") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids: ids })
        }).then(() => window.location.reload());
    });

    document.getElementById('bulkPublish')?.addEventListener('click', function() {
        const ids = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);

        fetch('{{ route("admin.pages.bulkUpdate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids: ids, is_active: true })
        }).then(() => window.location.reload());
    });

    document.getElementById('bulkUnpublish')?.addEventListener('click', function() {
        const ids = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);

        fetch('{{ route("admin.pages.bulkUpdate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids: ids, is_active: false })
        }).then(() => window.location.reload());
    });
});
</script>
@endpush
