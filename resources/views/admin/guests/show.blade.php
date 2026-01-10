@extends('admin.layouts.app')

@section('title', 'Guest Profile: ' . $guest->full_name)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <a href="{{ route('admin.guests.index') }}">Guests</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>{{ $guest->full_name }}</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $guest->full_name }}</h1>
        <p class="page-subtitle">
            Guest since {{ $guest->created_at->format('M Y') }}
            @if($guest->is_vip)
                <span class="badge badge-warning ml-2"><i class="fas fa-star"></i> VIP</span>
            @endif
        </p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.guests.edit', $guest) }}" class="btn btn-secondary">
            <i class="fas fa-edit"></i> Edit Profile
        </a>
        <a href="{{ route('admin.bookings.create', ['guest_id' => $guest->id]) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Booking
        </a>
    </div>
</div>

<div class="row">
    <div class="col-4">
        <!-- Guest Card -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="guest-avatar-large">
                    @if($guest->avatar)
                        <img src="{{ asset('storage/' . $guest->avatar) }}" alt="{{ $guest->full_name }}">
                    @else
                        {{ strtoupper(substr($guest->first_name, 0, 1) . substr($guest->last_name, 0, 1)) }}
                    @endif
                </div>
                <h3 class="mt-3 mb-1">{{ $guest->full_name }}</h3>
                @if($guest->company)
                    <p class="text-muted">{{ $guest->company }}</p>
                @endif

                <div class="guest-badges mt-3">
                    @if($guest->is_vip)
                        <span class="badge badge-warning"><i class="fas fa-star"></i> VIP Guest</span>
                    @endif
                    @if($guest->loyalty_tier)
                        <span class="badge badge-primary">{{ $guest->loyalty_tier }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Contact Information</h3>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <small class="text-muted">Email</small>
                        <a href="mailto:{{ $guest->email }}">{{ $guest->email }}</a>
                    </div>
                </div>

                @if($guest->phone)
                <div class="info-row">
                    <i class="fas fa-phone"></i>
                    <div>
                        <small class="text-muted">Phone</small>
                        <a href="tel:{{ $guest->phone }}">{{ $guest->phone }}</a>
                    </div>
                </div>
                @endif

                @if($guest->address)
                <div class="info-row">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <small class="text-muted">Address</small>
                        <span>
                            {{ $guest->address }}<br>
                            {{ $guest->city }}{{ $guest->state ? ', ' . $guest->state : '' }} {{ $guest->postal_code }}<br>
                            {{ $guest->country }}
                        </span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Identification -->
        @if($guest->id_type || $guest->id_number)
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Identification</h3>
            </div>
            <div class="card-body">
                @if($guest->id_type)
                <div class="info-row">
                    <i class="fas fa-id-card"></i>
                    <div>
                        <small class="text-muted">ID Type</small>
                        <span>{{ ucfirst($guest->id_type) }}</span>
                    </div>
                </div>
                @endif

                @if($guest->id_number)
                <div class="info-row">
                    <i class="fas fa-hashtag"></i>
                    <div>
                        <small class="text-muted">ID Number</small>
                        <span>{{ $guest->id_number }}</span>
                    </div>
                </div>
                @endif

                @if($guest->nationality)
                <div class="info-row">
                    <i class="fas fa-globe"></i>
                    <div>
                        <small class="text-muted">Nationality</small>
                        <span>{{ $guest->nationality }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Notes -->
        @if($guest->notes || $guest->preferences)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Notes & Preferences</h3>
            </div>
            <div class="card-body">
                @if($guest->preferences)
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Preferences</small>
                    <p class="mb-0">{{ $guest->preferences }}</p>
                </div>
                @endif

                @if($guest->notes)
                <div>
                    <small class="text-muted d-block mb-1">Internal Notes</small>
                    <p class="mb-0">{{ $guest->notes }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <div class="col-8">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-4">
                <div class="stats-card">
                    <div class="stats-icon" style="background: #e3f2fd; color: #1976d2;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stats-info">
                        <h3>{{ $stats['total_bookings'] ?? 0 }}</h3>
                        <p>Total Stays</p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="stats-card">
                    <div class="stats-icon" style="background: #e8f5e9; color: #388e3c;">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stats-info">
                        <h3>${{ number_format($stats['total_spent'] ?? 0, 0) }}</h3>
                        <p>Total Spent</p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="stats-card">
                    <div class="stats-icon" style="background: #fff3e0; color: #f57c00;">
                        <i class="fas fa-moon"></i>
                    </div>
                    <div class="stats-info">
                        <h3>{{ $stats['total_nights'] ?? 0 }}</h3>
                        <p>Total Nights</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking History -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-between align-items-center">
                <h3 class="card-title mb-0">Booking History</h3>
                <a href="{{ route('admin.bookings.index', ['guest' => $guest->id]) }}" class="btn btn-secondary btn-sm">
                    View All
                </a>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Booking #</th>
                            <th>Room</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings ?? [] as $booking)
                        <tr>
                            <td><strong>{{ $booking->booking_number }}</strong></td>
                            <td>{{ $booking->room->room_number ?? 'N/A' }}</td>
                            <td>{{ $booking->check_in->format('M d, Y') }}</td>
                            <td>{{ $booking->check_out->format('M d, Y') }}</td>
                            <td>
                                @switch($booking->status)
                                    @case('confirmed')
                                        <span class="badge badge-info">Confirmed</span>
                                        @break
                                    @case('checked_in')
                                        <span class="badge badge-success">Checked In</span>
                                        @break
                                    @case('checked_out')
                                        <span class="badge badge-secondary">Completed</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge badge-danger">Cancelled</span>
                                        @break
                                    @default
                                        <span class="badge badge-warning">{{ ucfirst($booking->status) }}</span>
                                @endswitch
                            </td>
                            <td>${{ number_format($booking->total_amount, 2) }}</td>
                            <td>
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No bookings yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Activity Timeline</h3>
            </div>
            <div class="card-body">
                <div class="activity-timeline">
                    @forelse($activities ?? [] as $activity)
                    <div class="timeline-item">
                        <div class="timeline-icon">
                            @switch($activity->type)
                                @case('booking')
                                    <i class="fas fa-calendar-plus" style="color: var(--primary);"></i>
                                    @break
                                @case('check_in')
                                    <i class="fas fa-sign-in-alt" style="color: var(--success);"></i>
                                    @break
                                @case('check_out')
                                    <i class="fas fa-sign-out-alt" style="color: var(--info);"></i>
                                    @break
                                @case('payment')
                                    <i class="fas fa-credit-card" style="color: var(--success);"></i>
                                    @break
                                @case('note')
                                    <i class="fas fa-sticky-note" style="color: var(--warning);"></i>
                                    @break
                                @default
                                    <i class="fas fa-circle" style="color: var(--gray-400);"></i>
                            @endswitch
                        </div>
                        <div class="timeline-content">
                            <p class="mb-1">{{ $activity->description }}</p>
                            <small class="text-muted">
                                {{ $activity->created_at->format('M d, Y H:i') }}
                                @if($activity->user)
                                    • by {{ $activity->user->name }}
                                @endif
                            </small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-history" style="font-size: 2rem; opacity: 0.3;"></i>
                        <p class="mt-2 mb-0">No activity recorded</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.guest-avatar-large {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 600;
    margin: 0 auto;
    overflow: hidden;
}

.guest-avatar-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.guest-badges {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

.info-row {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--gray-100);
}

.info-row:last-child {
    border-bottom: none;
}

.info-row i {
    width: 20px;
    text-align: center;
    color: var(--gray-400);
    margin-top: 0.25rem;
}

.info-row small {
    display: block;
}

.info-row a {
    color: var(--primary);
    text-decoration: none;
}

.stats-card {
    background: white;
    border-radius: 0.5rem;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.stats-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.stats-info h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
}

.stats-info p {
    margin: 0;
    color: var(--gray-500);
    font-size: 0.75rem;
}

.activity-timeline {
    position: relative;
    padding-left: 2rem;
}

.activity-timeline::before {
    content: '';
    position: absolute;
    left: 0.5rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--gray-200);
}

.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-icon {
    position: absolute;
    left: -2rem;
    width: 1rem;
    height: 1rem;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
}

.timeline-content {
    background: var(--gray-50);
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;
}
</style>
@endsection
