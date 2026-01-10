@extends('admin.layouts.app')

@section('title', 'Room Availability Calendar')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>Calendar</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Room Availability Calendar</h1>
        <p class="page-subtitle">Visual overview of room availability and bookings</p>
    </div>
    <div class="page-actions">
        @can('bookings.create')
        <button type="button" class="btn btn-primary" data-modal="quickBookingModal">
            <i class="fas fa-plus"></i> Quick Booking
        </button>
        @endcan
    </div>
</div>

<!-- Calendar Navigation -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex align-center justify-between">
            <div class="d-flex align-center gap-3">
                <a href="{{ route('admin.calendar.index', ['month' => $prevMonth->format('Y-m')]) }}" class="btn btn-secondary">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <h3 style="margin: 0; min-width: 200px; text-align: center;">{{ $currentMonth->format('F Y') }}</h3>
                <a href="{{ route('admin.calendar.index', ['month' => $nextMonth->format('Y-m')]) }}" class="btn btn-secondary">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="{{ route('admin.calendar.index') }}" class="btn btn-secondary">Today</a>
            </div>

            <div class="d-flex align-center gap-3">
                <!-- Room Type Filter -->
                <select id="roomTypeFilter" class="form-control" style="min-width: 150px;">
                    <option value="">All Room Types</option>
                    @foreach($roomTypes ?? [] as $type)
                        <option value="{{ $type->id }}" {{ request('room_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Legend -->
                <div class="d-flex align-center gap-3" style="font-size: 0.75rem;">
                    <span class="d-flex align-center gap-1">
                        <span style="width: 12px; height: 12px; background: #dcfce7; border: 1px solid #22c55e; border-radius: 2px;"></span>
                        Available
                    </span>
                    <span class="d-flex align-center gap-1">
                        <span style="width: 12px; height: 12px; background: #fee2e2; border: 1px solid #ef4444; border-radius: 2px;"></span>
                        Occupied
                    </span>
                    <span class="d-flex align-center gap-1">
                        <span style="width: 12px; height: 12px; background: #fef3c7; border: 1px solid #f59e0b; border-radius: 2px;"></span>
                        Reserved
                    </span>
                    <span class="d-flex align-center gap-1">
                        <span style="width: 12px; height: 12px; background: #e5e7eb; border: 1px solid #9ca3af; border-radius: 2px;"></span>
                        Maintenance
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Calendar Grid -->
<div class="card">
    <div class="card-body" style="padding: 0; overflow-x: auto;">
        <table class="calendar-table">
            <thead>
                <tr>
                    <th class="room-cell" style="position: sticky; left: 0; z-index: 10; background: var(--gray-50);">
                        Room
                    </th>
                    @foreach($dates as $date)
                        <th class="date-cell {{ $date->isToday() ? 'today' : '' }} {{ $date->isWeekend() ? 'weekend' : '' }}">
                            <div class="date-header">
                                <span class="day-name">{{ $date->format('D') }}</span>
                                <span class="day-number">{{ $date->format('j') }}</span>
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rooms ?? [] as $room)
                <tr class="room-row" data-room-type="{{ $room->room_type_id }}">
                    <td class="room-cell" style="position: sticky; left: 0; z-index: 5; background: white;">
                        <div class="room-info">
                            <strong>{{ $room->room_number }}</strong>
                            <span class="text-muted" style="font-size: 0.75rem;">{{ $room->roomType->name ?? 'N/A' }}</span>
                        </div>
                    </td>
                    @foreach($dates as $date)
                        @php
                            $dateKey = $date->format('Y-m-d');
                            $status = $roomAvailability[$room->id][$dateKey] ?? null;
                            $booking = $bookingsByRoomDate[$room->id][$dateKey] ?? null;
                        @endphp
                        <td class="availability-cell {{ $date->isToday() ? 'today' : '' }} {{ $date->isWeekend() ? 'weekend' : '' }}"
                            data-room="{{ $room->id }}"
                            data-date="{{ $dateKey }}"
                            data-status="{{ $status['status'] ?? 'available' }}">
                            @if($booking)
                                <div class="booking-bar status-{{ $booking->status }}"
                                     data-booking-id="{{ $booking->id }}"
                                     title="{{ $booking->guest->full_name ?? 'Guest' }} - {{ $booking->check_in ? $booking->check_in->format('M d') : '-' }} to {{ $booking->check_out ? $booking->check_out->format('M d') : '-' }}">
                                    @if($status['is_start'] ?? false)
                                        <span class="booking-guest">{{ Str::limit($booking->guest->full_name ?? 'Guest', 10) }}</span>
                                    @endif
                                </div>
                            @elseif(($status['status'] ?? 'available') === 'maintenance')
                                <div class="maintenance-bar" title="Under Maintenance">
                                    <i class="fas fa-wrench"></i>
                                </div>
                            @elseif(($status['status'] ?? 'available') === 'cleaning')
                                <div class="cleaning-bar" title="Cleaning">
                                    <i class="fas fa-broom"></i>
                                </div>
                            @endif
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Today's Activity Widget -->
<div class="row mt-4">
    <div class="col-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-sign-in-alt text-success"></i> Today's Check-ins
                </h3>
            </div>
            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                @forelse($todayArrivals ?? [] as $arrival)
                <div class="activity-item d-flex align-center justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                    <div>
                        <strong>{{ $arrival->guest->full_name ?? 'Guest' }}</strong>
                        <div class="text-muted" style="font-size: 0.75rem;">
                            Room {{ $arrival->room->room_number ?? 'N/A' }} • {{ $arrival->nights }} nights
                        </div>
                    </div>
                    <span class="badge badge-{{ $arrival->status === 'checked_in' ? 'success' : 'warning' }}">
                        {{ $arrival->status === 'checked_in' ? 'Arrived' : 'Expected' }}
                    </span>
                </div>
                @empty
                <p class="text-muted text-center py-3">No check-ins scheduled for today</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-sign-out-alt text-danger"></i> Today's Check-outs
                </h3>
            </div>
            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                @forelse($todayDepartures ?? [] as $departure)
                <div class="activity-item d-flex align-center justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                    <div>
                        <strong>{{ $departure->guest->full_name ?? 'Guest' }}</strong>
                        <div class="text-muted" style="font-size: 0.75rem;">
                            Room {{ $departure->room->room_number ?? 'N/A' }}
                        </div>
                    </div>
                    <span class="badge badge-{{ $departure->status === 'checked_out' ? 'secondary' : 'info' }}">
                        {{ $departure->status === 'checked_out' ? 'Departed' : 'Due' }}
                    </span>
                </div>
                @empty
                <p class="text-muted text-center py-3">No check-outs scheduled for today</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie text-primary"></i> Today's Summary
                </h3>
            </div>
            <div class="card-body">
                <div class="summary-stats">
                    <div class="d-flex justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                        <span class="text-muted">Total Rooms</span>
                        <strong>{{ $todaySummary['total_rooms'] ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                        <span class="text-muted">Occupied</span>
                        <strong class="text-danger">{{ $todaySummary['occupied'] ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                        <span class="text-muted">Available</span>
                        <strong class="text-success">{{ $todaySummary['available'] ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                        <span class="text-muted">Maintenance</span>
                        <strong class="text-secondary">{{ $todaySummary['maintenance'] ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-between py-2">
                        <span class="text-muted">Occupancy Rate</span>
                        <strong class="text-primary">{{ number_format($todaySummary['occupancy_rate'] ?? 0, 1) }}%</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Booking Modal -->
<div id="quickBookingModal" class="modal" style="display: none;">
    <div class="modal-dialog" style="max-width: 500px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Quick Booking</h4>
                <button type="button" class="modal-close" data-modal-close>&times;</button>
            </div>
            <form action="{{ route('admin.calendar.quick-book') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Guest Name <span class="text-danger">*</span></label>
                        <input type="text" name="guest_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="guest_email" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="tel" name="guest_phone" class="form-control">
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Check-in Date <span class="text-danger">*</span></label>
                                <input type="date" name="check_in_date" id="modalCheckIn" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Check-out Date <span class="text-danger">*</span></label>
                                <input type="date" name="check_out_date" id="modalCheckOut" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Room <span class="text-danger">*</span></label>
                        <select name="room_id" id="modalRoom" class="form-control" required>
                            <option value="">Select Room</option>
                            @foreach($rooms ?? [] as $room)
                                <option value="{{ $room->id }}">
                                    {{ $room->room_number }} - {{ $room->roomType->name ?? 'N/A' }} (${{ number_format($room->roomType->base_price ?? 0, 0) }}/night)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Adults</label>
                                <input type="number" name="adults" class="form-control" value="1" min="1" max="10">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Children</label>
                                <input type="number" name="children" class="form-control" value="0" min="0" max="10">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Special Requests</label>
                        <textarea name="special_requests" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.calendar-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.calendar-table th,
.calendar-table td {
    border: 1px solid var(--gray-200);
    text-align: center;
    vertical-align: middle;
}

.room-cell {
    width: 150px;
    min-width: 150px;
    padding: 0.75rem;
    text-align: left !important;
    background: var(--gray-50);
}

.room-info {
    display: flex;
    flex-direction: column;
}

.date-cell {
    width: 40px;
    min-width: 40px;
    padding: 0.5rem 0.25rem;
    background: var(--gray-50);
}

.date-cell.today {
    background: #dbeafe;
}

.date-cell.weekend {
    background: #fef3c7;
}

.date-header {
    display: flex;
    flex-direction: column;
    font-size: 0.75rem;
}

.day-name {
    color: var(--gray-500);
    font-size: 0.625rem;
    text-transform: uppercase;
}

.day-number {
    font-weight: 600;
    color: var(--gray-700);
}

.availability-cell {
    height: 40px;
    padding: 2px;
    position: relative;
    cursor: pointer;
    transition: background-color 0.15s;
}

.availability-cell:hover {
    background-color: var(--gray-100);
}

.availability-cell.today {
    background-color: #eff6ff;
}

.availability-cell.weekend {
    background-color: #fffbeb;
}

.availability-cell[data-status="available"] {
    background-color: #f0fdf4;
}

.availability-cell[data-status="occupied"],
.availability-cell[data-status="reserved"] {
    background-color: transparent;
}

.booking-bar {
    position: absolute;
    top: 4px;
    left: 0;
    right: 0;
    bottom: 4px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    padding: 0 4px;
    font-size: 0.625rem;
    font-weight: 500;
    color: white;
    overflow: hidden;
    white-space: nowrap;
    cursor: pointer;
}

.booking-bar.status-confirmed,
.booking-bar.status-checked_in {
    background: #3b82f6;
}

.booking-bar.status-pending {
    background: #f59e0b;
}

.booking-bar.status-checked_out {
    background: #6b7280;
}

.booking-bar.status-cancelled {
    background: #ef4444;
    opacity: 0.5;
}

.booking-guest {
    overflow: hidden;
    text-overflow: ellipsis;
}

.maintenance-bar,
.cleaning-bar {
    position: absolute;
    top: 4px;
    left: 4px;
    right: 4px;
    bottom: 4px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
}

.maintenance-bar {
    background: #e5e7eb;
    color: #6b7280;
}

.cleaning-bar {
    background: #fef3c7;
    color: #d97706;
}

/* Modal styles */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-dialog {
    background: white;
    border-radius: 0.5rem;
    width: 100%;
    margin: 1rem;
    max-height: 90vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.modal-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-title {
    margin: 0;
    font-size: 1.125rem;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--gray-500);
}

.modal-body {
    padding: 1.5rem;
    overflow-y: auto;
}

.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--gray-200);
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Room type filter
    const roomTypeFilter = document.getElementById('roomTypeFilter');
    if (roomTypeFilter) {
        roomTypeFilter.addEventListener('change', function() {
            const selectedType = this.value;
            const rows = document.querySelectorAll('.room-row');

            rows.forEach(row => {
                if (!selectedType || row.dataset.roomType === selectedType) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Modal handlers
    document.querySelectorAll('[data-modal]').forEach(trigger => {
        trigger.addEventListener('click', function() {
            const modalId = this.dataset.modal;
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
            }
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach(closer => {
        closer.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                modal.style.display = 'none';
            }
        });
    });

    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    });

    // Click on calendar cell to open quick booking
    document.querySelectorAll('.availability-cell[data-status="available"]').forEach(cell => {
        cell.addEventListener('click', function() {
            const roomId = this.dataset.room;
            const date = this.dataset.date;

            const modal = document.getElementById('quickBookingModal');
            const roomSelect = document.getElementById('modalRoom');
            const checkInInput = document.getElementById('modalCheckIn');
            const checkOutInput = document.getElementById('modalCheckOut');

            if (roomSelect) roomSelect.value = roomId;
            if (checkInInput) checkInInput.value = date;
            if (checkOutInput) {
                const nextDay = new Date(date);
                nextDay.setDate(nextDay.getDate() + 1);
                checkOutInput.value = nextDay.toISOString().split('T')[0];
            }

            if (modal) modal.style.display = 'flex';
        });
    });

    // Click on booking bar to view booking
    document.querySelectorAll('.booking-bar').forEach(bar => {
        bar.addEventListener('click', function(e) {
            e.stopPropagation();
            const bookingId = this.dataset.bookingId;
            if (bookingId) {
                window.location.href = `/admin/bookings/${bookingId}`;
            }
        });
    });
});
</script>
@endpush
