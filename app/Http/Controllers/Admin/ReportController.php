<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Payment;
use App\Models\Guest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Reports dashboard.
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Occupancy report.
     */
    public function occupancy(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $roomTypeId = $request->get('room_type_id');

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $totalRooms = Room::when($roomTypeId, fn($q) => $q->where('room_type_id', $roomTypeId))
            ->where('status', '!=', 'out_of_service')
            ->count();

        $days = [];
        $current = $start->copy();

        while ($current <= $end) {
            $date = $current->format('Y-m-d');

            $occupiedRooms = Booking::whereIn('status', ['confirmed', 'checked_in'])
                ->whereDate('check_in', '<=', $date)
                ->whereDate('check_out', '>', $date)
                ->when($roomTypeId, fn($q) => $q->where('room_type_id', $roomTypeId))
                ->count();

            $days[] = [
                'date' => $date,
                'day' => $current->format('D'),
                'occupied' => $occupiedRooms,
                'available' => max(0, $totalRooms - $occupiedRooms),
                'rate' => $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0,
            ];

            $current->addDay();
        }

        $averageOccupancy = count($days) > 0
            ? round(array_sum(array_column($days, 'rate')) / count($days), 1)
            : 0;

        $roomTypes = RoomType::all();

        return view('admin.reports.occupancy', compact(
            'days', 'startDate', 'endDate', 'roomTypeId', 'roomTypes',
            'totalRooms', 'averageOccupancy'
        ));
    }

    /**
     * Revenue report.
     */
    public function revenue(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $groupBy = $request->get('group_by', 'day');

        $query = Booking::whereBetween('check_in', [$startDate, $endDate])
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out']);

        // Summary stats
        $totalRevenue = (clone $query)->sum('total_amount');
        $totalBookings = (clone $query)->count();
        $averageBookingValue = $totalBookings > 0 ? $totalRevenue / $totalBookings : 0;

        // Revenue by room type
        $revenueByRoomType = (clone $query)
            ->select('room_type_id', DB::raw('SUM(total_amount) as revenue'), DB::raw('COUNT(*) as bookings'))
            ->groupBy('room_type_id')
            ->with('roomType')
            ->get();

        // Revenue by source
        $revenueBySource = (clone $query)
            ->select('source', DB::raw('SUM(total_amount) as revenue'), DB::raw('COUNT(*) as bookings'))
            ->groupBy('source')
            ->get();

        // Revenue trend
        $dateFormat = match($groupBy) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $revenueTrend = (clone $query)
            ->select(DB::raw("DATE_FORMAT(check_in, '{$dateFormat}') as period"),
                     DB::raw('SUM(total_amount) as revenue'),
                     DB::raw('COUNT(*) as bookings'))
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        // Payment stats
        $paymentStats = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();

        return view('admin.reports.revenue', compact(
            'startDate', 'endDate', 'groupBy', 'totalRevenue', 'totalBookings',
            'averageBookingValue', 'revenueByRoomType', 'revenueBySource',
            'revenueTrend', 'paymentStats'
        ));
    }

    /**
     * Booking analytics report.
     */
    public function bookings(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Booking counts by status
        $bookingsByStatus = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Booking sources
        $bookingsBySource = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->select('source', DB::raw('COUNT(*) as count'))
            ->groupBy('source')
            ->get();

        // Cancellation rate
        $totalBookings = Booking::whereBetween('created_at', [$startDate, $endDate])->count();
        $cancelledBookings = $bookingsByStatus['cancelled'] ?? 0;
        $cancellationRate = $totalBookings > 0 ? round(($cancelledBookings / $totalBookings) * 100, 1) : 0;

        // Average lead time (days between booking and check-in)
        $avgLeadTime = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->whereColumn('check_in', '>', 'created_at')
            ->select(DB::raw('AVG(DATEDIFF(check_in, created_at)) as avg_lead_time'))
            ->value('avg_lead_time');

        // Average length of stay
        $avgStayLength = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('AVG(total_nights) as avg_nights'))
            ->value('avg_nights');

        // Bookings by day of week
        $bookingsByDayOfWeek = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DAYOFWEEK(check_in) as day'), DB::raw('COUNT(*) as count'))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Daily booking trend
        $bookingTrend = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.reports.bookings', compact(
            'startDate', 'endDate', 'bookingsByStatus', 'bookingsBySource',
            'totalBookings', 'cancellationRate', 'avgLeadTime', 'avgStayLength',
            'bookingsByDayOfWeek', 'bookingTrend'
        ));
    }

    /**
     * Guest analytics report.
     */
    public function guests(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // Total guests
        $totalGuests = Guest::count();
        $newGuests = Guest::whereBetween('created_at', [$startDate, $endDate])->count();

        // VIP breakdown
        $guestsByVipStatus = Guest::select('vip_status', DB::raw('COUNT(*) as count'))
            ->groupBy('vip_status')
            ->get()
            ->pluck('count', 'vip_status');

        // Nationality breakdown
        $guestsByNationality = Guest::whereNotNull('nationality')
            ->select('nationality', DB::raw('COUNT(*) as count'))
            ->groupBy('nationality')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Top guests by revenue
        $topGuestsByRevenue = Guest::withSum(['bookings' => fn($q) => $q->whereIn('status', ['checked_out'])], 'total_amount')
            ->orderByDesc('bookings_sum_total_amount')
            ->limit(10)
            ->get();

        // Repeat guests
        $repeatGuests = Guest::where('total_stays', '>', 1)->count();
        $repeatGuestRate = $totalGuests > 0 ? round(($repeatGuests / $totalGuests) * 100, 1) : 0;

        // Average spend per guest
        $avgSpendPerGuest = Guest::where('total_spent', '>', 0)->avg('total_spent');

        return view('admin.reports.guests', compact(
            'startDate', 'endDate', 'totalGuests', 'newGuests', 'guestsByVipStatus',
            'guestsByNationality', 'topGuestsByRevenue', 'repeatGuests',
            'repeatGuestRate', 'avgSpendPerGuest'
        ));
    }

    /**
     * Room performance report.
     */
    public function rooms(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $totalDays = $start->diffInDays($end) + 1;

        // Room type performance
        $roomTypePerformance = RoomType::withCount(['rooms', 'bookings' => fn($q) =>
            $q->whereBetween('check_in', [$startDate, $endDate])
              ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
        ])
        ->withSum(['bookings' => fn($q) =>
            $q->whereBetween('check_in', [$startDate, $endDate])
              ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
        ], 'total_amount')
        ->get()
        ->map(function ($roomType) use ($totalDays) {
            $roomType->potential_room_nights = $roomType->rooms_count * $totalDays;
            $roomType->average_revenue = $roomType->bookings_count > 0
                ? $roomType->bookings_sum_total_amount / $roomType->bookings_count
                : 0;
            return $roomType;
        });

        // Individual room performance
        $roomPerformance = Room::withCount(['bookings' => fn($q) =>
            $q->whereBetween('check_in', [$startDate, $endDate])
              ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
        ])
        ->with('roomType')
        ->orderByDesc('bookings_count')
        ->limit(20)
        ->get();

        // Room maintenance days
        $maintenanceRooms = Room::where('status', 'maintenance')->count();

        return view('admin.reports.rooms', compact(
            'startDate', 'endDate', 'totalDays', 'roomTypePerformance',
            'roomPerformance', 'maintenanceRooms'
        ));
    }

    /**
     * Export report as CSV.
     */
    public function export(Request $request, string $report)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $data = match ($report) {
            'bookings' => $this->exportBookings($startDate, $endDate),
            'revenue' => $this->exportRevenue($startDate, $endDate),
            'guests' => $this->exportGuests($startDate, $endDate),
            default => [],
        };

        $filename = "{$report}_report_{$startDate}_{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            if (!empty($data)) {
                fputcsv($file, array_keys($data[0]));
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export bookings data.
     */
    protected function exportBookings(string $startDate, string $endDate): array
    {
        return Booking::whereBetween('check_in', [$startDate, $endDate])
            ->with(['guest', 'roomType'])
            ->get()
            ->map(fn($b) => [
                'Booking Reference' => $b->booking_reference,
                'Guest Name' => $b->guest?->full_name ?? $b->guest_name,
                'Room Type' => $b->roomType?->name ?? '-',
                'Check In' => $b->check_in->format('Y-m-d'),
                'Check Out' => $b->check_out->format('Y-m-d'),
                'Nights' => $b->total_nights,
                'Adults' => $b->adults,
                'Children' => $b->children,
                'Total Amount' => $b->total_amount,
                'Status' => $b->status,
                'Source' => $b->source,
                'Created At' => $b->created_at->format('Y-m-d H:i'),
            ])
            ->toArray();
    }

    /**
     * Export revenue data.
     */
    protected function exportRevenue(string $startDate, string $endDate): array
    {
        return Payment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->with(['booking', 'guest'])
            ->get()
            ->map(fn($p) => [
                'Payment Reference' => $p->payment_reference,
                'Booking Reference' => $p->booking?->booking_reference ?? '-',
                'Guest' => $p->guest?->full_name ?? '-',
                'Amount' => $p->amount,
                'Method' => $p->payment_method,
                'Status' => $p->status,
                'Date' => $p->created_at->format('Y-m-d H:i'),
            ])
            ->toArray();
    }

    /**
     * Export guests data.
     */
    protected function exportGuests(string $startDate, string $endDate): array
    {
        return Guest::whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(fn($g) => [
                'Name' => $g->full_name,
                'Email' => $g->email,
                'Phone' => $g->phone,
                'Nationality' => $g->nationality,
                'VIP Status' => $g->vip_status,
                'Total Stays' => $g->total_stays,
                'Total Spent' => $g->total_spent,
                'Created At' => $g->created_at->format('Y-m-d'),
            ])
            ->toArray();
    }
}
