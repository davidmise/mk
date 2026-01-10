<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Payment;
use App\Models\ActivityLog;
use App\Models\SystemPreference;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings.
     */
    public function index(Request $request)
    {
        $query = Booking::with(['roomType', 'room', 'guest']);

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        // Filter by source
        if ($request->filled('source')) {
            $query->source($request->source);
        }

        // Filter by date range
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->dateRange($request->date_from, $request->date_to);
        }

        // Filter by room type
        if ($request->filled('room_type')) {
            $query->where('room_type_id', $request->room_type);
        }

        $bookings = $query->latest()->paginate(20)->withQueryString();
        $roomTypes = RoomType::active()->get();
        $sources = Booking::getSources();

        return view('admin.bookings.index', compact('bookings', 'roomTypes', 'sources'));
    }

    /**
     * Show the form for creating a new booking (Walk-in / Manual).
     */
    public function create(Request $request)
    {
        $roomTypes = RoomType::active()->ordered()->get();
        $sources = Booking::getSources();
        $guests = Guest::orderBy('first_name')->get();

        // Pre-fill dates if provided
        $checkIn = $request->check_in ?? now()->format('Y-m-d');
        $checkOut = $request->check_out ?? now()->addDay()->format('Y-m-d');

        return view('admin.bookings.create', compact('roomTypes', 'sources', 'guests', 'checkIn', 'checkOut'));
    }

    /**
     * Store a newly created booking.
     */
    public function store(Request $request)
    {
        $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'nullable|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'room_type_id' => 'required|exists:room_types,id',
            'room_id' => 'nullable|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'number_of_rooms' => 'required|integer|min:1',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'source' => 'required|in:' . implode(',', array_keys(Booking::getSources())),
            'external_reference' => 'nullable|string|max:255',
            'room_rate' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_reason' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string',
            'amount_paid' => 'nullable|numeric|min:0',
            'special_requests' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check availability
        $availability = Booking::checkAvailability(
            $request->room_type_id,
            $request->check_in,
            $request->check_out
        );

        if (!$availability['available'] || $availability['rooms_available'] < $request->number_of_rooms) {
            return back()->withInput()->with('error', 'Not enough rooms available for the selected dates.');
        }

        DB::beginTransaction();
        try {
            // Find or create guest
            $guest = Guest::findOrCreateFromBooking([
                'name' => $request->guest_name,
                'email' => $request->guest_email,
                'phone' => $request->guest_phone,
            ]);

            // Create booking
            $booking = new Booking([
                'guest_id' => $guest->id,
                'guest_name' => $request->guest_name,
                'guest_email' => $request->guest_email,
                'guest_phone' => $request->guest_phone,
                'room_type_id' => $request->room_type_id,
                'room_id' => $request->room_id,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'number_of_rooms' => $request->number_of_rooms,
                'adults' => $request->adults,
                'children' => $request->children ?? 0,
                'source' => $request->source,
                'external_reference' => $request->external_reference,
                'room_rate' => $request->room_rate,
                'discount_amount' => $request->discount_amount ?? 0,
                'discount_reason' => $request->discount_reason,
                'special_requests' => $request->special_requests,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'status' => $request->source === 'walk_in' ? Booking::STATUS_CONFIRMED : Booking::STATUS_PENDING,
            ]);

            // Calculate pricing
            $booking->calculatePricing();
            $booking->save();

            // Process initial payment if provided
            if ($request->filled('amount_paid') && $request->amount_paid > 0) {
                $payment = Payment::create([
                    'booking_id' => $booking->id,
                    'guest_id' => $guest->id,
                    'amount' => $request->amount_paid,
                    'method' => $request->payment_method ?? 'cash',
                    'type' => $request->amount_paid >= $booking->total_amount ? 'full_payment' : 'deposit',
                    'status' => Payment::STATUS_COMPLETED,
                    'processed_by' => auth()->id(),
                    'processed_at' => now(),
                ]);

                $booking->recordPayment($request->amount_paid);
            }

            // If walk-in and room assigned, mark room as occupied
            if ($request->source === 'walk_in' && $request->room_id) {
                Room::find($request->room_id)?->update(['status' => Room::STATUS_OCCUPIED]);
                $booking->update([
                    'status' => Booking::STATUS_CHECKED_IN,
                    'checked_in_at' => now(),
                    'checked_in_by' => auth()->id(),
                    'confirmed_at' => now(),
                    'confirmed_by' => auth()->id(),
                ]);
            }

            DB::commit();

            ActivityLog::log('created', "Created booking: {$booking->booking_reference}", $booking);

            return redirect()->route('admin.bookings.show', $booking)
                ->with('success', "Reservation {$booking->booking_reference} created successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create booking: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        $booking->load([
            'roomType',
            'room',
            'guest',
            'payments.processedBy',
            'creator',
            'confirmedByUser',
            'checkedInByUser',
            'checkedOutByUser',
        ]);

        // Get available rooms for assignment
        $availableRooms = Room::active()
            ->available()
            ->ofType($booking->room_type_id)
            ->get();

        return view('admin.bookings.show', compact('booking', 'availableRooms'));
    }

    /**
     * Show the form for editing the booking.
     */
    public function edit(Booking $booking)
    {
        if (in_array($booking->status, [Booking::STATUS_CHECKED_OUT, Booking::STATUS_CANCELLED])) {
            return back()->with('error', 'Cannot edit a completed or cancelled booking.');
        }

        $roomTypes = RoomType::active()->ordered()->get();
        $sources = Booking::getSources();
        $availableRooms = Room::active()->ofType($booking->room_type_id)->get();

        return view('admin.bookings.edit', compact('booking', 'roomTypes', 'sources', 'availableRooms'));
    }

    /**
     * Update the specified booking.
     */
    public function update(Request $request, Booking $booking)
    {
        if (in_array($booking->status, [Booking::STATUS_CHECKED_OUT, Booking::STATUS_CANCELLED])) {
            return back()->with('error', 'Cannot update a completed or cancelled booking.');
        }

        $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'nullable|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'room_type_id' => 'required|exists:room_types,id',
            'room_id' => 'nullable|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'number_of_rooms' => 'required|integer|min:1',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'room_rate' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_reason' => 'nullable|string|max:255',
            'special_requests' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check availability if dates or room type changed
        if ($booking->room_type_id != $request->room_type_id ||
            $booking->check_in->format('Y-m-d') != $request->check_in ||
            $booking->check_out->format('Y-m-d') != $request->check_out ||
            $booking->number_of_rooms != $request->number_of_rooms) {

            $availability = Booking::checkAvailability(
                $request->room_type_id,
                $request->check_in,
                $request->check_out,
                $booking->id
            );

            if (!$availability['available'] || $availability['rooms_available'] < $request->number_of_rooms) {
                return back()->withInput()->with('error', 'Not enough rooms available for the selected dates.');
            }
        }

        $oldValues = $booking->toArray();

        $booking->update([
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
            'guest_phone' => $request->guest_phone,
            'room_type_id' => $request->room_type_id,
            'room_id' => $request->room_id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'number_of_rooms' => $request->number_of_rooms,
            'adults' => $request->adults,
            'children' => $request->children ?? 0,
            'room_rate' => $request->room_rate,
            'discount_amount' => $request->discount_amount ?? 0,
            'discount_reason' => $request->discount_reason,
            'special_requests' => $request->special_requests,
            'notes' => $request->notes,
        ]);

        // Recalculate pricing
        $booking->calculatePricing();
        $booking->save();

        ActivityLog::log('updated', "Updated booking: {$booking->booking_reference}", $booking, $oldValues);

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Reservation updated successfully.');
    }

    /**
     * Confirm a booking.
     */
    public function confirm(Booking $booking)
    {
        if ($booking->status !== Booking::STATUS_PENDING) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Only pending bookings can be confirmed.']);
            }
            return back()->with('error', 'Only pending bookings can be confirmed.');
        }

        $booking->confirm();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Reservation has been confirmed!']);
        }
        return back()->with('success', 'Reservation has been confirmed.');
    }

    /**
     * Check-in a guest.
     */
    public function checkIn(Request $request, Booking $booking)
    {
        if (!in_array($booking->status, [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Cannot check in this booking.']);
            }
            return back()->with('error', 'Cannot check in this booking.');
        }

        // If room_id provided, use it; otherwise use existing room
        $roomId = $request->room_id ?? $booking->room_id;

        if ($roomId) {
            $room = Room::find($roomId);
            if ($room && $room->status !== Room::STATUS_AVAILABLE && $room->id !== $booking->room_id) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Selected room is not available.']);
                }
                return back()->with('error', 'Selected room is not available.');
            }
        }

        // Confirm booking first if pending
        if ($booking->status === Booking::STATUS_PENDING) {
            $booking->update([
                'confirmed_at' => now(),
                'confirmed_by' => auth()->id(),
            ]);
        }

        $booking->checkIn($roomId);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Guest has been checked in successfully!']);
        }
        return back()->with('success', 'Guest has been checked in successfully.');
    }

    /**
     * Check-out a guest.
     */
    public function checkOut(Booking $booking)
    {
        if ($booking->status !== Booking::STATUS_CHECKED_IN) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Only in-house guests can be checked out.']);
            }
            return back()->with('error', 'Only in-house guests can be checked out.');
        }

        // Check if there's balance due
        if ($booking->balance_due > 0) {
            $message = "Cannot check out. Outstanding balance: TZS " . number_format($booking->balance_due, 0);
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $message]);
            }
            return back()->with('error', $message);
        }

        $booking->checkOut();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Guest has been checked out successfully!']);
        }
        return back()->with('success', 'Guest has been checked out successfully.');
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Request $request, Booking $booking)
    {
        if (in_array($booking->status, [Booking::STATUS_CHECKED_OUT, Booking::STATUS_CANCELLED])) {
            return back()->with('error', 'Cannot cancel this booking.');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $booking->cancel($request->cancellation_reason);

        return back()->with('success', 'Reservation has been cancelled.');
    }

    /**
     * Mark as no-show.
     */
    public function noShow(Booking $booking)
    {
        if (!in_array($booking->status, [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])) {
            return back()->with('error', 'Cannot mark this booking as no-show.');
        }

        $booking->markAsNoShow();

        return back()->with('success', 'Booking has been marked as no-show.');
    }

    /**
     * Record a payment for the booking.
     */
    public function recordPayment(Request $request, Booking $booking)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,card,bank_transfer,mobile_money,ota_prepaid,corporate_billing',
            'notes' => 'nullable|string|max:500',
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'guest_id' => $booking->guest_id,
            'amount' => $request->amount,
            'method' => $request->method,
            'type' => $request->amount >= $booking->balance_due ? 'full_payment' : 'partial',
            'status' => Payment::STATUS_COMPLETED,
            'notes' => $request->notes,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        $booking->recordPayment($request->amount);

        ActivityLog::log('payment_received', "Payment of {$request->amount} received for booking {$booking->booking_reference}", $payment);

        return back()->with('success', 'Payment has been recorded.');
    }

    /**
     * Assign a room to the booking.
     */
    public function assignRoom(Request $request, Booking $booking)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);

        $room = Room::find($request->room_id);

        if ($room->status !== Room::STATUS_AVAILABLE) {
            return back()->with('error', 'Selected room is not available.');
        }

        $booking->update(['room_id' => $request->room_id]);

        ActivityLog::log('room_assigned', "Room {$room->room_number} assigned to booking {$booking->booking_reference}", $booking);

        return back()->with('success', "Room {$room->room_number} has been assigned.");
    }

    /**
     * Check room availability (AJAX).
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'exclude_booking' => 'nullable|exists:bookings,id',
        ]);

        $availability = Booking::checkAvailability(
            $request->room_type_id,
            $request->check_in,
            $request->check_out,
            $request->exclude_booking
        );

        // Get room type price
        $roomType = RoomType::find($request->room_type_id);
        $nights = Carbon::parse($request->check_in)->diffInDays(Carbon::parse($request->check_out));

        $availability['room_type'] = $roomType->name;
        $availability['price_per_night'] = $roomType->price;
        $availability['nights'] = $nights;
        $availability['estimated_total'] = $roomType->price * $nights;

        return response()->json($availability);
    }

    /**
     * Delete a booking.
     */
    public function destroy(Booking $booking)
    {
        if (!in_array($booking->status, [Booking::STATUS_PENDING, Booking::STATUS_CANCELLED])) {
            return back()->with('error', 'Only pending or cancelled bookings can be deleted.');
        }

        ActivityLog::log('deleted', "Deleted booking: {$booking->booking_reference}", $booking);

        $booking->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking has been deleted.');
    }
}
