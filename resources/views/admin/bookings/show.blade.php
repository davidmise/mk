@extends('admin.layouts.app')

@section('title', 'Booking ' . $booking->booking_reference)

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
        <p class="page-subtitle">Created {{ $booking->created_at->format('M d, Y \a\t h:i A') }}</p>
    </div>
    <div class="d-flex gap-2">
        @if($booking->status === 'checked_in')
            <button type="button" class="btn btn-warning ajax-action-btn" data-action="{{ route('admin.bookings.check-out', $booking) }}">
                <i class="fas fa-sign-out-alt"></i> Check Out
            </button>
        @endif
        <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-secondary">
            <i class="fas fa-edit"></i> Edit
        </a>
        @if(!in_array($booking->status, ['checked_out', 'cancelled']))
            <button type="button" class="btn btn-danger" onclick="confirmCancel()">
                <i class="fas fa-times"></i> Cancel
            </button>
        @endif
    </div>
</div>

<div class="booking-detail-grid">
    <!-- Main Content -->
    <div class="booking-main">
        <!-- Status Card -->
        <div class="card status-card mb-4">
            <div class="status-banner {{ $booking->status }}">
                <div class="status-icon-wrapper">
                    @switch($booking->status)
                        @case('pending')
                            <i class="fas fa-clock"></i>
                        @break
                        @case('confirmed')
                            <i class="fas fa-check-circle"></i>
                        @break
                        @case('checked_in')
                            <i class="fas fa-user-check"></i>
                        @break
                        @case('checked_out')
                            <i class="fas fa-door-open"></i>
                        @break
                        @case('cancelled')
                            <i class="fas fa-ban"></i>
                        @break
                    @endswitch
                </div>
                <div class="status-info">
                    <span class="status-badge {{ $booking->status }}">
                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                    </span>
                    <p class="status-message">
                        @switch($booking->status)
                            @case('pending')
                                Awaiting confirmation
                            @break
                            @case('confirmed')
                                Ready for check-in
                            @break
                            @case('checked_in')
                                Guest is currently staying
                            @break
                            @case('checked_out')
                                Stay completed
                            @break
                            @case('cancelled')
                                {{ $booking->cancellation_reason ?? 'Booking was cancelled' }}
                            @break
                        @endswitch
                    </p>
                </div>
                <div class="status-source">
                    <span class="source-label">Source</span>
                    <span class="source-value">{{ ucfirst(str_replace('_', ' ', $booking->source ?? 'website')) }}</span>
                </div>
            </div>

            @if(in_array($booking->status, ['pending', 'confirmed']))
            <div class="status-actions">
                @if($booking->status === 'pending')
                    <button type="button" class="btn btn-success btn-lg ajax-action-btn" data-action="{{ route('admin.bookings.confirm', $booking) }}">
                        <i class="fas fa-check"></i> Confirm Booking
                    </button>
                @endif
                @if($booking->status === 'confirmed')
                    <button type="button" class="btn btn-primary btn-lg ajax-action-btn" data-action="{{ route('admin.bookings.check-in', $booking) }}">
                        <i class="fas fa-sign-in-alt"></i> Check In Guest
                    </button>
                @endif
            </div>
            @endif
        </div>

        <!-- Stay Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bed"></i> Stay Details</h3>
            </div>
            <div class="card-body">
                <div class="stay-details-grid">
                    <div class="stay-date-card checkin">
                        <div class="date-label">CHECK-IN</div>
                        <div class="date-value">{{ $booking->check_in ? $booking->check_in->format('D, M d, Y') : '-' }}</div>
                        <div class="date-time">After 2:00 PM</div>
                    </div>
                    <div class="stay-duration">
                        <div class="duration-line"></div>
                        <div class="duration-badge">
                            <span class="nights-count">{{ $booking->total_nights ?? 1 }}</span>
                            <span class="nights-label">{{ ($booking->total_nights ?? 1) == 1 ? 'Night' : 'Nights' }}</span>
                        </div>
                        <div class="duration-line"></div>
                    </div>
                    <div class="stay-date-card checkout">
                        <div class="date-label">CHECK-OUT</div>
                        <div class="date-value">{{ $booking->check_out ? $booking->check_out->format('D, M d, Y') : '-' }}</div>
                        <div class="date-time">Before 11:00 AM</div>
                    </div>
                </div>

                <div class="room-info-grid">
                    <div class="room-info-item">
                        <span class="info-label">Room Type</span>
                        <span class="info-value">{{ $booking->roomType->name ?? '-' }}</span>
                    </div>
                    <div class="room-info-item">
                        <span class="info-label">Assigned Room</span>
                        <span class="info-value">
                            @if($booking->room)
                                Room {{ $booking->room->room_number }} (Floor {{ $booking->room->floor }})
                            @else
                                <span class="text-muted">Not assigned</span>
                            @endif
                        </span>
                    </div>
                    <div class="room-info-item">
                        <span class="info-label">Guests</span>
                        <span class="info-value">
                            {{ $booking->adults }} {{ $booking->adults == 1 ? 'Adult' : 'Adults' }}
                            @if($booking->children > 0)
                                , {{ $booking->children }} {{ $booking->children == 1 ? 'Child' : 'Children' }}
                            @endif
                        </span>
                    </div>
                    <div class="room-info-item">
                        <span class="info-label">Rooms Booked</span>
                        <span class="info-value">{{ $booking->number_of_rooms ?? 1 }}</span>
                    </div>
                </div>

                @if($booking->special_requests)
                <div class="special-requests">
                    <span class="info-label">Special Requests</span>
                    <p class="requests-text">{{ $booking->special_requests }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Guest Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user"></i> Guest Information</h3>
                @if($booking->guest)
                    <a href="{{ route('admin.guests.show', $booking->guest) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-external-link-alt"></i> View Profile
                    </a>
                @endif
            </div>
            <div class="card-body">
                <div class="guest-info-grid">
                    <div class="guest-info-item">
                        <span class="info-label">Full Name</span>
                        <span class="info-value guest-name">
                            {{ $booking->guest?->full_name ?? $booking->guest_name }}
                            @if($booking->guest?->vip_status && $booking->guest->vip_status !== 'regular')
                                <span class="vip-badge">
                                    <i class="fas fa-crown"></i> {{ ucfirst($booking->guest->vip_status) }}
                                </span>
                            @endif
                        </span>
                    </div>
                    <div class="guest-info-item">
                        <span class="info-label">Phone Number</span>
                        <span class="info-value">
                            <a href="tel:{{ $booking->guest?->phone ?? $booking->guest_phone }}">
                                {{ $booking->guest?->phone ?? $booking->guest_phone ?? '-' }}
                            </a>
                        </span>
                    </div>
                    <div class="guest-info-item">
                        <span class="info-label">Email Address</span>
                        <span class="info-value">
                            @if($booking->guest?->email ?? $booking->guest_email)
                                <a href="mailto:{{ $booking->guest?->email ?? $booking->guest_email }}">
                                    {{ $booking->guest?->email ?? $booking->guest_email }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </span>
                    </div>
                    <div class="guest-info-item">
                        <span class="info-label">Nationality</span>
                        <span class="info-value">{{ $booking->guest?->nationality ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history"></i> Payment History</h3>
                @if(!in_array($booking->status, ['checked_out', 'cancelled']))
                    <button type="button" class="btn btn-sm btn-primary" onclick="openPaymentModal()">
                        <i class="fas fa-plus"></i> Add Payment
                    </button>
                @endif
            </div>
            <div class="card-body p-0">
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
                            <td><code>{{ $payment->payment_reference ?? $payment->reference ?? '-' }}</code></td>
                            <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? $payment->method ?? '-')) }}</td>
                            <td><strong>TZS {{ number_format($payment->amount, 0) }}</strong></td>
                            <td>
                                <span class="badge badge-{{ ($payment->status ?? 'completed') === 'completed' ? 'success' : (($payment->status ?? '') === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($payment->status ?? 'Completed') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-receipt" style="font-size: 2rem; opacity: 0.3; display: block; margin-bottom: 0.5rem;"></i>
                                No payments recorded yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="booking-sidebar">
        <!-- Financial Summary -->
        <div class="card financial-card mb-4">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calculator"></i> Financial Summary</h3>
            </div>
            <div class="card-body">
                <div class="financial-breakdown">
                    <div class="breakdown-row">
                        <span>Room Rate ({{ $booking->total_nights ?? 1 }} nights)</span>
                        <span>TZS {{ number_format(($booking->room_rate ?? 0) * ($booking->total_nights ?? 1), 0) }}</span>
                    </div>
                    @if(($booking->extras_amount ?? 0) > 0)
                    <div class="breakdown-row">
                        <span>Extra Services</span>
                        <span>TZS {{ number_format($booking->extras_amount, 0) }}</span>
                    </div>
                    @endif
                    <div class="breakdown-row subtotal">
                        <span>Subtotal</span>
                        <span>TZS {{ number_format($booking->subtotal ?? (($booking->room_rate ?? 0) * ($booking->total_nights ?? 1)), 0) }}</span>
                    </div>
                    @if(($booking->discount_amount ?? 0) > 0)
                    <div class="breakdown-row discount">
                        <span>Discount</span>
                        <span class="text-success">-TZS {{ number_format($booking->discount_amount, 0) }}</span>
                    </div>
                    @endif
                    <div class="breakdown-row">
                        <span>Tax</span>
                        <span>TZS {{ number_format($booking->tax_amount ?? 0, 0) }}</span>
                    </div>
                </div>

                <div class="financial-total">
                    <div class="total-row">
                        <span>Total Amount</span>
                        <span class="total-value">TZS {{ number_format($booking->total_amount ?? $booking->total_price ?? 0, 0) }}</span>
                    </div>
                    <div class="paid-row">
                        <span>Paid Amount</span>
                        <span class="text-success">TZS {{ number_format($booking->paid_amount ?? $booking->amount_paid ?? 0, 0) }}</span>
                    </div>
                    @php
                        $totalAmt = $booking->total_amount ?? $booking->total_price ?? 0;
                        $paidAmt = $booking->paid_amount ?? $booking->amount_paid ?? 0;
                        $balance = $totalAmt - $paidAmt;
                    @endphp
                    <div class="balance-row {{ $balance > 0 ? 'outstanding' : 'settled' }}">
                        <span>Balance Due</span>
                        <span class="balance-value">
                            TZS {{ number_format(abs($balance), 0) }}
                            @if($balance < 0) <small>(Overpaid)</small> @endif
                        </span>
                    </div>
                </div>

                @if($balance > 0 && !in_array($booking->status, ['checked_out', 'cancelled']))
                <button type="button" class="btn btn-primary btn-block mt-3" onclick="openPaymentModal()">
                    <i class="fas fa-credit-card"></i> Record Payment
                </button>
                @endif
            </div>
        </div>

        <!-- Activity Log -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list-alt"></i> Activity Log</h3>
            </div>
            <div class="card-body activity-log-body">
                @forelse($booking->activityLogs ?? [] as $log)
                <div class="activity-item">
                    <div class="activity-icon {{ $log->action === 'created' ? 'green' : 'blue' }}">
                        <i class="fas fa-{{ $log->action === 'created' ? 'plus' : 'edit' }}"></i>
                    </div>
                    <div class="activity-content">
                        <p class="activity-text">{{ $log->description }}</p>
                        <span class="activity-meta">{{ $log->user?->name ?? 'System' }} • {{ $log->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-3">
                    <i class="fas fa-clock" style="font-size: 1.5rem; opacity: 0.3; display: block; margin-bottom: 0.5rem;"></i>
                    No activity recorded
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 class="modal-title">Record Payment</h3>
            <button type="button" class="modal-close" onclick="closePaymentModal()">&times;</button>
        </div>
        <form action="{{ route('admin.payments.store') }}" method="POST" id="paymentForm">
            @csrf
            <input type="hidden" name="booking_id" value="{{ $booking->id }}">
            <input type="hidden" name="guest_id" value="{{ $booking->guest_id }}">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Amount <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">TZS</span>
                        <input type="number" name="amount" class="form-control" required min="1"
                               value="{{ $balance > 0 ? $balance : '' }}" placeholder="Enter amount">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                    <select name="method" class="form-control" required>
                        <option value="cash">Cash</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="debit_card">Debit Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="mobile_money">Mobile Money</option>
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">Reference (Optional)</label>
                    <input type="text" name="reference" class="form-control" placeholder="Transaction reference">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closePaymentModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> Record Payment
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Cancel Confirmation Form (Hidden) -->
<form id="cancelForm" action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="cancellation_reason" value="Cancelled by staff">
</form>

<!-- Toast Container -->
<div id="toast-container"></div>

<style>
.booking-detail-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 1.5rem;
}

.booking-main { min-width: 0; }

.booking-sidebar {
    position: sticky;
    top: 80px;
    align-self: start;
}

.status-card { overflow: hidden; }

.status-banner {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
}

.status-banner.pending { background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); }
.status-banner.confirmed { background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); }
.status-banner.checked_in { background: linear-gradient(135deg, #ecfeff 0%, #cffafe 100%); }
.status-banner.checked_out { background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); }
.status-banner.cancelled { background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); }

