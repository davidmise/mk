<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AmenityController extends Controller
{
    /**
     * Display a listing of amenities.
     */
    public function index(Request $request)
    {
        $query = Amenity::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $amenities = $query->ordered()->paginate(20)->withQueryString();

        return view('admin.amenities.index', compact('amenities'));
    }

    /**
     * Show the form for creating a new amenity.
     */
    public function create()
    {
        return view('admin.amenities.create');
    }

    /**
     * Store a newly created amenity.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:amenities,slug',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:room,property,service',
            'icon' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:2048',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->except('image');
        $data['slug'] = $request->slug ?? \Str::slug($request->name);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('amenities', 'public');
        }

        $amenity = Amenity::create($data);

        ActivityLog::log('created', "Created amenity: {$amenity->name}", $amenity);

        return redirect()->route('admin.amenities.index')
            ->with('success', 'Amenity created successfully.');
    }

    /**
     * Show the form for editing the amenity.
     */
    public function edit(Amenity $amenity)
    {
        return view('admin.amenities.edit', compact('amenity'));
    }

    /**
     * Update the specified amenity.
     */
    public function update(Request $request, Amenity $amenity)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:amenities,slug,' . $amenity->id,
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:room,property,service',
            'icon' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:2048',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $oldValues = $amenity->toArray();

        $data = $request->except('image');
        $data['slug'] = $request->slug ?? \Str::slug($request->name);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            if ($amenity->image) {
                Storage::disk('public')->delete($amenity->image);
            }
            $data['image'] = $request->file('image')->store('amenities', 'public');
        }

        $amenity->update($data);

        ActivityLog::log('updated', "Updated amenity: {$amenity->name}", $amenity, $oldValues);

        return redirect()->route('admin.amenities.index')
            ->with('success', 'Amenity updated successfully.');
    }

    /**
     * Delete the specified amenity.
     */
    public function destroy(Amenity $amenity)
    {
        if ($amenity->image) {
            Storage::disk('public')->delete($amenity->image);
        }

        ActivityLog::log('deleted', "Deleted amenity: {$amenity->name}", $amenity);

        $amenity->delete();

        return redirect()->route('admin.amenities.index')
            ->with('success', 'Amenity deleted successfully.');
    }

    /**
     * Toggle amenity status.
     */
    public function toggleStatus(Amenity $amenity)
    {
        $amenity->update(['is_active' => !$amenity->is_active]);

        $status = $amenity->is_active ? 'activated' : 'deactivated';
        ActivityLog::log('status_changed', "Amenity {$amenity->name} {$status}", $amenity);

        return back()->with('success', "Amenity {$status} successfully.");
    }

    /**
     * Update amenities order.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:amenities,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            Amenity::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['success' => true]);
    }
}
