<!-- Quick Booking Modal for Walk-in Customers -->
<div id="quickBookingModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3 class="modal-title">Quick Booking - Walk-in Customer</h3>
            <button type="button" class="modal-close" onclick="closeQuickBookingModal()">&times;</button>
        </div>
        <form action="{{ route('admin.bookings.store') }}" method="POST" id="quickBookingForm">
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">Guest Name <span class="text-danger">*</span></label>
                            <input type="text" name="guest_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" name="guest_phone" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email (Optional)</label>
                    <input type="email" name="guest_email" class="form-control">
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">Check-in Date <span class="text-danger">*</span></label>
                            <input type="date" name="check_in" id="quickCheckIn" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">Check-out Date <span class="text-danger">*</span></label>
                            <input type="date" name="check_out" id="quickCheckOut" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">Room Type <span class="text-danger">*</span></label>
                            <select name="room_type_id" id="quickRoomType" class="form-control" required>
                                <option value="">Select Room Type</option>
                                @foreach(\App\Models\RoomType::active()->get() as $type)
                                    <option value="{{ $type->id }}" data-price="{{ $type->price }}">
                                        {{ $type->name }} - TZS {{ number_format($type->price, 0) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">Number of Rooms <span class="text-danger">*</span></label>
                            <input type="number" name="number_of_rooms" id="quickNumRooms" class="form-control" min="1" value="1" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">Adults <span class="text-danger">*</span></label>
                            <input type="number" name="adults" class="form-control" min="1" value="1" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">Children</label>
                            <input type="number" name="children" class="form-control" min="0" value="0">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="source" value="walk_in">
                <input type="hidden" name="room_rate" id="quickRoomRate" value="0">

                <div id="priceEstimate" class="alert alert-info" style="display: none; margin-top: 1rem;">
                    <strong>Estimated Total:</strong> TZS <span id="estimatedPrice">0</span>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeQuickBookingModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Booking</button>
            </div>
        </form>
    </div>
</div>

<style>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    overflow-y: auto;
    padding: 20px;
}

.modal-content {
    background: white;
    margin: 50px auto;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--gray-600);
    line-height: 1;
    padding: 0;
    width: 30px;
    height: 30px;
}

.modal-close:hover {
    color: var(--gray-900);
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--gray-200);
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}
</style>

<script>
function openQuickBookingModal() {
    document.getElementById('quickBookingModal').style.display = 'block';
    document.getElementById('quickCheckIn').min = new Date().toISOString().split('T')[0];
    document.getElementById('quickCheckOut').min = new Date().toISOString().split('T')[0];
}

function closeQuickBookingModal() {
    document.getElementById('quickBookingModal').style.display = 'none';
    document.getElementById('quickBookingForm').reset();
    document.getElementById('priceEstimate').style.display = 'none';
}

// Calculate price estimate
document.addEventListener('DOMContentLoaded', function() {
    const roomTypeSelect = document.getElementById('quickRoomType');
    const numRoomsInput = document.getElementById('quickNumRooms');
    const checkInInput = document.getElementById('quickCheckIn');
    const checkOutInput = document.getElementById('quickCheckOut');
    const roomRateInput = document.getElementById('quickRoomRate');
    const priceEstimate = document.getElementById('priceEstimate');
    const estimatedPrice = document.getElementById('estimatedPrice');

    function calculatePrice() {
        const selectedOption = roomTypeSelect.options[roomTypeSelect.selectedIndex];
        const price = parseFloat(selectedOption.dataset.price || 0);
        const numRooms = parseInt(numRoomsInput.value || 1);
        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);

        if (price && checkInInput.value && checkOutInput.value && checkOut > checkIn) {
            const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
            const total = price * nights * numRooms;

            roomRateInput.value = price;
            estimatedPrice.textContent = total.toLocaleString();
            priceEstimate.style.display = 'block';
        } else {
            priceEstimate.style.display = 'none';
        }
    }

    if (roomTypeSelect) roomTypeSelect.addEventListener('change', calculatePrice);
    if (numRoomsInput) numRoomsInput.addEventListener('input', calculatePrice);
    if (checkInInput) checkInInput.addEventListener('change', calculatePrice);
    if (checkOutInput) checkOutInput.addEventListener('change', calculatePrice);

    // Set check-out when check-in changes
    if (checkInInput) {
        checkInInput.addEventListener('change', function() {
            const checkInDate = new Date(this.value);
            checkInDate.setDate(checkInDate.getDate() + 1);
            const minCheckOut = checkInDate.toISOString().split('T')[0];
            checkOutInput.min = minCheckOut;
            if (!checkOutInput.value || new Date(checkOutInput.value) <= new Date(this.value)) {
                checkOutInput.value = minCheckOut;
            }
            calculatePrice();
        });
    }
});

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('quickBookingModal');
    if (event.target === modal) {
        closeQuickBookingModal();
    }
}
</script>