.status-icon-wrapper {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.status-banner.pending .status-icon-wrapper { color: #f97316; }
.status-banner.confirmed .status-icon-wrapper { color: #22c55e; }
.status-banner.checked_in .status-icon-wrapper { color: #06b6d4; }
.status-banner.checked_out .status-icon-wrapper { color: #64748b; }
.status-banner.cancelled .status-icon-wrapper { color: #ef4444; }

.status-info { flex: 1; }

.status-badge {
    display: inline-block;
    padding: 0.375rem 0.875rem;
    border-radius: 50px;
    font-size: 0.8125rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.status-badge.pending { background: #f97316; color: white; }
.status-badge.confirmed { background: #22c55e; color: white; }
.status-badge.checked_in { background: #06b6d4; color: white; }
.status-badge.checked_out { background: #64748b; color: white; }
.status-badge.cancelled { background: #ef4444; color: white; }

.status-message {
    margin: 0.375rem 0 0;
    color: var(--gray-600);
    font-size: 0.875rem;
}

.status-source { text-align: right; }

.source-label {
    display: block;
    font-size: 0.6875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--gray-500);
    margin-bottom: 0.125rem;
}

.source-value {
    font-weight: 600;
    color: var(--gray-800);
}

.status-actions {
    padding: 1rem 1.5rem;
    background: white;
    border-top: 1px solid var(--gray-100);
}

.status-actions .btn {
    width: 100%;
    padding: 0.875rem;
    font-size: 1rem;
}

.stay-details-grid {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stay-date-card {
    flex: 1;
    padding: 1rem 1.25rem;
    border-radius: 12px;
    text-align: center;
}

.stay-date-card.checkin { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); }
.stay-date-card.checkout { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); }

.date-label {
    font-size: 0.6875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--gray-600);
    margin-bottom: 0.375rem;
}

.date-value {
    font-size: 1rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 0.125rem;
}

.date-time {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.stay-duration {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0 0.5rem;
}

.duration-line {
    width: 20px;
    height: 2px;
    background: var(--gray-300);
}

.duration-badge {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0.5rem 0.75rem;
    background: var(--gray-100);
    border-radius: 8px;
}

.nights-count {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary);
    line-height: 1;
}

.nights-label {
    font-size: 0.625rem;
    text-transform: uppercase;
    color: var(--gray-500);
}

.room-info-grid, .guest-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.room-info-item, .guest-info-item {
    padding: 0.75rem;
    background: var(--gray-50);
    border-radius: 8px;
}

.info-label {
    display: block;
    font-size: 0.6875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--gray-500);
    margin-bottom: 0.25rem;
}

.info-value {
    font-size: 0.9375rem;
    font-weight: 500;
    color: var(--gray-900);
}

.special-requests {
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid var(--gray-100);
}

.requests-text {
    margin: 0.5rem 0 0;
    padding: 0.75rem;
    background: var(--gray-50);
    border-radius: 8px;
    font-size: 0.875rem;
    color: var(--gray-700);
}

.guest-name {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.vip-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.125rem 0.5rem;
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: white;
    font-size: 0.6875rem;
    font-weight: 600;
    border-radius: 50px;
}

.financial-card .card-body { padding: 1.25rem; }

.financial-breakdown { margin-bottom: 1rem; }

.breakdown-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    font-size: 0.875rem;
    color: var(--gray-600);
    border-bottom: 1px dashed var(--gray-200);
}

.breakdown-row span:last-child {
    font-weight: 500;
    color: var(--gray-800);
}

.breakdown-row.subtotal { border-bottom-style: solid; }
.breakdown-row.discount span:last-child { color: #22c55e; }

.financial-total {
    background: var(--gray-50);
    margin: 0 -1.25rem -1.25rem;
    padding: 1.25rem;
    border-radius: 0 0 12px 12px;
}

.total-row, .paid-row, .balance-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    font-size: 0.875rem;
}

.total-row {
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--gray-200);
    margin-bottom: 0.5rem;
}

.total-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary);
}

.balance-row {
    margin-top: 0.5rem;
    padding-top: 0.75rem;
    border-top: 1px solid var(--gray-200);
}

.balance-row.outstanding .balance-value {
    color: #ef4444;
    font-weight: 700;
    font-size: 1.125rem;
}

.balance-row.settled .balance-value {
    color: #22c55e;
    font-weight: 700;
}

.activity-log-body {
    max-height: 280px;
    overflow-y: auto;
    padding: 1rem !important;
}

.activity-item {
    display: flex;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--gray-100);
}

.activity-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.activity-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    flex-shrink: 0;
}

.activity-icon.green { background: #dcfce7; color: #22c55e; }
.activity-icon.blue { background: #dbeafe; color: #3b82f6; }

.activity-content { flex: 1; min-width: 0; }

.activity-text {
    margin: 0;
    font-size: 0.8125rem;
    color: var(--gray-800);
    line-height: 1.4;
}

.activity-meta {
    font-size: 0.6875rem;
    color: var(--gray-500);
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.modal-content {
    background: white;
    border-radius: 12px;
    width: 100%;
    max-width: 500px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}

.modal-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--gray-400);
    padding: 0;
    line-height: 1;
}

.modal-body { padding: 1.5rem; }

.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--gray-200);
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

#toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
}

.toast {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    margin-bottom: 0.75rem;
    animation: slideIn 0.3s ease;
}

.toast.success { border-left: 4px solid #22c55e; }
.toast.error { border-left: 4px solid #ef4444; }
.toast i { font-size: 1.25rem; }
.toast.success i { color: #22c55e; }
.toast.error i { color: #ef4444; }

@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

.input-group { display: flex; }

.input-group-text {
    padding: 0.5rem 0.75rem;
    background: var(--gray-100);
    border: 1px solid var(--gray-300);
    border-right: none;
    border-radius: 8px 0 0 8px;
    color: var(--gray-600);
    font-weight: 500;
}

.input-group .form-control { border-radius: 0 8px 8px 0; }

.btn-block { width: 100%; }

.p-0 { padding: 0 !important; }
.py-4 { padding-top: 1.5rem !important; padding-bottom: 1.5rem !important; }

@media (max-width: 1024px) {
    .booking-detail-grid { grid-template-columns: 1fr; }
    .booking-sidebar { position: static; }
    .stay-details-grid { flex-direction: column; }
    .stay-date-card { width: 100%; }
    .stay-duration { transform: rotate(90deg); padding: 0.5rem 0; }
}
</style>

<script>
function openPaymentModal() {
    document.getElementById('paymentModal').style.display = 'flex';
}

function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
}

function confirmCancel() {
    if (confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
        document.getElementById('cancelForm').submit();
    }
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i><span>${message}</span>`;
    document.getElementById('toast-container').appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

document.querySelectorAll('.ajax-action-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const action = this.dataset.action;
        const originalHtml = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        this.disabled = true;

        fetch(action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message || 'Operation failed', 'error');
                this.innerHTML = originalHtml;
                this.disabled = false;
            }
        })
        .catch(() => {
            showToast('An error occurred', 'error');
            this.innerHTML = originalHtml;
            this.disabled = false;
        });
    });
});

document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    btn.disabled = true;

    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new FormData(this)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Payment recorded successfully!', 'success');
            closePaymentModal();
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message || 'Failed to record payment', 'error');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }
    })
    .catch(() => {
        showToast('An error occurred', 'error');
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    });
});

document.getElementById('paymentModal').addEventListener('click', function(e) {
    if (e.target === this) closePaymentModal();
});
</script>
@endsection
