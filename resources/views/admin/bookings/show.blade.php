@extends('admin.layouts.app')

@section('title', 'Booking #' . $booking->booking_reference)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <a href="{{ route('admin.bookings.index') }}">Bookings</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>{{ $booking->booking_reference }}</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Booking {{ $booking->booking_reference }}</h1>
        <p class="page-subtitle">
            Created {{ $booking->created_at->format('M d, Y \a\t h:i A') }}
            @if($booking->createdBy) by {{ $booking->createdBy->name }} @endif
        </p>
    </div>
    <div class="d-flex gap-2">
        @switch($booking->status)
            @case('pending')
                <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Confirm Booking
                    </button>
                </form>
                @break
            @case('confirmed')
                <form action="{{ route('admin.bookings.check-in', $booking) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Check In
                    </button>
                </form>
                @break
            @case('checked_in')
                <form action="{{ route('admin.bookings.check-out', $booking) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-sign-out-alt"></i> Check Out
                    </button>
                </form>
                @break
        @endswitch

        <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-secondary">
            <i class="fas fa-edit"></i> Edit
        </a>

        @if(!in_array($booking->status, ['checked_out', 'cancelled']))
            <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" style="display: inline;"
                  onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                @csrf
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </form>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-8">
        <!-- Booking Status -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-between align-center">
                    <div class="d-flex align-center gap-3">
                        @switch($booking->status)
                            @case('pending')
                                <div class="stat-icon orange" style="width: 56px; height: 56px;">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <span class="badge badge-warning" style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">Pending Confirmation</span>
                                    <p class="text-muted mb-0 mt-1">Awaiting confirmation from staff</p>
                                </div>
                                @break
                            @case('confirmed')
                                <div class="stat-icon green" style="width: 56px; height: 56px;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <span class="badge badge-success" style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">Confirmed</span>
                                    <p class="text-muted mb-0 mt-1">Ready for check-in on {{ $booking->check_in_date->format('M d, Y') }}</p>
                                </div>
                                @break
                            @case('checked_in')
                                <div class="stat-icon cyan" style="width: 56px; height: 56px;">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div>
                                    <span class="badge badge-info" style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">Checked In</span>
                                    <p class="text-muted mb-0 mt-1">
                                        Guest is currently staying
                                        @if($booking->room) in Room {{ $booking->room->room_number }} @endif
                                    </p>
                                </div>
                                @break
                            @case('checked_out')
                                <div class="stat-icon blue" style="width: 56px; height: 56px;">
                                    <i class="fas fa-door-open"></i>
                                </div>
                                <div>
                                    <span class="badge badge-secondary" style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">Checked Out</span>
                                    <p class="text-muted mb-0 mt-1">Completed on {{ $booking->actual_check_out?->format('M d, Y h:i A') ?? $booking->check_out_date->format('M d, Y') }}</p>
                                </div>
                                @break
                            @case('cancelled')
                                <div class="stat-icon red" style="width: 56px; height: 56px;">
                                    <i class="fas fa-ban"></i>
                                </div>
                                <div>
                                    <span class="badge badge-danger" style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">Cancelled</span>
                                    <p class="text-muted mb-0 mt-1">{{ $booking->cancellation_reason ?? 'No reason provided' }}</p>
                                </div>
                                @break
                        @endswitch
                    </div>

                    <div class="text-right">
                        <div class="text-muted" style="font-size: 0.75rem;">Source</div>
                        <strong>{{ ucfirst(str_replace('_', '.', $booking->source)) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stay Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Stay Details</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-4">
                        <div class="mb-3">
                            <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 0.25rem;">CHECK-IN</div>
                            <strong style="font-size: 1.125rem;">{{ $booking->check_in_date->format('M d, Y') }}</strong>
                            <div class="text-muted">After 2:00 PM</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 0.25rem;">CHECK-OUT</div>
                            <strong style="font-size: 1.125rem;">{{ $booking->check_out_date->format('M d, Y') }}</strong>
                            <div class="text-muted">Before 11:00 AM</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 0.25rem;">DURATION</div>
                            <strong style="font-size: 1.125rem;">{{ $booking->total_nights }} Nights</strong>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 0.25rem;">ROOM TYPE</div>
                            <strong>{{ $booking->roomType->name ?? '-' }}</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 0.25rem;">ASSIGNED ROOM</div>
                            @if($booking->room)
                                <strong>Room {{ $booking->room->room_number }}</strong>
                                <span class="text-muted">(Floor {{ $booking->room->floor }})</span>
                            @else
                                <span class="text-muted">Not assigned</span>
                                @if($booking->status === 'confirmed')
                                    <a href="#" class="btn btn-sm btn-primary" style="margin-left: 0.5rem;">Assign Room</a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 0.25rem;">GUESTS</div>
                            <strong>{{ $booking->adults }} Adults</strong>
                            @if($booking->children > 0)
                                <span>, {{ $booking->children }} Children</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($booking->special_requests)
                <hr>
                <div>
                    <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 0.25rem;">SPECIAL REQUESTS</div>
                    <p class="mb-0">{{ $booking->special_requests }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Guest Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Guest Information</h3>
                @if($booking->guest)
                    <a href="{{ route('admin.guests.show', $booking->guest) }}" class="btn btn-sm btn-secondary">View Profile</a>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 0.25rem;">NAME</div>
                            <strong>{{ $booking->guest?->full_name ?? $booking->guest_name }}</strong>
                            @if($booking->guest?->vip_status && $booking->guest->vip_status !== 'regular')
                                <span class="badge badge-warning" style="margin-left: 0.5rem;">
                                    <i class="fas fa-crown"></i> {{ ucfirst($booking->guest->vip_status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 0.25rem;">PHONE</div>
                            <strong>{{ $booking->guest?->phone ?? $booking->guest_phone }}</strong>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 0.25rem;">EMAIL</div>
                            <strong>{{ $booking->guest?->email ?? $booking->guest_email ?? '-' }}</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 0.25rem;">NATIONALITY</div>
                            <strong>{{ $booking->guest?->nationality ?? '-' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Payment History</h3>
                @if(!in_array($booking->status, ['checked_out', 'cancelled']))
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addPaymentModal">
                        <i class="fas fa-plus"></i> Add Payment
                    </button>
                @endif
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($booking->payments ?? [] as $payment)
                        <tr>
                            <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                            <td>{{ $payment->payment_reference }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                            <td><strong>${{ number_format($payment->amount, 2) }}</strong></td>
                            <td>
                                @if($payment->status === 'completed')
                                    <span class="badge badge-success">Completed</span>
                                @elseif($payment->status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @else
                                    <span class="badge badge-danger">{{ ucfirst($payment->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No payments recorded</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar - Financial Summary -->
    <div class="col-4">
        <div class="card mb-4" style="position: sticky; top: 80px;">
            <div class="card-header">
                <h3 class="card-title">Financial Summary</h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-between mb-2">
                    <span class="text-muted">Room Rate ({{ $booking->total_nights }} nights)</span>
                    <span>${{ number_format($booking->room_rate * $booking->total_nights, 2) }}</span>
                </div>

                @if($booking->extras_amount > 0)
                <div class="d-flex justify-between mb-2">
                    <span class="text-muted">Extra Services</span>
                    <span>${{ number_format($booking->extras_amount, 2) }}</span>
                </div>
                @endif

                <div class="d-flex justify-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span>${{ number_format($booking->subtotal, 2) }}</span>
                </div>

                @if($booking->discount_amount > 0)
                <div class="d-flex justify-between mb-2 text-success">
                    <span>Discount</span>
                    <span>-${{ number_format($booking->discount_amount, 2) }}</span>
                </div>
                @endif

                <div class="d-flex justify-between mb-2">
                    <span class="text-muted">Tax</span>
                    <span>${{ number_format($booking->tax_amount, 2) }}</span>
                </div>

                <hr>

                <div class="d-flex justify-between mb-3">
                    <strong>Total Amount</strong>
                    <strong style="font-size: 1.25rem;">${{ number_format($booking->total_amount, 2) }}</strong>
                </div>

                <div class="d-flex justify-between mb-2">
                    <span class="text-muted">Paid Amount</span>
                    <span class="text-success">${{ number_format($booking->paid_amount, 2) }}</span>
                </div>

                @php $balance = $booking->total_amount - $booking->paid_amount @endphp
                <div class="d-flex justify-between">
                    <strong>Balance Due</strong>
                    <strong class="{{ $balance > 0 ? 'text-danger' : 'text-success' }}" style="font-size: 1.125rem;">
                        ${{ number_format(abs($balance), 2) }}
                        @if($balance < 0) (Overpaid) @endif
                    </strong>
                </div>

                @if($balance > 0 && !in_array($booking->status, ['checked_out', 'cancelled']))
                <hr>
                <button type="button" class="btn btn-primary w-100" data-toggle="modal" data-target="#addPaymentModal">
                    <i class="fas fa-credit-card"></i> Record Payment
                </button>
                @endif
            </div>
        </div>

        <!-- Activity Log -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Activity Log</h3>
            </div>
            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                @forelse($booking->activityLogs ?? [] as $log)
                <div class="d-flex gap-3 mb-3">
                    <div class="stat-icon {{ $log->action === 'created' ? 'green' : 'blue' }}" style="width: 32px; height: 32px; font-size: 0.75rem;">
                        <i class="fas fa-{{ $log->action === 'created' ? 'plus' : 'edit' }}"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.8125rem;">{{ $log->description }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">
                            {{ $log->user?->name ?? 'System' }} • {{ $log->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center mb-0">No activity recorded</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
