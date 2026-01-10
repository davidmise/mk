<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * Display a listing of gallery images.
     */
    public function index(Request $request)
    {
        $query = GalleryImage::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('slider')) {
            $query->where('is_slider', true);
        }

        $images = $query->ordered()->paginate(24)->withQueryString();
        $categories = GalleryImage::distinct('category')->pluck('category')->filter();

        return view('admin.gallery.index', compact('images', 'categories'));
    }

    /**
     * Show the form for creating a new gallery image.
     */
    public function create()
    {
        $categories = GalleryImage::distinct('category')->pluck('category')->filter();
        return view('admin.gallery.create', compact('categories'));
    }

    /**
     * Store a newly created gallery image.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|max:10240',
            'thumbnail' => 'nullable|image|max:2048',
            'alt_text' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'is_slider' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['image', 'thumbnail']);
        $data['is_slider'] = $request->boolean('is_slider');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['order'] = $request->order ?? GalleryImage::max('order') + 1;

        $data['image'] = $request->file('image')->store('gallery', 'public');

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('gallery/thumbs', 'public');
        }

        $image = GalleryImage::create($data);

        ActivityLog::log('created', "Added gallery image: {$image->title}", $image);

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Image added to gallery.');
    }

    /**
     * Store multiple gallery images.
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'images' => 'required|array|min:1',
            'images.*' => 'image|max:10240',
            'category' => 'nullable|string|max:100',
        ]);

        $count = 0;
        $order = GalleryImage::max('order') + 1;

        foreach ($request->file('images') as $file) {
            GalleryImage::create([
                'image' => $file->store('gallery', 'public'),
                'category' => $request->category,
                'order' => $order++,
                'is_active' => true,
            ]);
            $count++;
        }

        ActivityLog::log('bulk_created', "Added {$count} images to gallery");

        return redirect()->route('admin.gallery.index')
            ->with('success', "{$count} images added to gallery.");
    }

    /**
     * Show the form for editing the gallery image.
     */
    public function edit(GalleryImage $gallery)
    {
        $categories = GalleryImage::distinct('category')->pluck('category')->filter();
        return view('admin.gallery.edit', compact('gallery', 'categories'));
    }

    /**
     * Update the specified gallery image.
     */
    public function update(Request $request, GalleryImage $gallery)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:10240',
            'thumbnail' => 'nullable|image|max:2048',
            'alt_text' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'is_slider' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['image', 'thumbnail']);
        $data['is_slider'] = $request->boolean('is_slider');
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($gallery->image);
            $data['image'] = $request->file('image')->store('gallery', 'public');
        }

        if ($request->hasFile('thumbnail')) {
            if ($gallery->thumbnail) {
                Storage::disk('public')->delete($gallery->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('gallery/thumbs', 'public');
        }

        $gallery->update($data);

        ActivityLog::log('updated', "Updated gallery image: {$gallery->title}", $gallery);

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Image updated successfully.');
    }

    /**
     * Delete the specified gallery image.
     */
    public function destroy(GalleryImage $gallery)
    {
        Storage::disk('public')->delete($gallery->image);
        if ($gallery->thumbnail) {
            Storage::disk('public')->delete($gallery->thumbnail);
        }

        ActivityLog::log('deleted', "Deleted gallery image: {$gallery->title}", $gallery);

        $gallery->delete();

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Image deleted successfully.');
    }

    /**
     * Toggle slider status.
     */
    public function toggleSlider(GalleryImage $gallery)
    {
        $gallery->update(['is_slider' => !$gallery->is_slider]);

        return back()->with('success', 'Slider status updated.');
    }

    /**
     * Update gallery order.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:gallery_images,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            GalleryImage::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['success' => true]);
    }
}
