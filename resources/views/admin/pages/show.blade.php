@extends('admin.layouts.app')

@section('title', $page->title)

@section('breadcrumb')
    <a href="{{ route('admin.pages.index') }}">Pages</a>
    <i class="fas fa-chevron-right"></i>
    <span>{{ Str::limit($page->title, 30) }}</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $page->title }}</h1>
        <p class="page-subtitle">
            /{{ $page->slug }} &bull; Last updated {{ $page->updated_at->diffForHumans() }}
        </p>
    </div>
    <div class="d-flex gap-2">
        @can('pages.edit')
        <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Page
        </a>
        @endcan
        <a href="{{ url($page->slug) }}" target="_blank" class="btn btn-secondary">
            <i class="fas fa-external-link-alt"></i> View on Site
        </a>
    </div>
</div>

<div class="row">
    <!-- Main Content -->
    <div class="col-8">
        <!-- Page Preview -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Page Content</h3>
                <div class="d-flex gap-2">
                    @if($page->is_active)
                        <span class="badge badge-success">Published</span>
                    @else
                        <span class="badge badge-warning">Draft</span>
                    @endif
                    @if($page->is_homepage)
                        <span class="badge badge-primary"><i class="fas fa-home"></i> Homepage</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if($page->featured_image)
                    <div class="page-featured-image mb-4">
                        <img src="{{ asset('storage/' . $page->featured_image) }}" alt="{{ $page->title }}">
                    </div>
                @endif

                <h2 class="page-title-preview">{{ $page->title }}</h2>

                @if($page->subtitle)
                    <p class="page-subtitle-preview">{{ $page->subtitle }}</p>
                @endif

                <div class="page-content">
                    {!! $page->content !!}
                </div>
            </div>
        </div>

        <!-- Sections -->
        @if($page->sections && $page->sections->count())
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Page Sections ({{ $page->sections->count() }})</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="sections-list">
                    @foreach($page->sections->sortBy('order') as $section)
                        <div class="section-item">
                            <div class="section-handle">
                                <i class="fas fa-grip-vertical"></i>
                            </div>
                            <div class="section-info">
                                <strong>{{ $section->title ?? 'Untitled Section' }}</strong>
                                <span class="section-type badge badge-secondary">{{ ucfirst($section->type) }}</span>
                                @if(!$section->is_active)
                                    <span class="badge badge-warning">Hidden</span>
                                @endif
                            </div>
                            <div class="section-preview">
                                {{ Str::limit(strip_tags($section->content), 100) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- SEO Preview -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">SEO Preview</h3>
            </div>
            <div class="card-body">
                <div class="seo-preview">
                    <div class="seo-preview-title">
                        {{ $page->meta_title ?? $page->title }} | MK Hotel
                    </div>
                    <div class="seo-preview-url">
                        {{ url($page->slug) }}
                    </div>
                    <div class="seo-preview-description">
                        {{ $page->meta_description ?? Str::limit(strip_tags($page->content), 155) }}
                    </div>
                </div>

                @if($page->meta_keywords)
                    <div class="mt-3">
                        <strong>Keywords:</strong>
                        <span class="text-muted">{{ $page->meta_keywords }}</span>
                    </div>
                @endif

                @if($page->og_image)
                    <div class="mt-3">
                        <strong>Open Graph Image:</strong>
                        <div class="og-image-preview mt-2">
                            <img src="{{ asset('storage/' . $page->og_image) }}" alt="OG Image">
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-4">
        <!-- Page Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Page Details</h3>
            </div>
            <div class="card-body">
                <div class="stat-list">
                    <div class="stat-list-item">
                        <span class="stat-list-label">Status</span>
                        <span class="stat-list-value">
                            @if($page->is_active)
                                <span class="text-success">Published</span>
                            @else
                                <span class="text-warning">Draft</span>
                            @endif
                        </span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">URL</span>
                        <span class="stat-list-value">/{{ $page->slug }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Template</span>
                        <span class="stat-list-value">{{ $page->template ?? 'Default' }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Sections</span>
                        <span class="stat-list-value">{{ $page->sections->count() }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Created</span>
                        <span class="stat-list-value">{{ $page->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Updated</span>
                        <span class="stat-list-value">{{ $page->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Flags -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Settings</h3>
            </div>
            <div class="card-body">
                <div class="flag-list">
                    <div class="flag-item">
                        <i class="fas fa-{{ $page->is_homepage ? 'check-circle text-success' : 'circle text-muted' }}"></i>
                        <span>Homepage</span>
                    </div>
                    <div class="flag-item">
                        <i class="fas fa-{{ $page->show_in_navigation ? 'check-circle text-success' : 'circle text-muted' }}"></i>
                        <span>Show in Navigation</span>
                    </div>
                    <div class="flag-item">
                        <i class="fas fa-{{ $page->show_in_footer ? 'check-circle text-success' : 'circle text-muted' }}"></i>
                        <span>Show in Footer</span>
                    </div>
                    <div class="flag-item">
                        <i class="fas fa-{{ $page->indexable ?? true ? 'check-circle text-success' : 'circle text-muted' }}"></i>
                        <span>Search Engine Indexing</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Actions</h3>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-2">
                    @can('pages.edit')
                    <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-primary btn-block">
                        <i class="fas fa-edit"></i> Edit Page
                    </a>
                    @endcan

                    <a href="{{ url($page->slug) }}" target="_blank" class="btn btn-secondary btn-block">
                        <i class="fas fa-external-link-alt"></i> View on Website
                    </a>

                    @if($page->is_active)
                        <form action="{{ route('admin.pages.toggle', $page) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning btn-block">
                                <i class="fas fa-eye-slash"></i> Unpublish
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.pages.toggle', $page) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-eye"></i> Publish
                            </button>
                        </form>
                    @endif

                    <button type="button" class="btn btn-secondary btn-block" onclick="copyToClipboard('{{ url($page->slug) }}')">
                        <i class="fas fa-link"></i> Copy URL
                    </button>

                    @if(!$page->is_homepage)
                    @can('pages.delete')
                    <form action="{{ route('admin.pages.destroy', $page) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this page?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash"></i> Delete Page
                        </button>
                    </form>
                    @endcan
                    @endif
                </div>
            </div>
        </div>

        <!-- Revision History -->
        @if(isset($revisions) && $revisions->count())
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Revisions</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="revision-list">
                    @foreach($revisions as $revision)
                        <div class="revision-item">
                            <div class="revision-icon">
                                <i class="fas fa-history"></i>
                            </div>
                            <div class="revision-info">
                                <span>{{ $revision->user->name ?? 'Unknown' }}</span>
                                <small>{{ $revision->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.page-featured-image img {
    width: 100%;
    max-height: 300px;
    object-fit: cover;
    border-radius: 8px;
}

.page-title-preview {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 0.5rem;
}

.page-subtitle-preview {
    font-size: 1.125rem;
    color: var(--gray-600);
    margin-bottom: 1.5rem;
}

.page-content {
    font-size: 1rem;
    line-height: 1.8;
    color: var(--gray-700);
}

.page-content h2,
.page-content h3,
.page-content h4 {
    color: var(--gray-900);
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
}

.page-content p {
    margin-bottom: 1rem;
}

.page-content img {
    max-width: 100%;
    border-radius: 8px;
    margin: 1rem 0;
}

.sections-list {
    display: flex;
    flex-direction: column;
}

.section-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid var(--gray-100);
}

.section-item:last-child {
    border-bottom: none;
}

.section-handle {
    color: var(--gray-400);
    padding: 0.25rem;
}

.section-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
    min-width: 200px;
}

.section-preview {
    flex: 1;
    color: var(--gray-500);
    font-size: 0.875rem;
}

.seo-preview {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    padding: 1rem;
}

.seo-preview-title {
    color: #1a0dab;
    font-size: 1.125rem;
    margin-bottom: 0.25rem;
}

.seo-preview-url {
    color: #006621;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.seo-preview-description {
    color: var(--gray-600);
    font-size: 0.875rem;
    line-height: 1.5;
}

.og-image-preview img {
    max-width: 200px;
    border-radius: 8px;
    border: 1px solid var(--gray-200);
}

.stat-list {
    display: flex;
    flex-direction: column;
}

.stat-list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--gray-100);
}

.stat-list-item:last-child {
    border-bottom: none;
}

.stat-list-label {
    color: var(--gray-600);
    font-size: 0.875rem;
}

.stat-list-value {
    font-weight: 500;
    color: var(--gray-800);
}

.flag-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.flag-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.flag-item i {
    font-size: 1rem;
}

.btn-block {
    width: 100%;
    text-align: center;
}

.revision-list {
    display: flex;
    flex-direction: column;
}

.revision-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--gray-100);
}

.revision-item:last-child {
    border-bottom: none;
}

.revision-icon {
    width: 32px;
    height: 32px;
    background: var(--gray-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-500);
}

.revision-info {
    display: flex;
    flex-direction: column;
}

.revision-info span {
    font-weight: 500;
    color: var(--gray-800);
    font-size: 0.875rem;
}

.revision-info small {
    color: var(--gray-500);
}
</style>
@endpush

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('URL copied to clipboard!');
    }, function() {
        alert('Failed to copy URL');
    });
}
</script>
@endpush
