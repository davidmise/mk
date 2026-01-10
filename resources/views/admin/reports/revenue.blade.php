@extends('admin.layouts.app')

@section('title', 'Revenue Report')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <a href="{{ route('admin.reports.index') }}">Reports</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>Revenue Report</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Revenue Report</h1>
        <p class="page-subtitle">Income analysis by room type, source, and period</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.reports.export', ['report' => 'revenue']) }}?start_date={{ $startDate }}&end_date={{ $endDate }}" class="btn btn-secondary">
            <i class="fas fa-download"></i> Export CSV
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.reports.revenue') }}" method="GET" class="d-flex align-center gap-3 flex-wrap">
            <div class="form-group mb-0" style="min-width: 150px;">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="form-group mb-0" style="min-width: 150px;">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="form-group mb-0" style="min-width: 150px;">
                <label class="form-label">Group By</label>
                <select name="group_by" class="form-control">
                    <option value="day" {{ request('group_by') == 'day' ? 'selected' : '' }}>Daily</option>
                    <option value="week" {{ request('group_by') == 'week' ? 'selected' : '' }}>Weekly</option>
                    <option value="month" {{ request('group_by', 'month') == 'month' ? 'selected' : '' }}>Monthly</option>
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
<div class="stats-grid" style="grid-template-columns: repeat(5, 1fr); margin-bottom: 1.5rem;">
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">${{ number_format($summary['total_revenue'] ?? 0, 0) }}</div>
            <div class="stat-label">Total Revenue</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-bed"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">${{ number_format($summary['room_revenue'] ?? 0, 0) }}</div>
            <div class="stat-label">Room Revenue</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-plus-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">${{ number_format($summary['extra_revenue'] ?? 0, 0) }}</div>
            <div class="stat-label">Extra Services</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon cyan">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">${{ number_format($summary['adr'] ?? 0, 2) }}</div>
            <div class="stat-label">ADR (Avg Daily Rate)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">
            <i class="fas fa-bed"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">${{ number_format($summary['revpar'] ?? 0, 2) }}</div>
            <div class="stat-label">RevPAR</div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <div class="col-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Revenue Trend</h3>
            </div>
            <div class="card-body">
                <canvas id="revenueTrendChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Revenue by Source</h3>
            </div>
            <div class="card-body">
                <canvas id="revenueSourceChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Revenue by Room Type -->
<div class="row mt-4">
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Revenue by Room Type</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Room Type</th>
                                <th>Bookings</th>
                                <th>Revenue</th>
                                <th>% of Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($revenueByRoomType ?? [] as $type)
                            <tr>
                                <td>{{ $type['name'] }}</td>
                                <td>{{ $type['bookings_count'] }}</td>
                                <td>${{ number_format($type['revenue'], 2) }}</td>
                                <td>
                                    <div class="d-flex align-center gap-2">
                                        <div class="progress" style="width: 60px; height: 8px;">
                                            <div class="progress-bar bg-primary" style="width: {{ $type['percentage'] }}%"></div>
                                        </div>
                                        <span>{{ number_format($type['percentage'], 1) }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Revenue by Booking Source</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Source</th>
                                <th>Bookings</th>
                                <th>Revenue</th>
                                <th>% of Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($revenueBySource ?? [] as $source)
                            <tr>
                                <td>
                                    <span class="d-flex align-center gap-2">
                                        @switch($source['source'])
                                            @case('direct')
                                                <i class="fas fa-globe text-primary"></i>
                                                @break
                                            @case('booking.com')
                                                <i class="fas fa-b text-info"></i>
                                                @break
                                            @case('expedia')
                                                <i class="fas fa-e text-warning"></i>
                                                @break
                                            @case('walk-in')
                                                <i class="fas fa-person-walking text-success"></i>
                                                @break
                                            @case('phone')
                                                <i class="fas fa-phone text-secondary"></i>
                                                @break
                                            @default
                                                <i class="fas fa-circle text-muted"></i>
                                        @endswitch
                                        {{ ucwords(str_replace(['-', '_', '.'], ' ', $source['source'])) }}
                                    </span>
                                </td>
                                <td>{{ $source['bookings_count'] }}</td>
                                <td>${{ number_format($source['revenue'], 2) }}</td>
                                <td>
                                    <div class="d-flex align-center gap-2">
                                        <div class="progress" style="width: 60px; height: 8px;">
                                            <div class="progress-bar bg-success" style="width: {{ $source['percentage'] }}%"></div>
                                        </div>
                                        <span>{{ number_format($source['percentage'], 1) }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Revenue Table -->
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Period Breakdown</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Period</th>
                        <th>Bookings</th>
                        <th>Room Revenue</th>
                        <th>Extra Revenue</th>
                        <th>Total Revenue</th>
                        <th>ADR</th>
                        <th>RevPAR</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periodData ?? [] as $period)
                    <tr>
                        <td>{{ $period['period'] }}</td>
                        <td>{{ $period['bookings_count'] }}</td>
                        <td>${{ number_format($period['room_revenue'], 2) }}</td>
                        <td>${{ number_format($period['extra_revenue'], 2) }}</td>
                        <td><strong>${{ number_format($period['total_revenue'], 2) }}</strong></td>
                        <td>${{ number_format($period['adr'], 2) }}</td>
                        <td>${{ number_format($period['revpar'], 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No revenue data available for the selected period</td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($periodData ?? []) > 0)
                <tfoot style="background: var(--gray-50); font-weight: 600;">
                    <tr>
                        <td>Total</td>
                        <td>{{ array_sum(array_column($periodData ?? [], 'bookings_count')) }}</td>
                        <td>${{ number_format(array_sum(array_column($periodData ?? [], 'room_revenue')), 2) }}</td>
                        <td>${{ number_format(array_sum(array_column($periodData ?? [], 'extra_revenue')), 2) }}</td>
                        <td>${{ number_format(array_sum(array_column($periodData ?? [], 'total_revenue')), 2) }}</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Trend Chart
    const periodData = @json($periodData ?? []);
    const trendCtx = document.getElementById('revenueTrendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'bar',
        data: {
            labels: periodData.map(d => d.period),
            datasets: [
                {
                    label: 'Room Revenue',
                    data: periodData.map(d => d.room_revenue),
                    backgroundColor: '#3b82f6'
                },
                {
                    label: 'Extra Revenue',
                    data: periodData.map(d => d.extra_revenue),
                    backgroundColor: '#f59e0b'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Revenue by Source Chart
    const sourceData = @json($revenueBySource ?? []);
    const sourceCtx = document.getElementById('revenueSourceChart').getContext('2d');
    new Chart(sourceCtx, {
        type: 'doughnut',
        data: {
            labels: sourceData.map(s => s.source.charAt(0).toUpperCase() + s.source.slice(1).replace(/[-_.]/g, ' ')),
            datasets: [{
                data: sourceData.map(s => s.revenue),
                backgroundColor: [
                    '#3b82f6',
                    '#10b981',
                    '#f59e0b',
                    '#06b6d4',
                    '#8b5cf6',
                    '#ef4444'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush
