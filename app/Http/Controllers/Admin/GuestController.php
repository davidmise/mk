<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    /**
     * Display a listing of guests.
     */
    public function index(Request $request)
    {
        $query = Guest::withCount('bookings');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('vip_status')) {
            $query->vip($request->vip_status);
        }

        $guests = $query->latest()->paginate(20)->withQueryString();

        return view('admin.guests.index', compact('guests'));
    }

    /**
     * Show the form for creating a new guest.
     */
    public function create()
    {
        return view('admin.guests.create');
    }

    /**
     * Store a newly created guest.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'id_type' => 'nullable|in:passport,national_id,driver_license',
            'id_number' => 'nullable|string|max:100',
            'nationality' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'vip_status' => 'nullable|in:regular,silver,gold,platinum',
        ]);

        $guest = Guest::create($request->all());

        ActivityLog::log('created', "Created guest: {$guest->full_name}", $guest);

        return redirect()->route('admin.guests.index')
            ->with('success', 'Guest profile created successfully.');
    }

    /**
     * Display the specified guest.
     */
    public function show(Guest $guest)
    {
        $guest->load([
            'bookings' => fn($q) => $q->latest()->take(10),
            'bookings.roomType',
            'payments' => fn($q) => $q->latest()->take(10),
        ]);

        return view('admin.guests.show', compact('guest'));
    }

    /**
     * Show the form for editing the guest.
     */
    public function edit(Guest $guest)
    {
        return view('admin.guests.edit', compact('guest'));
    }

    /**
     * Update the specified guest.
     */
    public function update(Request $request, Guest $guest)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'id_type' => 'nullable|in:passport,national_id,driver_license',
            'id_number' => 'nullable|string|max:100',
            'nationality' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'vip_status' => 'nullable|in:regular,silver,gold,platinum',
        ]);

        $oldValues = $guest->toArray();
        $guest->update($request->all());

        ActivityLog::log('updated', "Updated guest: {$guest->full_name}", $guest, $oldValues);

        return redirect()->route('admin.guests.index')
            ->with('success', 'Guest profile updated successfully.');
    }

    /**
     * Update VIP status.
     */
    public function updateVipStatus(Request $request, Guest $guest)
    {
        $request->validate([
            'vip_status' => 'required|in:regular,silver,gold,platinum',
        ]);

        $oldStatus = $guest->vip_status;
        $guest->update(['vip_status' => $request->vip_status]);

        ActivityLog::log('status_changed', "Changed guest {$guest->full_name} VIP status from {$oldStatus} to {$request->vip_status}", $guest);

        return back()->with('success', 'VIP status updated successfully.');
    }

    /**
     * Merge duplicate guests.
     */
    public function merge(Request $request)
    {
        $request->validate([
            'primary_id' => 'required|exists:guests,id',
            'merge_ids' => 'required|array|min:1',
            'merge_ids.*' => 'exists:guests,id|different:primary_id',
        ]);

        $primary = Guest::find($request->primary_id);
        $mergeGuests = Guest::whereIn('id', $request->merge_ids)->get();

        foreach ($mergeGuests as $guest) {
            // Transfer bookings
            $guest->bookings()->update(['guest_id' => $primary->id]);

            // Transfer payments
            $guest->payments()->update(['guest_id' => $primary->id]);

            // Update statistics
            $primary->increment('total_stays', $guest->total_stays);
            $primary->increment('total_spent', $guest->total_spent);

            ActivityLog::log('merged', "Merged guest {$guest->full_name} into {$primary->full_name}", $primary);

            $guest->delete();
        }

        $primary->updateVipStatus();

        return back()->with('success', 'Guest profiles merged successfully.');
    }

    /**
     * Delete the guest.
     */
    public function destroy(Guest $guest)
    {
        if ($guest->bookings()->whereIn('status', ['confirmed', 'checked_in'])->exists()) {
            return back()->with('error', 'Cannot delete guest with active bookings.');
        }

        ActivityLog::log('deleted', "Deleted guest: {$guest->full_name}", $guest);

        $guest->delete();

        return redirect()->route('admin.guests.index')
            ->with('success', 'Guest profile deleted successfully.');
    }
}
