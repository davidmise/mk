<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Booking;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of rooms.
     */
    public function index(Request $request)
    {
        $query = Room::with('roomType');

        // Filter by status
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        // Filter by room type
        if ($request->filled('room_type')) {
            $query->ofType($request->room_type);
        }

        // Filter by floor
        if ($request->filled('floor')) {
            $query->onFloor($request->floor);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('room_number', 'like', "%{$request->search}%");
        }

        $rooms = $query->orderBy('room_number')->paginate(20)->withQueryString();
        $roomTypes = RoomType::all();
        $floors = Room::distinct('floor')->pluck('floor')->filter();

        return view('admin.rooms.index', compact('rooms', 'roomTypes', 'floors'));
    }

    /**
     * Show the form for creating a new room.
     */
    public function create()
    {
        $roomTypes = RoomType::active()->get();
        return view('admin.rooms.create', compact('roomTypes'));
    }

    /**
     * Store a newly created room.
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_number' => 'required|string|max:20|unique:rooms,room_number',
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'nullable|string|max:10',
            'status' => 'required|in:available,maintenance',
            'features' => 'nullable|array',
            'notes' => 'nullable|string|max:500',
        ]);

        $room = Room::create([
            'room_number' => $request->room_number,
            'room_type_id' => $request->room_type_id,
            'floor' => $request->floor,
            'status' => $request->status,
            'features' => $request->features,
            'notes' => $request->notes,
            'is_active' => true,
        ]);

        ActivityLog::log('created', "Created room: {$room->room_number}", $room);

        return redirect()->route('admin.rooms.index')
            ->with('success', "Room {$room->room_number} created successfully.");
    }

    /**
     * Display the specified room.
     */
    public function show(Room $room)
    {
        $room->load('roomType');

        // Get upcoming bookings for this room
        $upcomingBookings = Booking::where('room_id', $room->id)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where('check_out', '>=', now())
            ->orderBy('check_in')
            ->take(5)
            ->get();

        return view('admin.rooms.show', compact('room', 'upcomingBookings'));
    }

    /**
     * Show the form for editing the room.
     */
    public function edit(Room $room)
    {
        $roomTypes = RoomType::active()->get();
        return view('admin.rooms.edit', compact('room', 'roomTypes'));
    }

    /**
     * Update the specified room.
     */
    public function update(Request $request, Room $room)
    {
        $request->validate([
            'room_number' => 'required|string|max:20|unique:rooms,room_number,' . $room->id,
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'nullable|string|max:10',
            'features' => 'nullable|array',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $oldValues = $room->toArray();

        $room->update([
            'room_number' => $request->room_number,
            'room_type_id' => $request->room_type_id,
            'floor' => $request->floor,
            'features' => $request->features,
            'notes' => $request->notes,
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::log('updated', "Updated room: {$room->room_number}", $room, $oldValues);

        return redirect()->route('admin.rooms.index')
            ->with('success', "Room {$room->room_number} updated successfully.");
    }

    /**
     * Update room status.
     */
    public function updateStatus(Request $request, Room $room)
    {
        $request->validate([
            'status' => 'required|in:available,maintenance,cleaning,out_of_order',
        ]);

        // Can't change status of occupied room
        if ($room->status === Room::STATUS_OCCUPIED && $request->status !== Room::STATUS_OCCUPIED) {
            return back()->with('error', 'Cannot change status of an occupied room. Please check out the guest first.');
        }

        $oldStatus = $room->status;
        $room->update(['status' => $request->status]);

        ActivityLog::log('status_changed', "Changed room {$room->room_number} status from {$oldStatus} to {$request->status}", $room);

        return back()->with('success', "Room status updated to {$request->status}.");
    }

    /**
     * Update room cleanliness status.
     */
    public function updateCleanliness(Request $request, Room $room)
    {
        $request->validate([
            'cleanliness' => 'required|in:clean,dirty,inspected',
        ]);

        $oldCleanliness = $room->cleanliness;
        $room->update(['cleanliness' => $request->cleanliness]);

        ActivityLog::log('cleanliness_changed', "Changed room {$room->room_number} cleanliness from {$oldCleanliness} to {$request->cleanliness}", $room);

        return back()->with('success', "Room cleanliness updated to {$request->cleanliness}.");
    }

    /**
     * Mark room as available.
     */
    public function markAvailable(Room $room)
    {
        if ($room->status === Room::STATUS_OCCUPIED) {
            return back()->with('error', 'Cannot mark occupied room as available.');
        }

        $room->markAsAvailable();
        ActivityLog::log('status_changed', "Marked room {$room->room_number} as available", $room);

        return back()->with('success', 'Room is now available.');
    }

    /**
     * Mark room for maintenance.
     */
    public function markMaintenance(Room $room)
    {
        if ($room->status === Room::STATUS_OCCUPIED) {
            return back()->with('error', 'Cannot put occupied room under maintenance.');
        }

        $room->markAsMaintenance();
        ActivityLog::log('status_changed', "Marked room {$room->room_number} for maintenance", $room);

        return back()->with('success', 'Room is now under maintenance.');
    }

    /**
     * Mark room for cleaning.
     */
    public function markCleaning(Room $room)
    {
        if ($room->status === Room::STATUS_OCCUPIED) {
            return back()->with('error', 'Cannot mark occupied room for cleaning.');
        }

        $room->markAsCleaning();
        ActivityLog::log('status_changed', "Marked room {$room->room_number} for cleaning", $room);

        return back()->with('success', 'Room is marked for cleaning.');
    }

    /**
     * Bulk create rooms.
     */
    public function bulkCreate(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'required|string|max:10',
            'start_number' => 'required|integer|min:1',
            'end_number' => 'required|integer|gte:start_number',
            'prefix' => 'nullable|string|max:5',
        ]);

        $created = 0;
        $skipped = [];

        for ($i = $request->start_number; $i <= $request->end_number; $i++) {
            $roomNumber = ($request->prefix ?? '') . str_pad($i, 2, '0', STR_PAD_LEFT);

            if (Room::where('room_number', $roomNumber)->exists()) {
                $skipped[] = $roomNumber;
                continue;
            }

            Room::create([
                'room_number' => $roomNumber,
                'room_type_id' => $request->room_type_id,
                'floor' => $request->floor,
                'status' => Room::STATUS_AVAILABLE,
                'is_active' => true,
            ]);

            $created++;
        }

        $message = "{$created} rooms created successfully.";
        if (count($skipped) > 0) {
            $message .= " Skipped existing rooms: " . implode(', ', $skipped);
        }

        ActivityLog::log('bulk_created', "Bulk created {$created} rooms on floor {$request->floor}");

        return back()->with('success', $message);
    }

    /**
     * Delete a room.
     */
    public function destroy(Room $room)
    {
        // Check if room has any bookings
        if ($room->bookings()->whereIn('status', ['confirmed', 'checked_in'])->exists()) {
            return back()->with('error', 'Cannot delete room with active bookings.');
        }

        ActivityLog::log('deleted', "Deleted room: {$room->room_number}", $room);

        $room->delete();

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room deleted successfully.');
    }
}
