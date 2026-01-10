<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\RoomType;
use App\Http\Requests\BookingFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function store(BookingFormRequest $request)
    {
        $roomType = RoomType::findOrFail($request->room_type_id);

        $booking = new Booking($request->validated());
        $booking->booking_reference = $this->generateBookingReference();
        $booking->source = 'website';
        $booking->status = 'pending';
        $booking->payment_status = 'pending';

        // Calculate total price
        $checkIn = \Carbon\Carbon::parse($request->check_in);
        $checkOut = \Carbon\Carbon::parse($request->check_out);
        $nights = $checkIn->diffInDays($checkOut);
        $booking->total_price = $roomType->price * $nights * $request->number_of_rooms;

        $booking->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Booking successful!',
                'booking_id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
            ]);
        }

        return redirect()->back()->with('success', 'Booking successful! Your reference is: ' . $booking->booking_reference);
    }

    protected function generateBookingReference(): string
    {
        $prefix = 'MKH';
        $date = date('ymd');
        $random = strtoupper(Str::random(4));
        return $prefix . $date . $random;
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $roomType = RoomType::findOrFail($request->room_type_id);
        $availableRooms = $roomType->getAvailableRooms($request->check_in, $request->check_out);

        return response()->json([
            'available_rooms' => $availableRooms,
            'total_rooms' => $roomType->total_rooms,
            'room_type' => $roomType->name,
            'price_per_night' => $roomType->price,
        ]);
    }
}
