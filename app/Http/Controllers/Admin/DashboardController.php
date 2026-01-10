<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        try {
            $today = Carbon::today();

            // Today's Operations
            $todayCheckIns = Booking::todayCheckIns()->count();
            $todayCheckOuts = Booking::todayCheckOuts()->count();
            $inHouseGuests = Booking::inHouse()->count();

            // Room statistics
            $totalRooms = Room::active()->count();
            $availableRooms = Room::active()->available()->count();
            $occupiedRooms = Room::active()->status('occupied')->count();
            $maintenanceRooms = Room::active()->status('maintenance')->count();

            // Calculate occupancy rate
            $occupancyRate = $totalRooms > 0
                ? round(($occupiedRooms / $totalRooms) * 100, 1)
                : 0;

            // Booking statistics
            $pendingBookings = Booking::pending()->count();
            $confirmedBookings = Booking::confirmed()->count();

            // Revenue - Today
            $todayRevenue = Payment::completed()
                ->whereDate('created_at', $today)
                ->sum('amount');

            // Revenue - This Month
            $monthlyRevenue = Payment::completed()
                ->whereMonth('created_at', $today->month)
                ->whereYear('created_at', $today->year)
                ->sum('amount');

            // Bookings by source (this month)
            $bookingsBySource = Booking::select('source', DB::raw('count(*) as count'))
                ->whereMonth('created_at', $today->month)
                ->groupBy('source')
                ->pluck('count', 'source')
                ->toArray();

            // Today's arrivals
            $todayArrivals = Booking::with(['roomType', 'room'])
                ->todayCheckIns()
                ->orderBy('check_in')
                ->take(10)
                ->get();

            // Today's departures
            $todayDepartures = Booking::with(['roomType', 'room'])
                ->todayCheckOuts()
                ->orderBy('check_out')
                ->take(10)
                ->get();

            // In-house guests
            $inHouseBookings = Booking::with(['roomType', 'room', 'guest'])
                ->inHouse()
                ->orderBy('check_out')
                ->take(10)
                ->get();

            // Recent bookings
            $recentBookings = Booking::with('roomType')
                ->latest()
                ->take(5)
                ->get();

            // Recent activity
            $recentActivity = ActivityLog::with('user')
                ->latest()
                ->take(10)
                ->get();

            // Revenue chart data (last 7 days)
            $revenueChartData = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = $today->copy()->subDays($i);
                $revenue = Payment::completed()
                    ->whereDate('created_at', $date)
                    ->sum('amount');

                $revenueChartData[] = [
                    'date' => $date->format('M d'),
                    'revenue' => $revenue,
                ];
            }

            // Occupancy chart data (last 7 days)
            $occupancyChartData = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = $today->copy()->subDays($i);
                $occupied = Booking::whereDate('check_in', '<=', $date)
                    ->whereDate('check_out', '>', $date)
                    ->whereIn('status', ['confirmed', 'checked_in'])
                    ->sum('number_of_rooms');

                $rate = $totalRooms > 0 ? round(($occupied / $totalRooms) * 100, 1) : 0;

                $occupancyChartData[] = [
                    'date' => $date->format('M d'),
                    'occupancy' => $rate,
                ];
            }

            return view('admin.dashboard.index', compact(
                'todayCheckIns',
                'todayCheckOuts',
                'inHouseGuests',
                'totalRooms',
                'availableRooms',
                'occupiedRooms',
                'maintenanceRooms',
                'occupancyRate',
                'pendingBookings',
                'confirmedBookings',
                'todayRevenue',
                'monthlyRevenue',
                'bookingsBySource',
                'todayArrivals',
                'todayDepartures',
                'inHouseBookings',
                'recentBookings',
                'recentActivity',
                'revenueChartData',
                'occupancyChartData'
            ));
        } catch (\Exception $e) {
            // Log the error and show a friendly message
            \Log::error('Dashboard error: ' . $e->getMessage());

            // Return view with default/empty data
            return view('admin.dashboard.index', [
                'todayCheckIns' => 0,
                'todayCheckOuts' => 0,
                'inHouseGuests' => 0,
                'totalRooms' => 0,
                'availableRooms' => 0,
                'occupiedRooms' => 0,
                'maintenanceRooms' => 0,
                'occupancyRate' => 0,
                'pendingBookings' => 0,
                'confirmedBookings' => 0,
                'todayRevenue' => 0,
                'monthlyRevenue' => 0,
                'bookingsBySource' => [],
                'todayArrivals' => collect(),
                'todayDepartures' => collect(),
                'inHouseBookings' => collect(),
                'recentBookings' => collect(),
                'recentActivity' => collect(),
                'revenueChartData' => [],
                'occupancyChartData' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get dashboard stats (AJAX).
     */
    public function getStats()
    {
        $today = Carbon::today();

        return response()->json([
            'today_check_ins' => Booking::todayCheckIns()->count(),
            'today_check_outs' => Booking::todayCheckOuts()->count(),
            'in_house' => Booking::inHouse()->count(),
            'available_rooms' => Room::active()->available()->count(),
            'pending_bookings' => Booking::pending()->count(),
            'today_revenue' => Payment::completed()->whereDate('created_at', $today)->sum('amount'),
        ]);
    }

    /**
     * Get revenue chart data.
     */
    public function revenueChart(Request $request)
    {
        $days = $request->get('days', 7);
        $today = Carbon::today();
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $revenue = Payment::completed()
                ->whereDate('created_at', $date)
                ->sum('amount');

            $data[] = [
                'date' => $date->format('M d'),
                'revenue' => $revenue,
            ];
        }

        return response()->json($data);
    }

    /**
     * Get booking chart data.
     */
    public function bookingChart(Request $request)
    {
        $days = $request->get('days', 7);
        $today = Carbon::today();
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $bookings = Booking::whereDate('created_at', $date)->count();

            $data[] = [
                'date' => $date->format('M d'),
                'bookings' => $bookings,
            ];
        }

        return response()->json($data);
    }

    /**
     * Get recent activity.
     */
    public function recentActivity()
    {
        $activities = ActivityLog::with('user')
            ->latest()
            ->take(20)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'user' => $activity->user?->name ?? 'System',
                    'action' => $activity->action,
                    'description' => $activity->description,
                    'created_at' => $activity->created_at->diffForHumans(),
                ];
            });

        return response()->json($activities);
    }

    /**
     * Alias for stats method.
     */
    public function stats()
    {
        return $this->getStats();
    }
}
