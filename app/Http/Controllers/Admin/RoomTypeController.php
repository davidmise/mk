<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\Amenity;
use App\Models\SeasonalPrice;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of room types.
     */
    public function index()
    {
        $roomTypes = RoomType::withCount('bookings')
            ->ordered()
            ->get();

        return view('admin.room-types.index', compact('roomTypes'));
    }

    /**
     * Show the form for creating a new room type.
     */
    public function create()
    {
        $amenities = Amenity::active()->ordered()->get();
        return view('admin.room-types.create', compact('amenities'));
    }

    /**
     * Store a newly created room type.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:room_types,slug',
            'price' => 'required|numeric|min:0',
            'price_label' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'secondary_description' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'total_rooms' => 'required|integer|min:1',
            'order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
            'extra_data' => 'nullable|array',
        ]);

        $data = $request->except(['image', 'amenities']);
        $data['slug'] = $request->slug ?? Str::slug($request->name);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('room-types', 'public');
        }

        // Store amenities as JSON array
        $data['amenities'] = $request->amenities ?? [];

        $roomType = RoomType::create($data);

        ActivityLog::log('room_type', 'created', "Created room type: {$roomType->name}", $roomType);

        return redirect()->route('admin.room-types.index')
            ->with('success', 'Room type created successfully.');
    }

    /**
     * Display the specified room type.
     */
    public function show(RoomType $roomType)
    {
        $roomType->load(['amenities', 'rooms', 'seasonalPrices']);

        return view('admin.room-types.show', compact('roomType'));
    }

    /**
     * Show the form for editing the room type.
     */
    public function edit(RoomType $roomType)
    {
        $amenities = Amenity::active()->ordered()->get();
        $selectedAmenities = $roomType->amenities ?? [];

        return view('admin.room-types.edit', compact('roomType', 'amenities', 'selectedAmenities'));
    }

    /**
     * Update the specified room type.
     */
    public function update(Request $request, RoomType $roomType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:room_types,slug,' . $roomType->id,
            'price' => 'required|numeric|min:0',
            'price_label' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'secondary_description' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'total_rooms' => 'required|integer|min:1',
            'order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
        ]);

        $data = $request->except(['image', 'amenities']);
        $data['slug'] = $request->slug ?? Str::slug($request->name);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($roomType->image) {
                Storage::disk('public')->delete($roomType->image);
            }
            $data['image'] = $request->file('image')->store('room-types', 'public');
        }

        // Store amenities as JSON array
        $data['amenities'] = $request->amenities ?? [];

        $roomType->update($data);

        ActivityLog::log('room_type', 'updated', "Updated room type: {$roomType->name}", $roomType);

        return redirect()->route('admin.room-types.index')
            ->with('success', 'Room type updated successfully.');
    }

    /**
     * Delete the specified room type.
     */
    public function destroy(RoomType $roomType)
    {
        // Check for existing bookings
        if ($roomType->bookings()->exists()) {
            return back()->with('error', 'Cannot delete room type with existing bookings.');
        }

        // Check for rooms
        if ($roomType->rooms()->exists()) {
            return back()->with('error', 'Cannot delete room type with assigned rooms.');
        }

        // Delete image
        if ($roomType->image) {
            Storage::disk('public')->delete($roomType->image);
        }

        ActivityLog::log('room_type', 'deleted', "Deleted room type: {$roomType->name}", $roomType);

        $roomType->delete();

        return redirect()->route('admin.room-types.index')
            ->with('success', 'Room type deleted successfully.');
    }

    /**
     * Manage seasonal prices for a room type.
     */
    public function seasonalPrices(RoomType $roomType)
    {
        $seasonalPrices = $roomType->seasonalPrices()->orderBy('start_date')->get();

        return view('admin.room-types.seasonal-prices', compact('roomType', 'seasonalPrices'));
    }

    /**
     * Store a seasonal price.
     */
    public function storeSeasonalPrice(Request $request, RoomType $roomType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'priority' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $seasonalPrice = $roomType->seasonalPrices()->create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'price' => $request->price,
            'discount_percentage' => $request->discount_percentage,
            'priority' => $request->priority ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::log('seasonal_price', 'created', "Created seasonal price '{$seasonalPrice->name}' for {$roomType->name}", $seasonalPrice);

        return back()->with('success', 'Seasonal price created successfully.');
    }

    /**
     * Update a seasonal price.
     */
    public function updateSeasonalPrice(Request $request, RoomType $roomType, SeasonalPrice $seasonalPrice)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'priority' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $seasonalPrice->update([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'price' => $request->price,
            'discount_percentage' => $request->discount_percentage,
            'priority' => $request->priority ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::log('seasonal_price', 'updated', "Updated seasonal price '{$seasonalPrice->name}' for {$roomType->name}", $seasonalPrice);

        return back()->with('success', 'Seasonal price updated successfully.');
    }

    /**
     * Delete a seasonal price.
     */
    public function destroySeasonalPrice(RoomType $roomType, SeasonalPrice $seasonalPrice)
    {
        ActivityLog::log('seasonal_price', 'deleted', "Deleted seasonal price '{$seasonalPrice->name}' from {$roomType->name}", $seasonalPrice);

        $seasonalPrice->delete();

        return back()->with('success', 'Seasonal price deleted successfully.');
    }

    /**
     * Update room type order.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:room_types,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            RoomType::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['success' => true]);
    }
}
