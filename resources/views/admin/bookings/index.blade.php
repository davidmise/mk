@extends('admin.layouts.app')

@section('title', 'Bookings')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>Bookings</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Booking Management</h1>
        <p class="page-subtitle">Manage all hotel reservations and bookings</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.calendar.index') }}" class="btn btn-secondary">
            <i class="fas fa-calendar"></i> Calendar View
        </a>
        <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Booking
        </a>
    </div>
</div>

<!-- Quick Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Pending Confirmation</div>
            <div class="stat-value">{{ $stats['pending'] ?? 0 }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Confirmed</div>
            <div class="stat-value">{{ $stats['confirmed'] ?? 0 }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon cyan">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Checked In</div>
            <div class="stat-value">{{ $stats['checked_in'] ?? 0 }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Today's Arrivals</div>
            <div class="stat-value">{{ $stats['arrivals_today'] ?? 0 }}</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.bookings.index') }}" method="GET" class="d-flex gap-3 align-center" style="flex-wrap: wrap;">
            <div class="form-group mb-0" style="min-width: 200px;">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Search by reference, guest name..."
                    value="{{ request('search') }}"
                >
            </div>

            <div class="form-group mb-0">
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="checked_in" {{ request('status') === 'checked_in' ? 'selected' : '' }}>Checked In</option>
                    <option value="checked_out" {{ request('status') === 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="form-group mb-0">
                <select name="room_type_id" class="form-control">
                    <option value="">All Room Types</option>
                    @foreach($roomTypes ?? [] as $type)
                        <option value="{{ $type->id }}" {{ request('room_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-0">
                <input
                    type="date"
                    name="check_in_date"
                    class="form-control"
                    placeholder="Check-in Date"
                    value="{{ request('check_in_date') }}"
                >
            </div>

            <div class="form-group mb-0">
                <input
                    type="date"
                    name="check_out_date"
                    class="form-control"
                    placeholder="Check-out Date"
                    value="{{ request('check_out_date') }}"
                >
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Filter
            </button>

            @if(request()->hasAny(['search', 'status', 'room_type_id', 'check_in_date', 'check_out_date']))
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </form>
    </div>
</div>

<!-- Bookings Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Guests</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr>
                        <td>
                            <a href="{{ route('admin.bookings.show', $booking) }}" style="color: var(--primary); font-weight: 600;">
                                {{ $booking->booking_reference }}
                            </a>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                {{ ucfirst($booking->source) }}
                            </div>
                        </td>
                        <td>
                            <strong>{{ $booking->guest?->full_name ?? $booking->guest_name }}</strong>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                {{ $booking->guest?->email ?? $booking->guest_email }}
                            </div>
                        </td>
                        <td>
                            {{ $booking->roomType->name ?? '-' }}
                            @if($booking->room)
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    Room {{ $booking->room->room_number }}
                                </div>
                            @endif
                        </td>
                        <td>
                            {{ $booking->check_in_date->format('M d, Y') }}
                            @if($booking->check_in_date->isToday())
                                <span class="badge badge-success" style="font-size: 0.625rem;">Today</span>
                            @endif
                        </td>
                        <td>
                            {{ $booking->check_out_date->format('M d, Y') }}
                            <div class="text-muted" style="font-size: 0.75rem;">
                                {{ $booking->total_nights }} nights
                            </div>
                        </td>
                        <td>
                            {{ $booking->adults }} adults
                            @if($booking->children > 0)
                                , {{ $booking->children }} children
                            @endif
                        </td>
                        <td>
                            <strong>${{ number_format($booking->total_amount, 2) }}</strong>
                            @php $balance = $booking->total_amount - $booking->paid_amount @endphp
                            @if($balance > 0)
                                <div class="text-danger" style="font-size: 0.75rem;">
                                    Due: ${{ number_format($balance, 2) }}
                                </div>
                            @else
                                <div class="text-success" style="font-size: 0.75rem;">Paid</div>
                            @endif
                        </td>
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
                                @case('no_show')
                                    <span class="badge badge-danger">No Show</span>
                                    @break
                            @endswitch
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-secondary btn-icon" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if($booking->status === 'pending')
                                    <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success btn-icon" title="Confirm">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif

                                @if($booking->status === 'confirmed')
                                    <form action="{{ route('admin.bookings.check-in', $booking) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary btn-icon" title="Check In">
                                            <i class="fas fa-sign-in-alt"></i>
                                        </button>
                                    </form>
                                @endif

                                @if($booking->status === 'checked_in')
                                    <form action="{{ route('admin.bookings.check-out', $booking) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning btn-icon" title="Check Out">
                                            <i class="fas fa-sign-out-alt"></i>
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-sm btn-secondary btn-icon" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted" style="padding: 3rem;">
                            <i class="fas fa-calendar-times" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                            No bookings found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($bookings->hasPages())
    <div class="card-body" style="border-top: 1px solid var(--gray-200);">
        {{ $bookings->links() }}
    </div>
    @endif
</div>
@endsection
