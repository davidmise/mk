@extends('admin.layouts.app')

@section('title', 'Financial Report')

@section('breadcrumb')
    <a href="{{ route('admin.reports.index') }}">Reports</a>
    <i class="fas fa-chevron-right"></i>
    <span>Financial</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Financial Report</h1>
        <p class="page-subtitle">Comprehensive financial overview and analysis</p>
    </div>
    <div class="d-flex gap-2">
        <div class="date-range-picker">
            <select id="dateRange" class="form-control" onchange="updateDateRange(this.value)">
                <option value="today">Today</option>
                <option value="yesterday">Yesterday</option>
                <option value="7days" selected>Last 7 Days</option>
                <option value="30days">Last 30 Days</option>
                <option value="this_month">This Month</option>
                <option value="last_month">Last Month</option>
                <option value="this_year">This Year</option>
                <option value="custom">Custom Range</option>
            </select>
        </div>
        <button class="btn btn-secondary" onclick="exportReport('pdf')">
            <i class="fas fa-file-pdf"></i> Export PDF
        </button>
        <button class="btn btn-secondary" onclick="exportReport('excel')">
            <i class="fas fa-file-excel"></i> Export Excel
        </button>
    </div>
</div>

<!-- Financial Summary Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">${{ number_format($totalRevenue ?? 0, 2) }}</div>
            <div class="stat-change {{ ($revenueChange ?? 0) >= 0 ? 'up' : 'down' }}">
                <i class="fas fa-arrow-{{ ($revenueChange ?? 0) >= 0 ? 'up' : 'down' }}"></i>
                {{ abs($revenueChange ?? 0) }}% vs previous period
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-bed"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Room Revenue</div>
            <div class="stat-value">${{ number_format($roomRevenue ?? 0, 2) }}</div>
            <div class="stat-change">{{ $roomRevenuePercent ?? 0 }}% of total</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-utensils"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">F&B / Services</div>
            <div class="stat-value">${{ number_format($serviceRevenue ?? 0, 2) }}</div>
            <div class="stat-change">{{ $serviceRevenuePercent ?? 0 }}% of total</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon cyan">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Average Daily Rate</div>
            <div class="stat-value">${{ number_format($adr ?? 0, 2) }}</div>
            <div class="stat-change {{ ($adrChange ?? 0) >= 0 ? 'up' : 'down' }}">
                <i class="fas fa-arrow-{{ ($adrChange ?? 0) >= 0 ? 'up' : 'down' }}"></i>
                {{ abs($adrChange ?? 0) }}% change
            </div>
        </div>
    </div>
</div>

