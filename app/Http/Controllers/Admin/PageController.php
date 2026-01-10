<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Section;
use App\Models\HeroSlide;
use App\Models\GalleryImage;
use App\Models\NavigationItem;
use App\Models\SocialLink;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    /**
     * Display a listing of pages.
     */
    public function index()
    {
        $pages = Page::withCount('sections')->paginate(15);
        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create()
    {
        return view('admin.pages.create');
    }

    /**
     * Store a newly created page.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'hero_heading' => 'nullable|string|max:255',
            'hero_subtext' => 'nullable|string',
            'hero_image' => 'nullable|image|max:5120',
            'is_active' => 'boolean',
        ]);

        $data = $request->except('hero_image');
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('hero_image')) {
            $data['hero_image'] = $request->file('hero_image')->store('pages', 'public');
        }

        $page = Page::create($data);

        ActivityLog::log('page', 'created', "Created page: {$page->title}", $page);

        return redirect()->route('admin.pages.edit', $page)
            ->with('success', 'Page created successfully. You can now add sections.');
    }

    /**
     * Show the form for editing the page.
     */
    public function edit(Page $page)
    {
        $page->load(['sections' => fn($q) => $q->orderBy('order')]);
        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update the specified page.
     */
    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $page->id,
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'hero_heading' => 'nullable|string|max:255',
            'hero_subtext' => 'nullable|string',
            'hero_image' => 'nullable|image|max:5120',
            'is_active' => 'boolean',
        ]);

        $oldValues = $page->toArray();

        $data = $request->except('hero_image');
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('hero_image')) {
            if ($page->hero_image) {
                Storage::disk('public')->delete($page->hero_image);
            }
            $data['hero_image'] = $request->file('hero_image')->store('pages', 'public');
        }

        $page->update($data);

        ActivityLog::log('page', 'updated', "Updated page: {$page->title}", $page, $oldValues);

        return back()->with('success', 'Page updated successfully.');
    }

    /**
     * Delete the page.
     */
    public function destroy(Page $page)
    {
        // Prevent deletion of core pages
        $corePages = ['home', 'about', 'contact', 'rooms', 'amenities', 'gallery'];
        if (in_array($page->slug, $corePages)) {
            return back()->with('error', 'Core pages cannot be deleted.');
        }

        if ($page->hero_image) {
            Storage::disk('public')->delete($page->hero_image);
        }

        ActivityLog::log('page', 'deleted', "Deleted page: {$page->title}", $page);

        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page deleted successfully.');
    }

    /**
     * Add a section to the page.
     */
    public function addSection(Request $request, Page $page)
    {
        $request->validate([
            'key' => 'required|string|max:100',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'image_alt' => 'nullable|string|max:255',
            'extra_data' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $data = $request->except('image');
        $data['page_id'] = $page->id;
        $data['order'] = $page->sections()->max('order') + 1;
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('sections', 'public');
        }

        $section = Section::create($data);

        ActivityLog::log('section', 'created', "Added section '{$section->key}' to page: {$page->title}", $section);

        return back()->with('success', 'Section added successfully.');
    }

    /**
     * Update a section.
     */
    public function updateSection(Request $request, Page $page, Section $section)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'image_alt' => 'nullable|string|max:255',
            'extra_data' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $data = $request->except('image');
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            if ($section->image) {
                Storage::disk('public')->delete($section->image);
            }
            $data['image'] = $request->file('image')->store('sections', 'public');
        }

        $section->update($data);

        ActivityLog::log('section', 'updated', "Updated section '{$section->key}' on page: {$page->title}", $section);

        return back()->with('success', 'Section updated successfully.');
    }

    /**
     * Delete a section.
     */
    public function deleteSection(Page $page, Section $section)
    {
        if ($section->image) {
            Storage::disk('public')->delete($section->image);
        }

        ActivityLog::log('section', 'deleted', "Deleted section '{$section->key}' from page: {$page->title}", $section);

        $section->delete();

        return back()->with('success', 'Section deleted successfully.');
    }

    /**
     * Reorder sections.
     */
    public function reorderSections(Request $request, Page $page)
    {
        $request->validate([
            'sections' => 'required|array',
            'sections.*.id' => 'required|exists:sections,id',
            'sections.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->sections as $item) {
            Section::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Reorder pages.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|exists:pages,id',
            'order.*.position' => 'required|integer|min:0',
        ]);

        foreach ($request->order as $item) {
            Page::where('id', $item['id'])->update(['order' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Bulk update pages.
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pages,id',
            'is_active' => 'required|boolean',
        ]);

        $updatedCount = Page::whereIn('id', $request->ids)
            ->update(['is_active' => $request->is_active]);

        $action = $request->is_active ? 'published' : 'unpublished';
        ActivityLog::log('page', 'bulk_updated', "Bulk {$action} {$updatedCount} page(s)");

        return response()->json([
            'success' => true,
            'message' => "Successfully {$action} {$updatedCount} page(s).",
            'updated' => $updatedCount
        ]);
    }

    /**
     * Bulk delete pages.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pages,id',
        ]);

        $corePages = ['home', 'about', 'contact', 'rooms', 'amenities', 'gallery'];
        $pages = Page::whereIn('id', $request->ids)->get();

        $deletedCount = 0;
        $skippedCount = 0;

        foreach ($pages as $page) {
            if (in_array($page->slug, $corePages)) {
                $skippedCount++;
                continue;
            }

            if ($page->hero_image) {
                Storage::disk('public')->delete($page->hero_image);
            }

            ActivityLog::log('page', 'deleted', "Deleted page: {$page->title}", $page);
            $page->delete();
            $deletedCount++;
        }

        $message = "Deleted {$deletedCount} page(s) successfully.";
        if ($skippedCount > 0) {
            $message .= " Skipped {$skippedCount} core page(s).";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'deleted' => $deletedCount,
            'skipped' => $skippedCount
        ]);
    }
}
