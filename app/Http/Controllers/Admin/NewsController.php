<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    /**
     * Display a listing of news articles.
     */
    public function index(Request $request)
    {
        $query = News::with('author');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%');
            });
        }

        $articles = $query->latest()->paginate(15)->withQueryString();

        // Calculate statistics
        $stats = [
            'total' => News::count(),
            'published' => News::where('status', 'published')->count(),
            'draft' => News::where('status', 'draft')->count(),
            'scheduled' => News::where('status', 'scheduled')->count(),
        ];

        return view('admin.news.index', compact('articles', 'stats'));
    }

    /**
     * Show the form for creating a new article.
     */
    public function create()
    {
        $categories = NewsCategory::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.news.create', compact('categories'));
    }

    /**
     * Store a newly created article.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news,slug',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:5120',
            'featured_image_caption' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:news_categories,id',
            'status' => 'required|in:draft,published,scheduled',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $data = $request->except('featured_image');
        $data['slug'] = $request->slug ?: Str::slug($request->title);
        $data['author_id'] = auth()->id();
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->status === 'published') {
            $data['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('news', 'public');
        }

        $article = News::create($data);

        ActivityLog::log('news', 'created', "Created article: {$article->title}", $article);

        return redirect()->route('admin.news.index')
            ->with('success', 'Article created successfully.');
    }

    /**
     * Display the specified article.
     */
    public function show(News $article)
    {
        return view('admin.news.show', ['article' => $article]);
    }

    /**
     * Show the form for editing the article.
     */
    public function edit(News $article)
    {
        $categories = NewsCategory::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.news.edit', ['article' => $article, 'categories' => $categories]);
    }

    /**
     * Update the specified article.
     */
    public function update(Request $request, News $article)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news,slug,' . $article->id,
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:5120',
            'featured_image_caption' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:news_categories,id',
            'status' => 'required|in:draft,published,scheduled',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $data = $request->except('featured_image');
        $data['slug'] = $request->slug ?: Str::slug($request->title);
        $data['is_featured'] = $request->boolean('is_featured');

        // Set published_at if publishing for first time
        if ($request->status === 'published' && !$article->published_at) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('news', 'public');
        }

        $article->update($data);

        ActivityLog::log('news', 'updated', "Updated article: {$article->title}", $article);

        return redirect()->route('admin.news.index')
            ->with('success', 'Article updated successfully.');
    }

    /**
     * Publish the article.
     */
    public function publish(News $article)
    {
        $article->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        ActivityLog::log('news', 'published', "Published article: {$article->title}", $article);

        return back()->with('success', 'Article has been published.');
    }

    /**
     * Unpublish the article.
     */
    public function unpublish(News $article)
    {
        $article->update(['status' => 'draft']);

        ActivityLog::log('news', 'unpublished', "Unpublished article: {$article->title}", $article);

        return back()->with('success', 'Article has been unpublished.');
    }

    /**
     * Archive the article.
     */
    public function archive(News $article)
    {
        $article->update(['status' => 'archived']);

        ActivityLog::log('news', 'archived', "Archived article: {$article->title}", $article);

        return back()->with('success', 'Article has been archived.');
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(News $article)
    {
        $article->update(['is_featured' => !$article->is_featured]);

        $status = $article->is_featured ? 'featured' : 'unfeatured';
        ActivityLog::log('news', 'status_changed', "Article {$article->title} marked as {$status}", $article);

        return back()->with('success', "Article is now {$status}.");
    }

    /**
     * Delete the article.
     */
    public function destroy(News $article)
    {
        if ($article->featured_image) {
            Storage::disk('public')->delete($article->featured_image);
        }

        ActivityLog::log('news', 'deleted', "Deleted article: {$article->title}", $article);

        $article->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'Article deleted successfully.');
    }

    /**
     * Bulk actions on articles.
     */
    public function bulk(Request $request)
    {
        $request->validate([
            'action' => 'required|in:publish,unpublish,delete,archive',
            'ids' => 'required|array',
            'ids.*' => 'exists:news,id',
        ]);

        $articles = News::whereIn('id', $request->ids)->get();
        $count = $articles->count();

        switch ($request->action) {
            case 'publish':
                foreach ($articles as $article) {
                    $article->update([
                        'status' => 'published',
                        'published_at' => $article->published_at ?? now(),
                    ]);
                }
                $message = "{$count} articles published successfully.";
                break;

            case 'unpublish':
                News::whereIn('id', $request->ids)->update(['status' => 'draft']);
                $message = "{$count} articles unpublished successfully.";
                break;

            case 'archive':
                News::whereIn('id', $request->ids)->update(['status' => 'archived']);
                $message = "{$count} articles archived successfully.";
                break;

            case 'delete':
                foreach ($articles as $article) {
                    if ($article->featured_image) {
                        Storage::disk('public')->delete($article->featured_image);
                    }
                    $article->delete();
                }
                $message = "{$count} articles deleted successfully.";
                break;
        }

        ActivityLog::log('news', 'bulk_action', "Bulk {$request->action} on {$count} articles");

        return redirect()->route('admin.news.index')->with('success', $message);
    }
}