<!-- Additional KPIs -->
<div class="stats-grid mb-4">
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-chart-bar"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">RevPAR</div>
            <div class="stat-value">${{ number_format($revpar ?? 0, 2) }}</div>
            <div class="stat-change">Revenue per Available Room</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-percent"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Occupancy Rate</div>
            <div class="stat-value">{{ $occupancyRate ?? 0 }}%</div>
            <div class="stat-change">{{ $roomNightsSold ?? 0 }} room nights sold</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Bookings</div>
            <div class="stat-value">{{ $totalBookings ?? 0 }}</div>
            <div class="stat-change">{{ $newBookings ?? 0 }} new reservations</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon red">
            <i class="fas fa-undo"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Refunds</div>
            <div class="stat-value">${{ number_format($totalRefunds ?? 0, 2) }}</div>
            <div class="stat-change">{{ $cancellations ?? 0 }} cancellations</div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Revenue Chart -->
    <div class="col-8">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Revenue Trend</h3>
                <div class="chart-legend">
                    <span class="legend-item"><span class="legend-color" style="background: #2563eb;"></span> Room Revenue</span>
                    <span class="legend-item"><span class="legend-color" style="background: #22c55e;"></span> Services</span>
                    <span class="legend-item"><span class="legend-color" style="background: #f59e0b;"></span> F&B</span>
                </div>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="300"></canvas>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Payments by Method</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Payment Method</th>
                                <th>Transactions</th>
                                <th>Amount</th>
                                <th>% of Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paymentsByMethod ?? [] as $method => $data)
                                <tr>
                                    <td>
                                        <i class="{{ $data['icon'] ?? 'fas fa-credit-card' }} text-primary"></i>
                                        {{ ucfirst(str_replace('_', ' ', $method)) }}
                                    </td>
                                    <td>{{ $data['count'] }}</td>
                                    <td>${{ number_format($data['amount'], 2) }}</td>
                                    <td>
                                        <div class="progress-inline">
                                            <div class="progress-fill" style="width: {{ $data['percent'] }}%"></div>
                                            <span>{{ $data['percent'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No payment data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(isset($paymentsByMethod) && count($paymentsByMethod) > 0)
                        <tfoot>
                            <tr class="total-row">
                                <td><strong>Total</strong></td>
                                <td><strong>{{ collect($paymentsByMethod)->sum('count') }}</strong></td>
                                <td><strong>${{ number_format(collect($paymentsByMethod)->sum('amount'), 2) }}</strong></td>
                                <td><strong>100%</strong></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Revenue by Room Type -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Revenue by Room Type</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Room Type</th>
                                <th>Bookings</th>
                                <th>Room Nights</th>
                                <th>Revenue</th>
                                <th>ADR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($revenueByRoomType ?? [] as $roomType)
                                <tr>
                                    <td><strong>{{ $roomType['name'] }}</strong></td>
                                    <td>{{ $roomType['bookings'] }}</td>
                                    <td>{{ $roomType['room_nights'] }}</td>
                                    <td>${{ number_format($roomType['revenue'], 2) }}</td>
                                    <td>${{ number_format($roomType['adr'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No room type data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-4">
        <!-- Revenue Distribution -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Revenue Distribution</h3>
            </div>
            <div class="card-body">
                <canvas id="distributionChart" height="200"></canvas>
                <div class="distribution-legend mt-3">
                    <div class="legend-row">
                        <span class="legend-color" style="background: #2563eb;"></span>
                        <span>Room Revenue</span>
                        <strong>{{ $roomRevenuePercent ?? 0 }}%</strong>
                    </div>
                    <div class="legend-row">
                        <span class="legend-color" style="background: #22c55e;"></span>
                        <span>Services</span>
                        <strong>{{ $serviceRevenuePercent ?? 0 }}%</strong>
                    </div>
                    <div class="legend-row">
                        <span class="legend-color" style="background: #f59e0b;"></span>
                        <span>F&B</span>
                        <strong>{{ $fbRevenuePercent ?? 0 }}%</strong>
                    </div>
                    <div class="legend-row">
                        <span class="legend-color" style="background: #64748b;"></span>
                        <span>Other</span>
                        <strong>{{ $otherRevenuePercent ?? 0 }}%</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue by Source -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Booking Sources</h3>
            </div>
            <div class="card-body">
                @forelse($revenueBySource ?? [] as $source => $data)
                    <div class="source-item mb-3">
                        <div class="d-flex justify-between mb-1">
                            <span>{{ ucfirst(str_replace('_', ' ', $source)) }}</span>
                            <strong>${{ number_format($data['revenue'], 2) }}</strong>
                        </div>
                        <div class="progress-bar-sm">
                            <div class="progress-fill" style="width: {{ $data['percent'] }}%; background: {{ $data['color'] ?? '#2563eb' }};"></div>
                        </div>
                        <small class="text-muted">{{ $data['bookings'] }} bookings ({{ $data['percent'] }}%)</small>
                    </div>
                @empty
                    <p class="text-muted text-center">No source data available</p>
                @endforelse
            </div>
        </div>

        <!-- Outstanding Payments -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Outstanding Payments</h3>
            </div>
            <div class="card-body">
                <div class="outstanding-display">
                    <div class="outstanding-amount text-danger">${{ number_format($outstandingAmount ?? 0, 2) }}</div>
                    <div class="outstanding-label">{{ $outstandingBookings ?? 0 }} bookings with balance due</div>
                </div>
                @if(($outstandingAmount ?? 0) > 0)
                    <a href="{{ route('admin.bookings.index', ['payment_status' => 'partial']) }}" class="btn btn-danger btn-block mt-3">
                        View Outstanding
                    </a>
                @endif
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Transactions</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="transaction-list">
                    @forelse($recentTransactions ?? [] as $transaction)
                        <div class="transaction-item">
                            <div class="transaction-icon {{ $transaction['type'] === 'refund' ? 'refund' : '' }}">
                                <i class="fas {{ $transaction['type'] === 'refund' ? 'fa-undo' : 'fa-arrow-down' }}"></i>
                            </div>
                            <div class="transaction-info">
                                <div class="transaction-ref">{{ $transaction['reference'] }}</div>
                                <div class="transaction-time">{{ $transaction['date']->format('M d, H:i') }}</div>
                            </div>
                            <div class="transaction-amount {{ $transaction['type'] === 'refund' ? 'text-danger' : 'text-success' }}">
                                {{ $transaction['type'] === 'refund' ? '-' : '+' }}${{ number_format($transaction['amount'], 2) }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">No recent transactions</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.date-range-picker select {
    min-width: 160px;
}

.chart-legend {
    display: flex;
    gap: 1rem;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.8125rem;
    color: var(--gray-600);
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 2px;
}

.progress-inline {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.progress-inline .progress-fill {
    height: 6px;
    background: var(--primary);
    border-radius: 3px;
    flex: 1;
    max-width: 100px;
}

.progress-inline span {
    font-size: 0.8125rem;
    color: var(--gray-600);
}

.total-row {
    background: var(--gray-50);
}

.distribution-legend {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.legend-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.legend-row .legend-color {
    width: 12px;
    height: 12px;
    border-radius: 2px;
}

.legend-row span {
    flex: 1;
    color: var(--gray-600);
}

.legend-row strong {
    color: var(--gray-800);
}

.progress-bar-sm {
    height: 4px;
    background: var(--gray-200);
    border-radius: 2px;
    overflow: hidden;
}

.progress-bar-sm .progress-fill {
    height: 100%;
    border-radius: 2px;
}

.outstanding-display {
    text-align: center;
    padding: 1rem 0;
}

.outstanding-amount {
    font-size: 2rem;
    font-weight: 700;
}

.outstanding-label {
    font-size: 0.875rem;
    color: var(--gray-500);
}

.btn-block {
    width: 100%;
    text-align: center;
}

.transaction-list {
    max-height: 300px;
    overflow-y: auto;
}

.transaction-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--gray-100);
}

.transaction-item:last-child {
    border-bottom: none;
}

.transaction-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(34, 197, 94, 0.1);
    color: var(--success);
    display: flex;
    align-items: center;
    justify-content: center;
}

.transaction-icon.refund {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.transaction-info {
    flex: 1;
}

.transaction-ref {
    font-weight: 500;
    color: var(--gray-800);
    font-size: 0.875rem;
}

.transaction-time {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.transaction-amount {
    font-weight: 600;
}
</style>
@endpush

@push('scripts')
<script>
// Revenue Trend Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($chartLabels ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']) !!},
        datasets: [
            {
                label: 'Room Revenue',
                data: {!! json_encode($roomRevenueData ?? [0, 0, 0, 0, 0, 0, 0]) !!},
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                fill: true,
                tension: 0.4
            },
            {
                label: 'Services',
                data: {!! json_encode($serviceRevenueData ?? [0, 0, 0, 0, 0, 0, 0]) !!},
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                fill: true,
                tension: 0.4
            },
            {
                label: 'F&B',
                data: {!! json_encode($fbRevenueData ?? [0, 0, 0, 0, 0, 0, 0]) !!},
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                fill: true,
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
                    }
                }
            }
        },
        scales: {
            y: {
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

// Distribution Chart
const distCtx = document.getElementById('distributionChart').getContext('2d');
const distChart = new Chart(distCtx, {
    type: 'doughnut',
    data: {
        labels: ['Room Revenue', 'Services', 'F&B', 'Other'],
        datasets: [{
            data: [
                {{ $roomRevenuePercent ?? 0 }},
                {{ $serviceRevenuePercent ?? 0 }},
                {{ $fbRevenuePercent ?? 0 }},
                {{ $otherRevenuePercent ?? 0 }}
            ],
            backgroundColor: ['#2563eb', '#22c55e', '#f59e0b', '#64748b']
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
        cutout: '70%'
    }
});

function updateDateRange(range) {
    const url = new URL(window.location.href);
    url.searchParams.set('range', range);
    window.location.href = url.toString();
}

function exportReport(format) {
    const range = document.getElementById('dateRange').value;
    window.location.href = `{{ route('admin.reports.export', ['type' => 'financial']) }}?format=${format}&range=${range}`;
}
</script>
@endpush
