@extends('admin.layouts.app')

@section('title', 'Edit Booking #' . $booking->booking_number)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <a href="{{ route('admin.bookings.index') }}">Bookings</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <a href="{{ route('admin.bookings.show', $booking) }}">{{ $booking->booking_number }}</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>Edit</span>
@endsection

@section('content')
<form action="{{ route('admin.bookings.update', $booking) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Booking #{{ $booking->booking_number }}</h1>
            <p class="page-subtitle">Modify booking details and guest information</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Booking
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-8">
            <!-- Guest Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Guest Information</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Guest</label>
                        <select name="guest_id" class="form-control @error('guest_id') is-invalid @enderror" id="guestSelect">
                            @if($booking->guest)
                                <option value="{{ $booking->guest->id }}" selected>
                                    {{ $booking->guest->full_name }} ({{ $booking->guest->email }})
                                </option>
                            @endif
                        </select>
                        @error('guest_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Number of Adults <span class="text-danger">*</span></label>
                                <input type="number" name="adults" class="form-control @error('adults') is-invalid @enderror"
                                       value="{{ old('adults', $booking->adults) }}" min="1" max="10" required>
                                @error('adults')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Number of Children</label>
                                <input type="number" name="children" class="form-control @error('children') is-invalid @enderror"
                                       value="{{ old('children', $booking->children) }}" min="0" max="10">
                                @error('children')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
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
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Check-in Date <span class="text-danger">*</span></label>
                                <input type="date" name="check_in_date" id="checkInDate" class="form-control @error('check_in_date') is-invalid @enderror"
                                       value="{{ old('check_in_date', $booking->check_in_date->format('Y-m-d')) }}" required>
                                @error('check_in_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Check-out Date <span class="text-danger">*</span></label>
                                <input type="date" name="check_out_date" id="checkOutDate" class="form-control @error('check_out_date') is-invalid @enderror"
                                       value="{{ old('check_out_date', $booking->check_out_date->format('Y-m-d')) }}" required>
                                @error('check_out_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Room Type <span class="text-danger">*</span></label>
                                <select name="room_type_id" id="roomTypeSelect" class="form-control @error('room_type_id') is-invalid @enderror" required>
                                    @foreach($roomTypes ?? [] as $type)
                                        <option value="{{ $type->id }}"
                                                data-price="{{ $type->base_price }}"
                                                {{ old('room_type_id', $booking->room_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }} - ${{ number_format($type->base_price, 0) }}/night
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Room <span class="text-danger">*</span></label>
                                <select name="room_id" id="roomSelect" class="form-control @error('room_id') is-invalid @enderror" required>
                                    @foreach($rooms ?? [] as $room)
                                        <option value="{{ $room->id }}"
                                                data-room-type="{{ $room->room_type_id }}"
                                                {{ old('room_id', $booking->room_id) == $room->id ? 'selected' : '' }}>
                                            Room {{ $room->room_number }} ({{ $room->roomType->name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Pricing</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Nightly Rate <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="nightly_rate" id="nightlyRate" class="form-control @error('nightly_rate') is-invalid @enderror"
                                           value="{{ old('nightly_rate', $booking->nightly_rate) }}" min="0" step="0.01" required>
                                </div>
                                @error('nightly_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Discount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="discount" class="form-control @error('discount') is-invalid @enderror"
                                           value="{{ old('discount', $booking->discount ?? 0) }}" min="0" step="0.01">
                                </div>
                                @error('discount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Extra Charges</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="extra_charges" class="form-control @error('extra_charges') is-invalid @enderror"
                                           value="{{ old('extra_charges', $booking->extra_charges ?? 0) }}" min="0" step="0.01">
                                </div>
                                @error('extra_charges')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Tax Rate (%)</label>
                                <input type="number" name="tax_rate" class="form-control @error('tax_rate') is-invalid @enderror"
                                       value="{{ old('tax_rate', $booking->tax_rate ?? 10) }}" min="0" max="100" step="0.01">
                                @error('tax_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Tax Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="tax_amount" id="taxAmount" class="form-control"
                                           value="{{ old('tax_amount', $booking->tax_amount ?? 0) }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Total Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="total_amount" id="totalAmount" class="form-control"
                                           value="{{ old('total_amount', $booking->total_amount) }}" readonly style="font-weight: 600;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Special Requests -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Additional Information</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Special Requests</label>
                        <textarea name="special_requests" class="form-control @error('special_requests') is-invalid @enderror"
                                  rows="3" placeholder="Any special requests or notes">{{ old('special_requests', $booking->special_requests) }}</textarea>
                        @error('special_requests')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Internal Notes</label>
                        <textarea name="internal_notes" class="form-control @error('internal_notes') is-invalid @enderror"
                                  rows="3" placeholder="Staff notes (not visible to guest)">{{ old('internal_notes', $booking->internal_notes) }}</textarea>
                        @error('internal_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-4">
            <!-- Booking Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Booking Status</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="pending" {{ old('status', $booking->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ old('status', $booking->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="checked_in" {{ old('status', $booking->status) == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                            <option value="checked_out" {{ old('status', $booking->status) == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                            <option value="cancelled" {{ old('status', $booking->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="no_show" {{ old('status', $booking->status) == 'no_show' ? 'selected' : '' }}>No Show</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Booking Source</label>
                        <select name="source" class="form-control @error('source') is-invalid @enderror">
                            <option value="direct" {{ old('source', $booking->source) == 'direct' ? 'selected' : '' }}>Direct (Website)</option>
                            <option value="phone" {{ old('source', $booking->source) == 'phone' ? 'selected' : '' }}>Phone</option>
                            <option value="walk-in" {{ old('source', $booking->source) == 'walk-in' ? 'selected' : '' }}>Walk-in</option>
                            <option value="booking.com" {{ old('source', $booking->source) == 'booking.com' ? 'selected' : '' }}>Booking.com</option>
                            <option value="expedia" {{ old('source', $booking->source) == 'expedia' ? 'selected' : '' }}>Expedia</option>
                            <option value="agoda" {{ old('source', $booking->source) == 'agoda' ? 'selected' : '' }}>Agoda</option>
                            <option value="airbnb" {{ old('source', $booking->source) == 'airbnb' ? 'selected' : '' }}>Airbnb</option>
                            <option value="other" {{ old('source', $booking->source) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('source')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Payment Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Payment</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Payment Status</label>
                        <select name="payment_status" class="form-control @error('payment_status') is-invalid @enderror">
                            <option value="pending" {{ old('payment_status', $booking->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="partial" {{ old('payment_status', $booking->payment_status) == 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="paid" {{ old('payment_status', $booking->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="refunded" {{ old('payment_status', $booking->payment_status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                        @error('payment_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-control @error('payment_method') is-invalid @enderror">
                            <option value="">Select Method</option>
                            <option value="credit_card" {{ old('payment_method', $booking->payment_method) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                            <option value="debit_card" {{ old('payment_method', $booking->payment_method) == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                            <option value="cash" {{ old('payment_method', $booking->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank_transfer" {{ old('payment_method', $booking->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="paypal" {{ old('payment_method', $booking->payment_method) == 'paypal' ? 'selected' : '' }}>PayPal</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Amount Paid</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="amount_paid" class="form-control @error('amount_paid') is-invalid @enderror"
                                   value="{{ old('amount_paid', $booking->amount_paid ?? 0) }}" min="0" step="0.01">
                        </div>
                        @error('amount_paid')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Price Summary -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Price Summary</h3>
                </div>
                <div class="card-body">
                    <div class="price-summary">
                        <div class="d-flex justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                            <span class="text-muted">Nights</span>
                            <span id="summaryNights">{{ $booking->nights }}</span>
                        </div>
                        <div class="d-flex justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                            <span class="text-muted">Room Total</span>
                            <span id="summaryRoomTotal">${{ number_format($booking->nightly_rate * $booking->nights, 2) }}</span>
                        </div>
                        <div class="d-flex justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                            <span class="text-muted">Discount</span>
                            <span id="summaryDiscount" class="text-danger">-${{ number_format($booking->discount ?? 0, 2) }}</span>
                        </div>
                        <div class="d-flex justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                            <span class="text-muted">Extra Charges</span>
                            <span id="summaryExtras">${{ number_format($booking->extra_charges ?? 0, 2) }}</span>
                        </div>
                        <div class="d-flex justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                            <span class="text-muted">Tax</span>
                            <span id="summaryTax">${{ number_format($booking->tax_amount ?? 0, 2) }}</span>
                        </div>
                        <div class="d-flex justify-between py-2" style="font-size: 1.125rem; font-weight: 600;">
                            <span>Total</span>
                            <span id="summaryTotal" class="text-primary">${{ number_format($booking->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.input-group {
    display: flex;
}

.input-group-text {
    padding: 0.5rem 0.75rem;
    background: var(--gray-100);
    border: 1px solid var(--gray-300);
    border-right: none;
    border-radius: 0.375rem 0 0 0.375rem;
    color: var(--gray-600);
}

.input-group .form-control {
    border-radius: 0 0.375rem 0.375rem 0;
}
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkInDate = document.getElementById('checkInDate');
    const checkOutDate = document.getElementById('checkOutDate');
    const roomTypeSelect = document.getElementById('roomTypeSelect');
    const roomSelect = document.getElementById('roomSelect');
    const nightlyRateInput = document.getElementById('nightlyRate');
    const discountInput = document.querySelector('input[name="discount"]');
    const extraChargesInput = document.querySelector('input[name="extra_charges"]');
    const taxRateInput = document.querySelector('input[name="tax_rate"]');
    const taxAmountInput = document.getElementById('taxAmount');
    const totalAmountInput = document.getElementById('totalAmount');

    // Summary elements
    const summaryNights = document.getElementById('summaryNights');
    const summaryRoomTotal = document.getElementById('summaryRoomTotal');
    const summaryDiscount = document.getElementById('summaryDiscount');
    const summaryExtras = document.getElementById('summaryExtras');
    const summaryTax = document.getElementById('summaryTax');
    const summaryTotal = document.getElementById('summaryTotal');

    function calculateNights() {
        const checkIn = new Date(checkInDate.value);
        const checkOut = new Date(checkOutDate.value);
        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        return nights > 0 ? nights : 0;
    }

    function calculateTotal() {
        const nights = calculateNights();
        const rate = parseFloat(nightlyRateInput.value) || 0;
        const discount = parseFloat(discountInput.value) || 0;
        const extras = parseFloat(extraChargesInput.value) || 0;
        const taxRate = parseFloat(taxRateInput.value) || 0;

        const roomTotal = nights * rate;
        const subtotal = roomTotal - discount + extras;
        const taxAmount = subtotal * (taxRate / 100);
        const total = subtotal + taxAmount;

        // Update form fields
        taxAmountInput.value = taxAmount.toFixed(2);
        totalAmountInput.value = total.toFixed(2);

        // Update summary
        summaryNights.textContent = nights;
        summaryRoomTotal.textContent = '$' + roomTotal.toFixed(2);
        summaryDiscount.textContent = '-$' + discount.toFixed(2);
        summaryExtras.textContent = '$' + extras.toFixed(2);
        summaryTax.textContent = '$' + taxAmount.toFixed(2);
        summaryTotal.textContent = '$' + total.toFixed(2);
    }

    // Filter rooms by room type
    function filterRooms() {
        const selectedType = roomTypeSelect.value;
        const options = roomSelect.querySelectorAll('option');
        let firstMatch = null;

        options.forEach(option => {
            if (option.dataset.roomType === selectedType || !selectedType) {
                option.style.display = '';
                if (!firstMatch) firstMatch = option;
            } else {
                option.style.display = 'none';
            }
        });

        // Update nightly rate from room type
        const selectedOption = roomTypeSelect.options[roomTypeSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.price) {
            nightlyRateInput.value = selectedOption.dataset.price;
        }

        calculateTotal();
    }

    // Event listeners
    checkInDate.addEventListener('change', calculateTotal);
    checkOutDate.addEventListener('change', calculateTotal);
    roomTypeSelect.addEventListener('change', filterRooms);
    nightlyRateInput.addEventListener('input', calculateTotal);
    discountInput.addEventListener('input', calculateTotal);
    extraChargesInput.addEventListener('input', calculateTotal);
    taxRateInput.addEventListener('input', calculateTotal);

    // Initial calculation
    calculateTotal();
});
</script>
@endpush
