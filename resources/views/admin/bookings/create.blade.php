@extends('admin.layouts.app')

@section('title', 'New Booking')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <a href="{{ route('admin.bookings.index') }}">Bookings</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>New Booking</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Create New Booking</h1>
        <p class="page-subtitle">Add a new reservation to the system</p>
    </div>
</div>

<form action="{{ route('admin.bookings.store') }}" method="POST" id="bookingForm">
    @csrf

    <div class="row">
        <div class="col-8">
            <!-- Booking Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Booking Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Check-in Date <span class="text-danger">*</span></label>
                                <input
                                    type="date"
                                    name="check_in_date"
                                    class="form-control @error('check_in_date') is-invalid @enderror"
                                    value="{{ old('check_in_date', request('check_in_date', date('Y-m-d'))) }}"
                                    min="{{ date('Y-m-d') }}"
                                    required
                                    id="checkInDate"
                                >
                                @error('check_in_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Check-out Date <span class="text-danger">*</span></label>
                                <input
                                    type="date"
                                    name="check_out_date"
                                    class="form-control @error('check_out_date') is-invalid @enderror"
                                    value="{{ old('check_out_date', request('check_out_date')) }}"
                                    required
                                    id="checkOutDate"
                                >
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
                                <select
                                    name="room_type_id"
                                    class="form-control @error('room_type_id') is-invalid @enderror"
                                    required
                                    id="roomTypeSelect"
                                >
                                    <option value="">Select Room Type</option>
                                    @foreach($roomTypes as $type)
                                        <option
                                            value="{{ $type->id }}"
                                            data-price="{{ $type->base_price_per_night }}"
                                            {{ old('room_type_id', request('room_type_id')) == $type->id ? 'selected' : '' }}
                                        >
                                            {{ $type->name }} - ${{ number_format($type->base_price_per_night, 2) }}/night
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
                                <label class="form-label">Assign Room (Optional)</label>
                                <select
                                    name="room_id"
                                    class="form-control @error('room_id') is-invalid @enderror"
                                    id="roomSelect"
                                >
                                    <option value="">Assign Later</option>
                                    @foreach($rooms ?? [] as $room)
                                        <option
                                            value="{{ $room->id }}"
                                            data-room-type="{{ $room->room_type_id }}"
                                            {{ old('room_id', request('room_id')) == $room->id ? 'selected' : '' }}
                                        >
                                            Room {{ $room->room_number }} ({{ $room->roomType->name ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Adults <span class="text-danger">*</span></label>
                                <select name="adults" class="form-control @error('adults') is-invalid @enderror" required>
                                    @for($i = 1; $i <= 4; $i++)
                                        <option value="{{ $i }}" {{ old('adults', 2) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                                @error('adults')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Children</label>
                                <select name="children" class="form-control @error('children') is-invalid @enderror">
                                    @for($i = 0; $i <= 4; $i++)
                                        <option value="{{ $i }}" {{ old('children', 0) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                                @error('children')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Booking Source</label>
                                <select name="source" class="form-control @error('source') is-invalid @enderror">
                                    <option value="direct" {{ old('source', 'direct') === 'direct' ? 'selected' : '' }}>Direct (Walk-in/Call)</option>
                                    <option value="website" {{ old('source') === 'website' ? 'selected' : '' }}>Website</option>
                                    <option value="booking_com" {{ old('source') === 'booking_com' ? 'selected' : '' }}>Booking.com</option>
                                    <option value="expedia" {{ old('source') === 'expedia' ? 'selected' : '' }}>Expedia</option>
                                    <option value="agoda" {{ old('source') === 'agoda' ? 'selected' : '' }}>Agoda</option>
                                    <option value="airbnb" {{ old('source') === 'airbnb' ? 'selected' : '' }}>Airbnb</option>
                                    <option value="other" {{ old('source') === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('source')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Special Requests / Notes</label>
                        <textarea
                            name="special_requests"
                            class="form-control @error('special_requests') is-invalid @enderror"
                            rows="3"
                            placeholder="Any special requests or notes..."
                        >{{ old('special_requests') }}</textarea>
                        @error('special_requests')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Guest Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Guest Information</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Search Existing Guest</label>
                        <div class="d-flex gap-2">
                            <input
                                type="text"
                                id="guestSearch"
                                class="form-control"
                                placeholder="Search by name, email, or phone..."
                            >
                            <button type="button" class="btn btn-secondary" id="searchGuestBtn">
                                <i class="fas fa-search"></i>
                            </button>
                            <button type="button" class="btn btn-primary" id="newGuestBtn">
                                <i class="fas fa-plus"></i> New Guest
                            </button>
                        </div>
                    </div>

                    <input type="hidden" name="guest_id" id="guestId" value="{{ old('guest_id') }}">

                    <div id="guestFormFields">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        name="first_name"
                                        class="form-control @error('first_name') is-invalid @enderror"
                                        value="{{ old('first_name') }}"
                                        required
                                        id="firstName"
                                    >
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        name="last_name"
                                        class="form-control @error('last_name') is-invalid @enderror"
                                        value="{{ old('last_name') }}"
                                        required
                                        id="lastName"
                                    >
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input
                                        type="email"
                                        name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}"
                                        id="email"
                                    >
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input
                                        type="tel"
                                        name="phone"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        value="{{ old('phone') }}"
                                        required
                                        id="phone"
                                    >
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">ID Type</label>
                                    <select name="id_type" class="form-control" id="idType">
                                        <option value="">Select ID Type</option>
                                        <option value="passport" {{ old('id_type') === 'passport' ? 'selected' : '' }}>Passport</option>
                                        <option value="national_id" {{ old('id_type') === 'national_id' ? 'selected' : '' }}>National ID</option>
                                        <option value="driver_license" {{ old('id_type') === 'driver_license' ? 'selected' : '' }}>Driver's License</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">ID Number</label>
                                    <input
                                        type="text"
                                        name="id_number"
                                        class="form-control"
                                        value="{{ old('id_number') }}"
                                        id="idNumber"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Address</label>
                            <input
                                type="text"
                                name="address"
                                class="form-control"
                                value="{{ old('address') }}"
                                id="address"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar - Pricing -->
        <div class="col-4">
            <div class="card" style="position: sticky; top: 80px;">
                <div class="card-header">
                    <h3 class="card-title">Booking Summary</h3>
                </div>
                <div class="card-body">
                    <div id="bookingSummary">
                        <div class="d-flex justify-between mb-2">
                            <span class="text-muted">Room Type:</span>
                            <span id="summaryRoomType">-</span>
                        </div>
                        <div class="d-flex justify-between mb-2">
                            <span class="text-muted">Check-in:</span>
                            <span id="summaryCheckIn">-</span>
                        </div>
                        <div class="d-flex justify-between mb-2">
                            <span class="text-muted">Check-out:</span>
                            <span id="summaryCheckOut">-</span>
                        </div>
                        <div class="d-flex justify-between mb-3">
                            <span class="text-muted">Nights:</span>
                            <span id="summaryNights">-</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-between mb-2">
                            <span class="text-muted">Room Rate:</span>
                            <span id="summaryRoomRate">$0.00</span>
                        </div>
                        <div class="d-flex justify-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span id="summarySubtotal">$0.00</span>
                        </div>
                        <div class="d-flex justify-between mb-2">
                            <span class="text-muted">Tax (10%):</span>
                            <span id="summaryTax">$0.00</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-between mb-3">
                            <strong>Total Amount:</strong>
                            <strong id="summaryTotal" style="font-size: 1.25rem; color: var(--primary);">$0.00</strong>
                        </div>

                        <input type="hidden" name="room_rate" id="roomRateInput">
                        <input type="hidden" name="subtotal" id="subtotalInput">
                        <input type="hidden" name="tax_amount" id="taxInput">
                        <input type="hidden" name="total_amount" id="totalInput">
                    </div>

                    <hr>

                    <div class="form-group">
                        <label class="form-label">Initial Payment</label>
                        <input
                            type="number"
                            name="initial_payment"
                            class="form-control"
                            value="{{ old('initial_payment', 0) }}"
                            min="0"
                            step="0.01"
                            placeholder="0.00"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-control">
                            <option value="cash">Cash</option>
                            <option value="card">Credit/Debit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" name="status" value="confirmed" class="btn btn-primary w-100">
                            <i class="fas fa-check"></i> Confirm Booking
                        </button>
                    </div>
                    <div class="d-flex gap-2 mt-2">
                        <button type="submit" name="status" value="pending" class="btn btn-secondary w-100">
                            <i class="fas fa-clock"></i> Save as Pending
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkInDate = document.getElementById('checkInDate');
    const checkOutDate = document.getElementById('checkOutDate');
    const roomTypeSelect = document.getElementById('roomTypeSelect');
    const taxRate = 0.10;

    function updateSummary() {
        const roomType = roomTypeSelect.options[roomTypeSelect.selectedIndex];
        const checkIn = checkInDate.value;
        const checkOut = checkOutDate.value;

        // Update room type display
        document.getElementById('summaryRoomType').textContent = roomType.value ? roomType.text.split(' - ')[0] : '-';
        document.getElementById('summaryCheckIn').textContent = checkIn ? new Date(checkIn).toLocaleDateString() : '-';
        document.getElementById('summaryCheckOut').textContent = checkOut ? new Date(checkOut).toLocaleDateString() : '-';

        // Calculate nights
        let nights = 0;
        if (checkIn && checkOut) {
            const start = new Date(checkIn);
            const end = new Date(checkOut);
            nights = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
        }
        document.getElementById('summaryNights').textContent = nights > 0 ? nights : '-';

        // Calculate pricing
        let roomRate = roomType.dataset.price ? parseFloat(roomType.dataset.price) : 0;
        let subtotal = roomRate * nights;
        let tax = subtotal * taxRate;
        let total = subtotal + tax;

        document.getElementById('summaryRoomRate').textContent = '$' + roomRate.toFixed(2) + '/night';
        document.getElementById('summarySubtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('summaryTax').textContent = '$' + tax.toFixed(2);
        document.getElementById('summaryTotal').textContent = '$' + total.toFixed(2);

        // Update hidden inputs
        document.getElementById('roomRateInput').value = roomRate;
        document.getElementById('subtotalInput').value = subtotal;
        document.getElementById('taxInput').value = tax;
        document.getElementById('totalInput').value = total;
    }

    // Set minimum checkout date
    checkInDate.addEventListener('change', function() {
        const minCheckout = new Date(this.value);
        minCheckout.setDate(minCheckout.getDate() + 1);
        checkOutDate.min = minCheckout.toISOString().split('T')[0];

        if (checkOutDate.value && new Date(checkOutDate.value) <= new Date(this.value)) {
            checkOutDate.value = minCheckout.toISOString().split('T')[0];
        }

        updateSummary();
    });

    checkOutDate.addEventListener('change', updateSummary);
    roomTypeSelect.addEventListener('change', updateSummary);

    // Initial calculation
    updateSummary();

    // Guest search functionality
    document.getElementById('searchGuestBtn').addEventListener('click', function() {
        const query = document.getElementById('guestSearch').value;
        if (query.length < 2) {
            alert('Please enter at least 2 characters to search');
            return;
        }
        // In production, this would make an AJAX call to search guests
        alert('Guest search would search for: ' + query);
    });

    document.getElementById('newGuestBtn').addEventListener('click', function() {
        document.getElementById('guestId').value = '';
        document.getElementById('firstName').value = '';
        document.getElementById('lastName').value = '';
        document.getElementById('email').value = '';
        document.getElementById('phone').value = '';
        document.getElementById('idType').value = '';
        document.getElementById('idNumber').value = '';
        document.getElementById('address').value = '';
        document.getElementById('firstName').focus();
    });
});
</script>
@endpush
