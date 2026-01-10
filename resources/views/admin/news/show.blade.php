@extends('admin.layouts.app')

@section('title', $article->title)

@section('breadcrumb')
    <a href="{{ route('admin.news.index') }}">News</a>
    <i class="fas fa-chevron-right"></i>
    <span>{{ Str::limit($article->title, 30) }}</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $article->title }}</h1>
        <p class="page-subtitle">
            By {{ $article->author->name ?? 'Unknown' }} &bull;
            {{ $article->created_at->format('M d, Y') }}
        </p>
    </div>
    <div class="d-flex gap-2">
        @if($article->status === 'draft')
            <form action="{{ route('admin.news.publish', $article) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check"></i> Publish
                </button>
            </form>
        @elseif($article->status === 'published')
            <form action="{{ route('admin.news.unpublish', $article) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-pause"></i> Unpublish
                </button>
            </form>
        @endif
        @can('news.edit')
        <a href="{{ route('admin.news.edit', $article) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Article
        </a>
        @endcan
        <a href="{{ $article->url ?? '#' }}" target="_blank" class="btn btn-secondary">
            <i class="fas fa-external-link-alt"></i> View on Site
        </a>
    </div>
</div>

<div class="row">
    <!-- Main Content -->
    <div class="col-8">
        <!-- Article Preview -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Article Preview</h3>
                <div class="d-flex gap-2">
                    @switch($article->status)
                        @case('published')
                            <span class="badge badge-success">Published</span>
                            @break
                        @case('draft')
                            <span class="badge badge-warning">Draft</span>
                            @break
                        @case('scheduled')
                            <span class="badge badge-info">Scheduled</span>
                            @break
                    @endswitch
                    @if($article->is_featured)
                        <span class="badge badge-primary"><i class="fas fa-star"></i> Featured</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if($article->featured_image)
                    <div class="article-featured-image">
                        <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}">
                        @if($article->featured_image_caption)
                            <p class="image-caption">{{ $article->featured_image_caption }}</p>
                        @endif
                    </div>
                @endif

                <div class="article-meta">
                    @if($article->category)
                        <span class="article-category" style="background: {{ $article->category->color ?? '#6366f1' }};">
                            {{ $article->category->name }}
                        </span>
                    @endif
                    <span class="article-date">
                        <i class="far fa-calendar-alt"></i>
                        @if($article->published_at)
                            {{ $article->published_at->format('F d, Y') }}
                        @else
                            Not published
                        @endif
                    </span>
                    <span class="article-reading-time">
                        <i class="far fa-clock"></i> {{ $article->reading_time ?? 5 }} min read
                    </span>
                </div>

                <h2 class="article-title-preview">{{ $article->title }}</h2>

                @if($article->excerpt)
                    <div class="article-excerpt">
                        <p>{{ $article->excerpt }}</p>
                    </div>
                @endif

                <div class="article-content">
                    {!! $article->content !!}
                </div>

                @if($article->tags && $article->tags->count())
                    <div class="article-tags">
                        @foreach($article->tags as $tag)
                            <a href="{{ route('admin.news.index', ['tag' => $tag->slug]) }}" class="tag">
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- SEO Preview -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">SEO Preview</h3>
            </div>
            <div class="card-body">
                <div class="seo-preview">
                    <div class="seo-preview-title">
                        {{ $article->meta_title ?? $article->title }}
                    </div>
                    <div class="seo-preview-url">
                        {{ url('/news/' . $article->slug) }}
                    </div>
                    <div class="seo-preview-description">
                        {{ $article->meta_description ?? Str::limit(strip_tags($article->excerpt ?? $article->content), 155) }}
                    </div>
                </div>

                @if($article->meta_keywords)
                    <div class="mt-3">
                        <strong>Keywords:</strong>
                        <span class="text-muted">{{ $article->meta_keywords }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-4">
        <!-- Article Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Details</h3>
            </div>
            <div class="card-body">
                <div class="stat-list">
                    <div class="stat-list-item">
                        <span class="stat-list-label">Status</span>
                        <span class="stat-list-value">
                            @switch($article->status)
                                @case('published')
                                    <span class="text-success">Published</span>
                                    @break
                                @case('draft')
                                    <span class="text-warning">Draft</span>
                                    @break
                                @case('scheduled')
                                    <span class="text-info">Scheduled</span>
                                    @break
                            @endswitch
                        </span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Author</span>
                        <span class="stat-list-value">{{ $article->author->name ?? 'Unknown' }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Category</span>
                        <span class="stat-list-value">{{ $article->category->name ?? 'Uncategorized' }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Created</span>
                        <span class="stat-list-value">{{ $article->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Updated</span>
                        <span class="stat-list-value">{{ $article->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                    @if($article->published_at)
                        <div class="stat-list-item">
                            <span class="stat-list-label">Published</span>
                            <span class="stat-list-value">{{ $article->published_at->format('M d, Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Statistics</h3>
            </div>
            <div class="card-body">
                <div class="stats-row">
                    <div class="stat-box">
                        <div class="stat-box-value">{{ number_format($article->views ?? 0) }}</div>
                        <div class="stat-box-label">Views</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-value">{{ $article->reading_time ?? 5 }}</div>
                        <div class="stat-box-label">Min Read</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-value">{{ $article->tags->count() ?? 0 }}</div>
                        <div class="stat-box-label">Tags</div>
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
                    @can('news.edit')
                    <a href="{{ route('admin.news.edit', $article) }}" class="btn btn-primary btn-block">
                        <i class="fas fa-edit"></i> Edit Article
                    </a>
                    @endcan

                    @if($article->status === 'draft')
                        <form action="{{ route('admin.news.publish', $article) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-check"></i> Publish Now
                            </button>
                        </form>
                    @endif

                    <button type="button" class="btn btn-secondary btn-block" onclick="copyToClipboard('{{ url('/news/' . $article->slug) }}')">
                        <i class="fas fa-link"></i> Copy URL
                    </button>

                    @if($article->is_featured)
                        <form action="{{ route('admin.news.toggle-featured', $article) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning btn-block">
                                <i class="fas fa-star"></i> Remove Featured
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.news.toggle-featured', $article) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-primary btn-block">
                                <i class="far fa-star"></i> Mark as Featured
                            </button>
                        </form>
                    @endif

                    @can('news.delete')
                    <form action="{{ route('admin.news.destroy', $article) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this article?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash"></i> Delete Article
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Related Articles -->
        @if(isset($relatedArticles) && $relatedArticles->count())
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Related Articles</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="related-list">
                    @foreach($relatedArticles as $related)
                        <a href="{{ route('admin.news.show', $related) }}" class="related-item">
                            @if($related->featured_image)
                                <img src="{{ asset('storage/' . $related->featured_image) }}" alt="{{ $related->title }}">
                            @else
                                <div class="related-placeholder">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                            @endif
                            <div class="related-info">
                                <strong>{{ Str::limit($related->title, 40) }}</strong>
                                <small>{{ $related->published_at?->format('M d, Y') }}</small>
                            </div>
                        </a>
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
.article-featured-image {
    margin-bottom: 1.5rem;
}

.article-featured-image img {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 8px;
}

.image-caption {
    text-align: center;
    color: var(--gray-500);
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.article-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.article-category {
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    color: white;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.article-date,
.article-reading-time {
    color: var(--gray-500);
    font-size: 0.875rem;
}

.article-title-preview {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 1rem;
    line-height: 1.3;
}

.article-excerpt {
    background: var(--gray-50);
    border-left: 4px solid var(--primary);
    padding: 1rem;
    margin-bottom: 1.5rem;
    border-radius: 0 8px 8px 0;
}

.article-excerpt p {
    margin: 0;
    color: var(--gray-700);
    font-style: italic;
}

.article-content {
    font-size: 1rem;
    line-height: 1.8;
    color: var(--gray-700);
}

.article-content h2,
.article-content h3,
.article-content h4 {
    color: var(--gray-900);
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
}

.article-content p {
    margin-bottom: 1rem;
}

.article-content img {
    max-width: 100%;
    border-radius: 8px;
    margin: 1rem 0;
}

.article-content blockquote {
    border-left: 4px solid var(--primary);
    padding-left: 1rem;
    margin: 1.5rem 0;
    color: var(--gray-600);
    font-style: italic;
}

.article-tags {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--gray-200);
}

.tag {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: var(--gray-100);
    color: var(--gray-700);
    border-radius: 999px;
    font-size: 0.875rem;
    text-decoration: none;
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
    transition: var(--transition);
}

.tag:hover {
    background: var(--primary);
    color: white;
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
    font-weight: 600;
    color: var(--gray-800);
}

.stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.stat-box {
    text-align: center;
    padding: 1rem;
    background: var(--gray-50);
    border-radius: 8px;
}

.stat-box-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
}

.stat-box-label {
    font-size: 0.75rem;
    color: var(--gray-500);
    text-transform: uppercase;
}

.btn-block {
    width: 100%;
    text-align: center;
}

.btn-outline-primary {
    background: transparent;
    border: 1px solid var(--primary);
    color: var(--primary);
}

.btn-outline-primary:hover {
    background: var(--primary);
    color: white;
}

.related-list {
    display: flex;
    flex-direction: column;
}

.related-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    text-decoration: none;
    border-bottom: 1px solid var(--gray-100);
    transition: var(--transition);
}

.related-item:last-child {
    border-bottom: none;
}

.related-item:hover {
    background: var(--gray-50);
}

.related-item img {
    width: 60px;
    height: 45px;
    object-fit: cover;
    border-radius: 4px;
}

.related-placeholder {
    width: 60px;
    height: 45px;
    background: var(--gray-200);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-400);
}

.related-info {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.related-info strong {
    color: var(--gray-800);
    font-size: 0.875rem;
}

.related-info small {
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
