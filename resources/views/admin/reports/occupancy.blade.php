@extends('admin.layouts.app')

@section('title', 'Occupancy Report')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <a href="{{ route('admin.reports.index') }}">Reports</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>Occupancy Report</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Occupancy Report</h1>
        <p class="page-subtitle">Daily, weekly, and monthly occupancy rates analysis</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.reports.export', ['report' => 'occupancy']) }}?start_date={{ $startDate }}&end_date={{ $endDate }}" class="btn btn-secondary">
            <i class="fas fa-download"></i> Export CSV
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.reports.occupancy') }}" method="GET" class="d-flex align-center gap-3 flex-wrap">
            <div class="form-group mb-0" style="min-width: 150px;">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="form-group mb-0" style="min-width: 150px;">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="form-group mb-0" style="min-width: 150px;">
                <label class="form-label">Room Type</label>
                <select name="room_type_id" class="form-control">
                    <option value="">All Room Types</option>
                    @foreach($roomTypes ?? [] as $type)
                        <option value="{{ $type->id }}" {{ request('room_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-0" style="align-self: flex-end;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Stats -->
<div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 1.5rem;">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-percentage"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ number_format($summary['average_occupancy'] ?? 0, 1) }}%</div>
            <div class="stat-label">Average Occupancy</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ number_format($summary['peak_occupancy'] ?? 0, 1) }}%</div>
            <div class="stat-label">Peak Occupancy</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-chart-line fa-flip-vertical"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ number_format($summary['lowest_occupancy'] ?? 0, 1) }}%</div>
            <div class="stat-label">Lowest Occupancy</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon cyan">
            <i class="fas fa-bed"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $summary['total_room_nights'] ?? 0 }}</div>
            <div class="stat-label">Total Room Nights</div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <div class="col-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daily Occupancy Rate</h3>
            </div>
            <div class="card-body">
                <canvas id="dailyOccupancyChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">By Room Type</h3>
            </div>
            <div class="card-body">
                <canvas id="roomTypeOccupancyChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Daily Data Table -->
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Daily Breakdown</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Day</th>
                        <th>Rooms Occupied</th>
                        <th>Total Rooms</th>
                        <th>Occupancy Rate</th>
                        <th>Revenue</th>
                        <th>RevPAR</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dailyData ?? [] as $day)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($day['date'])->format('M d, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($day['date'])->format('l') }}</td>
                        <td>{{ $day['occupied_rooms'] }}</td>
                        <td>{{ $day['total_rooms'] }}</td>
                        <td>
                            <div class="d-flex align-center gap-2">
                                <div class="progress" style="width: 80px; height: 8px;">
                                    <div class="progress-bar {{ $day['occupancy_rate'] >= 80 ? 'bg-success' : ($day['occupancy_rate'] >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                         style="width: {{ $day['occupancy_rate'] }}%"></div>
                                </div>
                                <span>{{ number_format($day['occupancy_rate'], 1) }}%</span>
                            </div>
                        </td>
                        <td>${{ number_format($day['revenue'], 2) }}</td>
                        <td>${{ number_format($day['revpar'], 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No occupancy data available for the selected period</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Daily Occupancy Chart
    const dailyData = @json($dailyData ?? []);
    const dailyCtx = document.getElementById('dailyOccupancyChart').getContext('2d');
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: dailyData.map(d => d.date),
            datasets: [{
                label: 'Occupancy Rate (%)',
                data: dailyData.map(d => d.occupancy_rate),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });

    // Room Type Occupancy Chart
    const roomTypeData = @json($roomTypeData ?? []);
    const roomTypeCtx = document.getElementById('roomTypeOccupancyChart').getContext('2d');
    new Chart(roomTypeCtx, {
        type: 'bar',
        data: {
            labels: roomTypeData.map(r => r.name),
            datasets: [{
                label: 'Occupancy Rate (%)',
                data: roomTypeData.map(r => r.occupancy_rate),
                backgroundColor: [
                    '#3b82f6',
                    '#10b981',
                    '#f59e0b',
                    '#06b6d4',
                    '#8b5cf6'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
