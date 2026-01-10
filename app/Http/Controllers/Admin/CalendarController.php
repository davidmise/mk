<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Display the availability calendar.
     */
    public function index(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);
        $roomTypeId = $request->get('room_type_id');

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $roomTypes = RoomType::withCount('rooms')->get();

        // Get rooms
        $roomsQuery = Room::with('roomType')
            ->where('status', '!=', 'out_of_service');

        if ($roomTypeId) {
            $roomsQuery->where('room_type_id', $roomTypeId);
        }

        $rooms = $roomsQuery->orderBy('room_number')->get();

        // Get bookings for the month
        $bookings = Booking::whereIn('status', ['confirmed', 'checked_in'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('check_in', [$startDate, $endDate])
                  ->orWhereBetween('check_out', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('check_in', '<=', $startDate)
                         ->where('check_out', '>=', $endDate);
                  });
            })
            ->when($roomTypeId, fn($q) => $q->where('room_type_id', $roomTypeId))
            ->with(['roomType', 'guest', 'room'])
            ->get();

        // Build calendar data
        $calendarData = $this->buildCalendarData($rooms, $bookings, $startDate, $endDate);

        // Generate dates array for the view
        $dates = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dates[] = $current->copy();
            $current->addDay();
        }

        $prevMonth = $startDate->copy()->subMonth();
        $nextMonth = $startDate->copy()->addMonth();
        $currentMonth = $startDate->copy();

        return view('admin.calendar.index', compact(
            'calendarData', 'rooms', 'roomTypes', 'bookings',
            'startDate', 'endDate', 'month', 'year', 'roomTypeId',
            'prevMonth', 'nextMonth', 'currentMonth', 'dates'
        ));
    }

    /**
     * Build calendar data for the room grid.
     */
    protected function buildCalendarData($rooms, $bookings, $startDate, $endDate): array
    {
        $calendar = [];
        $current = $startDate->copy();
        $daysInMonth = [];

        while ($current <= $endDate) {
            $daysInMonth[] = $current->copy();
            $current->addDay();
        }

        foreach ($rooms as $room) {
            $roomData = [
                'room' => $room,
                'days' => [],
            ];

            foreach ($daysInMonth as $day) {
                $dateStr = $day->format('Y-m-d');

                // Find booking for this room on this day
                $booking = $bookings->first(function ($b) use ($room, $day) {
                    if ($b->room_id !== $room->id && $b->room_type_id !== $room->room_type_id) {
                        return false;
                    }

                    return $day->between($b->check_in, $b->check_out->copy()->subDay());
                });

                $status = 'available';
                $bookingInfo = null;

                if ($room->status === 'maintenance') {
                    $status = 'maintenance';
                } elseif ($room->status === 'cleaning') {
                    $status = 'cleaning';
                } elseif ($booking) {
                    $status = $booking->status === 'checked_in' ? 'occupied' : 'reserved';
                    $bookingInfo = [
                        'id' => $booking->id,
                        'reference' => $booking->booking_reference,
                        'guest' => $booking->guest?->full_name ?? $booking->guest_name,
                        'check_in' => $booking->check_in->format('Y-m-d'),
                        'check_out' => $booking->check_out->format('Y-m-d'),
                        'is_start' => $day->isSameDay($booking->check_in),
                        'is_end' => $day->isSameDay($booking->check_out->copy()->subDay()),
                    ];
                }

                $roomData['days'][$dateStr] = [
                    'date' => $dateStr,
                    'day' => $day->day,
                    'dayOfWeek' => $day->dayOfWeek,
                    'isWeekend' => $day->isWeekend(),
                    'isToday' => $day->isToday(),
                    'status' => $status,
                    'booking' => $bookingInfo,
                ];
            }

            $calendar[] = $roomData;
        }

        return $calendar;
    }

    /**
     * Get availability data for a specific date range.
     */
    public function availability(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'room_type_id' => 'nullable|exists:room_types,id',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $roomTypeId = $request->room_type_id;

        $roomTypes = RoomType::withCount('rooms')
            ->when($roomTypeId, fn($q) => $q->where('id', $roomTypeId))
            ->get();

        $availability = [];

        foreach ($roomTypes as $roomType) {
            $totalRooms = $roomType->rooms_count;

            // Get bookings for this room type
            $bookings = Booking::where('room_type_id', $roomType->id)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->where('check_in', '<', $endDate)
                ->where('check_out', '>', $startDate)
                ->get();

            $current = $startDate->copy();
            $dailyAvailability = [];

            while ($current < $endDate) {
                $date = $current->format('Y-m-d');

                $bookedRooms = $bookings->filter(fn($b) =>
                    $current->between($b->check_in, $b->check_out->copy()->subDay())
                )->count();

                $dailyAvailability[$date] = [
                    'total' => $totalRooms,
                    'booked' => $bookedRooms,
                    'available' => max(0, $totalRooms - $bookedRooms),
                ];

                $current->addDay();
            }

            $availability[$roomType->id] = [
                'room_type' => $roomType,
                'daily' => $dailyAvailability,
            ];
        }

        if ($request->ajax()) {
            return response()->json($availability);
        }

        return view('admin.calendar.availability', compact('availability', 'startDate', 'endDate', 'roomTypes'));
    }

    /**
     * Quick booking from calendar.
     */
    public function quickBook(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $room = Room::with('roomType')->findOrFail($request->room_id);

        // Check availability
        $isAvailable = !Booking::where('room_id', $room->id)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where('check_in', '<', $request->check_out)
            ->where('check_out', '>', $request->check_in)
            ->exists();

        if (!$isAvailable) {
            return response()->json([
                'success' => false,
                'message' => 'Room is not available for selected dates.',
            ], 422);
        }

        // Return booking form data
        return response()->json([
            'success' => true,
            'room' => $room,
            'room_type' => $room->roomType,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'nights' => Carbon::parse($request->check_in)->diffInDays($request->check_out),
            'base_price' => $room->roomType->base_price_per_night ?? $room->roomType->price,
            'redirect_url' => route('admin.bookings.create', [
                'room_id' => $room->id,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
            ]),
        ]);
    }

    /**
     * Get bookings for a specific room.
     */
    public function roomBookings(Room $room, Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $bookings = Booking::where('room_id', $room->id)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('check_in', [$startDate, $endDate])
                  ->orWhereBetween('check_out', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('check_in', '<=', $startDate)
                         ->where('check_out', '>=', $endDate);
                  });
            })
            ->with(['guest', 'roomType'])
            ->orderBy('check_in')
            ->get();

        return response()->json([
            'room' => $room,
            'bookings' => $bookings,
        ]);
    }

    /**
     * Bulk update room status.
     */
    public function bulkStatusUpdate(Request $request)
    {
        $request->validate([
            'room_ids' => 'required|array',
            'room_ids.*' => 'exists:rooms,id',
            'status' => 'required|in:available,maintenance,cleaning,out_of_service',
        ]);

        Room::whereIn('id', $request->room_ids)->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Room statuses updated successfully.',
        ]);
    }

    /**
     * Today's arrivals and departures widget.
     */
    public function todaysActivity()
    {
        $today = Carbon::today();

        $arrivals = Booking::whereDate('check_in', $today)
            ->whereIn('status', ['confirmed', 'pending'])
            ->with(['guest', 'roomType', 'room'])
            ->get();

        $departures = Booking::whereDate('check_out', $today)
            ->where('status', 'checked_in')
            ->with(['guest', 'roomType', 'room'])
            ->get();

        $inHouse = Booking::where('status', 'checked_in')
            ->whereDate('check_in', '<=', $today)
            ->whereDate('check_out', '>', $today)
            ->count();

        return response()->json([
            'arrivals' => $arrivals,
            'departures' => $departures,
            'in_house_count' => $inHouse,
            'arrivals_count' => $arrivals->count(),
            'departures_count' => $departures->count(),
        ]);
    }
}
