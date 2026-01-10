@extends('admin.layouts.app')

@section('title', 'Reports')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>Reports</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Reports & Analytics</h1>
        <p class="page-subtitle">View comprehensive hotel performance metrics</p>
    </div>
</div>

<!-- Report Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
    <!-- Occupancy Report -->
    <a href="{{ route('admin.reports.occupancy') }}" class="card" style="text-decoration: none; transition: all 0.2s;">
        <div class="card-body">
            <div class="d-flex align-center gap-3">
                <div class="stat-icon blue" style="width: 56px; height: 56px;">
                    <i class="fas fa-bed"></i>
                </div>
                <div>
                    <h4 style="color: var(--gray-900); margin-bottom: 0.25rem;">Occupancy Report</h4>
                    <p class="text-muted mb-0" style="font-size: 0.875rem;">Daily, weekly, and monthly occupancy rates</p>
                </div>
            </div>
        </div>
    </a>

    <!-- Revenue Report -->
    <a href="{{ route('admin.reports.revenue') }}" class="card" style="text-decoration: none; transition: all 0.2s;">
        <div class="card-body">
            <div class="d-flex align-center gap-3">
                <div class="stat-icon green" style="width: 56px; height: 56px;">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div>
                    <h4 style="color: var(--gray-900); margin-bottom: 0.25rem;">Revenue Report</h4>
                    <p class="text-muted mb-0" style="font-size: 0.875rem;">Income analysis by room type, source, and period</p>
                </div>
            </div>
        </div>
    </a>

    <!-- Booking Analytics -->
    <a href="{{ route('admin.reports.bookings') }}" class="card" style="text-decoration: none; transition: all 0.2s;">
        <div class="card-body">
            <div class="d-flex align-center gap-3">
                <div class="stat-icon orange" style="width: 56px; height: 56px;">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <h4 style="color: var(--gray-900); margin-bottom: 0.25rem;">Booking Analytics</h4>
                    <p class="text-muted mb-0" style="font-size: 0.875rem;">Booking trends, sources, and cancellation rates</p>
                </div>
            </div>
        </div>
    </a>

    <!-- Guest Analytics -->
    <a href="{{ route('admin.reports.guests') }}" class="card" style="text-decoration: none; transition: all 0.2s;">
        <div class="card-body">
            <div class="d-flex align-center gap-3">
                <div class="stat-icon cyan" style="width: 56px; height: 56px;">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h4 style="color: var(--gray-900); margin-bottom: 0.25rem;">Guest Analytics</h4>
                    <p class="text-muted mb-0" style="font-size: 0.875rem;">Guest demographics, repeat visitors, VIP stats</p>
                </div>
            </div>
        </div>
    </a>

    <!-- Room Performance -->
    <a href="{{ route('admin.reports.rooms') }}" class="card" style="text-decoration: none; transition: all 0.2s;">
        <div class="card-body">
            <div class="d-flex align-center gap-3">
                <div class="stat-icon red" style="width: 56px; height: 56px;">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div>
                    <h4 style="color: var(--gray-900); margin-bottom: 0.25rem;">Room Performance</h4>
                    <p class="text-muted mb-0" style="font-size: 0.875rem;">Room type popularity and revenue per room</p>
                </div>
            </div>
        </div>
    </a>
</div>

<!-- Quick Stats -->
<div class="row mt-4">
    <div class="col-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">This Month Overview</h3>
            </div>
            <div class="card-body">
                <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">
                    <div class="stat-card" style="border: none; padding: 0;">
                        <div class="stat-content">
                            <div class="stat-label">Total Bookings</div>
                            <div class="stat-value">{{ $monthlyStats['total_bookings'] ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="stat-card" style="border: none; padding: 0;">
                        <div class="stat-content">
                            <div class="stat-label">Revenue</div>
                            <div class="stat-value">${{ number_format($monthlyStats['revenue'] ?? 0, 0) }}</div>
                        </div>
                    </div>
                    <div class="stat-card" style="border: none; padding: 0;">
                        <div class="stat-content">
                            <div class="stat-label">Avg. Occupancy</div>
                            <div class="stat-value">{{ $monthlyStats['avg_occupancy'] ?? 0 }}%</div>
                        </div>
                    </div>
                    <div class="stat-card" style="border: none; padding: 0;">
                        <div class="stat-content">
                            <div class="stat-label">New Guests</div>
                            <div class="stat-value">{{ $monthlyStats['new_guests'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Export</h3>
            </div>
            <div class="card-body">
                <p class="text-muted" style="font-size: 0.875rem;">Export data for the current month:</p>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('admin.reports.export', ['report' => 'bookings']) }}?start_date={{ now()->startOfMonth()->format('Y-m-d') }}&end_date={{ now()->endOfMonth()->format('Y-m-d') }}" class="btn btn-secondary">
                        <i class="fas fa-download"></i> Export Bookings (CSV)
                    </a>
                    <a href="{{ route('admin.reports.export', ['report' => 'revenue']) }}?start_date={{ now()->startOfMonth()->format('Y-m-d') }}&end_date={{ now()->endOfMonth()->format('Y-m-d') }}" class="btn btn-secondary">
                        <i class="fas fa-download"></i> Export Revenue (CSV)
                    </a>
                    <a href="{{ route('admin.reports.export', ['report' => 'guests']) }}?start_date={{ now()->startOfMonth()->format('Y-m-d') }}&end_date={{ now()->endOfMonth()->format('Y-m-d') }}" class="btn btn-secondary">
                        <i class="fas fa-download"></i> Export Guests (CSV)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
</style>
@endsection
