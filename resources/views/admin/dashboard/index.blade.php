@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <span>Dashboard</span>
@endsection

@section('content')
@if(isset($error))
    <div class="alert alert-danger">
        <strong>Dashboard Error:</strong> {{ $error }}
    </div>
@endif

<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Welcome back, {{ auth()->user()->name }}! Here's what's happening at MK Hotel.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary" onclick="openBookingModal()">
            <i class="fas fa-plus"></i> New Booking
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Today's Arrivals</div>
            <div class="stat-value">{{ $todayCheckIns }}</div>
            <div class="stat-change up">
                <i class="fas fa-arrow-up"></i> Expected check-ins
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Today's Departures</div>
            <div class="stat-value">{{ $todayCheckOuts }}</div>
            <div class="stat-change">Pending check-outs</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-bed"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Occupancy Rate</div>
            <div class="stat-value">{{ $occupancyRate }}%</div>
            <div class="stat-change up">
                <i class="fas fa-arrow-up"></i> {{ $occupiedRooms }} of {{ $totalRooms }} rooms
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon cyan">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Today's Revenue</div>
            <div class="stat-value">${{ number_format($todayRevenue, 2) }}</div>
            <div class="stat-change up">
                <i class="fas fa-arrow-up"></i> Monthly: ${{ number_format($monthlyRevenue, 2) }}
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions & Today's Activity -->
<div class="row">
    <div class="col-8">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Revenue Overview</h3>
                <select class="form-control" style="width: auto;" id="revenueRange">
                    <option value="7">Last 7 days</option>
                    <option value="30" selected>Last 30 days</option>
                    <option value="90">Last 90 days</option>
                </select>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-4">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Room Status</h3>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height: 200px;">
                    <canvas id="roomStatusChart"></canvas>
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-between align-center mb-2">
                        <span><i class="fas fa-circle text-success"></i> Available</span>
                        <strong>{{ $availableRooms }}</strong>
                    </div>
                    <div class="d-flex justify-between align-center mb-2">
                        <span><i class="fas fa-circle text-danger"></i> Occupied</span>
                        <strong>{{ $occupiedRooms }}</strong>
                    </div>
                    <div class="d-flex justify-between align-center mb-2">
                        <span><i class="fas fa-circle text-warning"></i> Reserved</span>
                        <strong>{{ $confirmedBookings }}</strong>
                    </div>
                    <div class="d-flex justify-between align-center">
                        <span><i class="fas fa-circle text-muted"></i> Maintenance</span>
                        <strong>{{ $maintenanceRooms }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Today's Arrivals & Departures -->
<div class="row">
    <div class="col-6">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-plane-arrival text-success"></i> Today's Arrivals
                </h3>
                <a href="{{ route('admin.bookings.index', ['check_in_date' => today()->format('Y-m-d')]) }}" class="btn btn-sm btn-secondary">View All</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Guest</th>
                                <th>Room</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todayArrivals as $booking)
                            <tr>
                                <td>
                                    <strong>{{ $booking->guest?->full_name ?? $booking->guest_name }}</strong>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $booking->booking_reference }}</div>
                                </td>
                                <td>{{ $booking->roomType->name ?? '-' }}</td>
                                <td>
                                    @if($booking->status === 'confirmed')
                                        <span class="badge badge-success">Confirmed</span>
                                    @elseif($booking->status === 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($booking->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($booking->status === 'confirmed')
                                        <form action="{{ route('admin.bookings.check-in', $booking) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Check In</button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-secondary">View</a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No arrivals scheduled for today</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-plane-departure text-warning"></i> Today's Departures
                </h3>
                <a href="{{ route('admin.bookings.index', ['check_out_date' => today()->format('Y-m-d')]) }}" class="btn btn-sm btn-secondary">View All</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Guest</th>
                                <th>Room</th>
                                <th>Balance</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todayDepartures as $booking)
                            <tr>
                                <td>
                                    <strong>{{ $booking->guest?->full_name ?? $booking->guest_name }}</strong>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $booking->booking_reference }}</div>
                                </td>
                                <td>{{ $booking->room?->room_number ?? $booking->roomType->name }}</td>
                                <td>
                                    @php $balance = $booking->total_amount - $booking->paid_amount @endphp
                                    @if($balance > 0)
                                        <span class="text-danger">${{ number_format($balance, 2) }}</span>
                                    @else
                                        <span class="text-success">Paid</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.bookings.check-out', $booking) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning">Check Out</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No departures scheduled for today</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Bookings & Activity -->
<div class="row">
    <div class="col-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Bookings</h3>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-secondary">View All</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Guest</th>
                                <th>Room Type</th>
                                <th>Dates</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.bookings.show', $booking) }}" style="color: var(--primary); font-weight: 500;">
                                        {{ $booking->booking_reference }}
                                    </a>
                                </td>
                                <td>{{ $booking->guest?->full_name ?? $booking->guest_name }}</td>
                                <td>{{ $booking->roomType->name ?? '-' }}</td>
                                <td>
                                    {{ $booking->check_in_date->format('M d') }} - {{ $booking->check_out_date->format('M d') }}
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $booking->total_nights }} nights</div>
                                </td>
                                <td>${{ number_format($booking->total_amount, 2) }}</td>
                                <td>
                                    @switch($booking->status)
                                        @case('pending')
                                            <span class="badge badge-warning">Pending</span>
                                            @break
                                        @case('confirmed')
                                            <span class="badge badge-success">Confirmed</span>
                                            @break
                                        @case('checked_in')
                                            <span class="badge badge-info">Checked In</span>
                                            @break
                                        @case('checked_out')
                                            <span class="badge badge-secondary">Checked Out</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge badge-danger">Cancelled</span>
                                            @break
                                    @endswitch
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No recent bookings</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Activity</h3>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                @forelse($recentActivity as $activity)
                <div class="d-flex gap-3 mb-3">
                    <div class="stat-icon {{ $activity->action === 'created' ? 'green' : ($activity->action === 'updated' ? 'blue' : 'orange') }}" style="width: 36px; height: 36px; font-size: 0.875rem;">
                        @switch($activity->action)
                            @case('created')
                                <i class="fas fa-plus"></i>
                                @break
                            @case('updated')
                                <i class="fas fa-edit"></i>
                                @break
                            @case('deleted')
                                <i class="fas fa-trash"></i>
                                @break
                            @default
                                <i class="fas fa-info"></i>
                        @endswitch
                    </div>
                    <div>
                        <div style="font-size: 0.875rem;">{{ $activity->description }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">
                            {{ $activity->user?->name ?? 'System' }} • {{ $activity->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">No recent activity</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($revenueChartData, 'date')) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode(array_column($revenueChartData, 'revenue')) !!},
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 5
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
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Room Status Chart
    const roomCtx = document.getElementById('roomStatusChart').getContext('2d');
    new Chart(roomCtx, {
        type: 'doughnut',
        data: {
            labels: ['Available', 'Occupied', 'Reserved', 'Maintenance'],
            datasets: [{
                data: [
                    {{ $availableRooms }},
                    {{ $occupiedRooms }},
                    {{ $confirmedBookings }},
                    {{ $maintenanceRooms }}
                ],
                backgroundColor: ['#22c55e', '#ef4444', '#f59e0b', '#9ca3af'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endpush
<!-- Quick Booking Modal -->
<div id="quickBookingModal" class="modal-overlay" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Walk-In Booking</h5>
                <button type="button" class="modal-close" onclick="closeBookingModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="quickBookingForm" method="POST" action="{{ route('admin.bookings.store') }}" onsubmit="return validateBookingForm()">
                @csrf
                <div class="modal-body">
                    <div class="form-row">
                        <!-- Guest Information -->
                        <div class="form-group col-md-6">
                            <label class="form-label">Guest Name *</label>
                            <input type="text" name="guest_name" class="form-control" placeholder="Enter guest name" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="guest_email" class="form-control" placeholder="guest@example.com">
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label">Phone *</label>
                            <input type="tel" name="guest_phone" class="form-control" placeholder="Contact number" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Adults *</label>
                            <input type="number" name="adults" class="form-control" value="1" min="1" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label">Children</label>
                            <input type="number" name="children" class="form-control" value="0" min="0">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Number of Rooms *</label>
                            <input type="number" name="number_of_rooms" class="form-control" value="1" min="1" required>
                        </div>

                        <!-- Room Selection -->
                        <div class="form-group col-md-6">
                            <label class="form-label">Room Type *</label>
                            <select name="room_type_id" class="form-control" id="roomTypeSelect" required>
                                <option value="">-- Select Room Type --</option>
                                @foreach(\App\Models\RoomType::active()->get() as $roomType)
                                    <option value="{{ $roomType->id }}">{{ $roomType->name }} - ${{ number_format($roomType->base_price, 2) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Specific Room</label>
                            <select name="room_id" class="form-control" id="roomSelect">
                                <option value="">-- Auto Assign --</option>
                            </select>
                        </div>

                        <!-- Dates -->
                        <div class="form-group col-md-6">
                            <label class="form-label">Check-In *</label>
                            <input type="date" name="check_in" class="form-control" value="{{ today()->format('Y-m-d') }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Check-Out *</label>
                            <input type="date" name="check_out" class="form-control" value="{{ today()->addDay()->format('Y-m-d') }}" required>
                        </div>

                        <!-- Source -->
                        <div class="form-group col-12">
                            <label class="form-label">Booking Source</label>
                            <select name="source" class="form-control">
                                <option value="walk_in" selected>Walk-In</option>
                                <option value="phone">Phone</option>
                                <option value="email">Email</option>
                                <option value="website">Website</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="form-group col-12">
                            <label class="form-label">Special Requests</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Any special requests or notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeBookingModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1050;
}

.modal-dialog {
    background: white;
    border-radius: 12px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-lg {
    max-width: 900px;
}

.modal-content {
    border: none;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
    border-radius: 12px 12px 0 0;
}

.modal-title {
    font-weight: 600;
    font-size: 1.25rem;
    color: #1f2937;
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #6b7280;
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    color: #1f2937;
}

.modal-body {
    padding: 2rem;
}

.modal-footer {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    border-top: 1px solid #e5e7eb;
    background: #f9fafb;
    border-radius: 0 0 12px 12px;
    justify-content: flex-end;
}

.form-row {
    display: flex;
    flex-wrap: wrap;
    margin: -0.5rem;
}

.form-group {
    padding: 0.5rem;
}

.form-group.col-12 {
    flex: 0 0 100%;
}

.form-group.col-md-6 {
    flex: 0 0 50%;
}

@media (max-width: 768px) {
    .form-group.col-md-6 {
        flex: 0 0 100%;
    }
}

.form-label {
    display: block;
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: #374151;
    font-size: 0.875rem;
}

.form-control {
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 0.75rem;
    font-size: 0.9375rem;
    font-family: inherit;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    border: none;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary {
    background: #2563eb;
    color: white;
}

.btn-primary:hover {
    background: #1d4ed8;
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.btn-secondary:hover {
    background: #d1d5db;
}
</style>

<script>
function openBookingModal() {
    document.getElementById('quickBookingModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeBookingModal() {
    document.getElementById('quickBookingModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('quickBookingModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeBookingModal();
    }
});

// Load available rooms when room type is selected
document.getElementById('roomTypeSelect')?.addEventListener('change', function() {
    const roomTypeId = this.value;
    const roomSelect = document.getElementById('roomSelect');

    if (!roomTypeId) {
        roomSelect.innerHTML = '<option value="">-- Auto Assign --</option>';
        return;
    }

    const checkIn = document.querySelector('input[name="check_in"]').value;
    const checkOut = document.querySelector('input[name="check_out"]').value;

    if (!checkIn || !checkOut) return;

    // Fetch available rooms for this room type
    fetch(`/admin/api/rooms/available?room_type_id=${roomTypeId}&check_in=${checkIn}&check_out=${checkOut}`)
        .then(response => response.json())
        .then(data => {
            roomSelect.innerHTML = '<option value="">-- Auto Assign --</option>';
            if (data.length === 0) {
                const option = document.createElement('option');
                option.textContent = 'No rooms available for selected dates';
                option.disabled = true;
                roomSelect.appendChild(option);
            } else {
                data.forEach(room => {
                    const option = document.createElement('option');
                    option.value = room.id;
                    option.textContent = `${room.room_number} (Floor ${room.floor})`;
                    roomSelect.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Error loading rooms:', error));
});

// Reload rooms when dates change
document.querySelector('input[name="check_in"]')?.addEventListener('change', function() {
    const roomTypeSelect = document.getElementById('roomTypeSelect');
    if (roomTypeSelect.value) {
        roomTypeSelect.dispatchEvent(new Event('change'));
    }
});

document.querySelector('input[name="check_out"]')?.addEventListener('change', function() {
    const roomTypeSelect = document.getElementById('roomTypeSelect');
    if (roomTypeSelect.value) {
        roomTypeSelect.dispatchEvent(new Event('change'));
    }
});

function validateBookingForm() {
    const checkIn = new Date(document.querySelector('input[name="check_in"]').value);
    const checkOut = new Date(document.querySelector('input[name="check_out"]').value);

    if (checkOut <= checkIn) {
        alert('Check-out date must be after check-in date');
        return false;
    }
    return true;
}
</script>
