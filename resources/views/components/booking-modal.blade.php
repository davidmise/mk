@props(['roomTypes' => []])

@php
    $roomTypes = $roomTypes->count() > 0 ? $roomTypes : \App\Models\RoomType::active()->ordered()->get();
@endphp

<!-- Modal Structure -->
<div id="bookingModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Book a Room</h2>
        <form id="bookingForm" action="{{ route('booking.store') }}" method="POST" class="booking-form">
            @csrf
            <div class="form-group">
                <input type="text" name="guest_name" placeholder="Your Name" required>
            </div>

            <div class="form-group">
                <input type="email" name="guest_email" placeholder="Email Address">
            </div>

            <div class="form-group">
                <input type="tel" name="guest_phone" placeholder="Phone Number" required pattern="[0-9+ ]{6,15}">
            </div>

            <div class="form-group">
                <label>Check-in:</label>
                <input type="datetime-local" id="check_in" name="check_in" required>
            </div>

            <div class="form-group">
                <label>Check-out:</label>
                <input type="datetime-local" id="check_out" name="check_out" required>
            </div>

            <div class="form-group">
                <label>Room Type:</label>
                <select name="room_type_id" id="room_type_id" required>
                    <option value="">Select a Room</option>
                    @foreach($roomTypes as $room)
                        <option value="{{ $room->id }}">{{ $room->name }} - {{ $room->formatted_price }}</option>
                    @endforeach
                </select>
                <p id="availability" style="margin-top: 5px; color: green;"></p>
            </div>

            <div class="form-group" id="room-count-group" style="display: none;">
                <label>Number of Rooms:</label>
                <input type="number" name="number_of_rooms" id="number_of_rooms" min="1" required>
            </div>
            <div id="formErrors"></div>
            <button type="submit" class="btn btn-dark">Submit</button>
        </form>
    </div>
</div>

@push('styles')
<style>
    .booking-form label {
        display: block;
        text-align: left;
        margin-bottom: 5px;
        font-weight: 500;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .loaderr {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 2px solid #999;
        border-top: 2px solid transparent;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
        vertical-align: middle;
        margin-left: 5px;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .input-error {
        border: 1px solid red;
        background-color: #ffe5e5;
    }
    .error-text {
        color: red;
        font-size: 0.9em;
        margin-top: 3px;
    }
    .toast {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background-color: #28a745;
        color: white;
        padding: 14px 24px;
        border-radius: 8px;
        font-size: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        z-index: 1000;
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.4s ease, transform 0.4s ease;
    }
    .toast.show {
        opacity: 1;
        transform: translateY(0);
    }
    .toast.hidden {
        display: none;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById("bookingModal");
    const openBtn = document.getElementById("openModalBtn");
    const openBtn2 = document.getElementById("openModalBtn2");
    const closeBtn = document.querySelector(".close-btn");
    const openModal = () => modal.style.display = "block";
    if (openBtn) openBtn.onclick = openModal;
    if (openBtn2) openBtn2.onclick = openModal;
    if (closeBtn) closeBtn.onclick = () => modal.style.display = "none";
    window.onclick = (event) => { if (event.target === modal) modal.style.display = "none"; };

    const today = new Date().toISOString().split('T')[0];
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const roomSelect = document.getElementById('room_type_id');
    const availabilityText = document.getElementById('availability');
    const roomCountInput = document.getElementById('number_of_rooms');
    const roomCountGroup = document.getElementById('room-count-group');
    const errorContainer = document.getElementById('formErrors');

    if (checkInInput) checkInInput.min = today;
    if (checkOutInput) checkOutInput.min = today;

    let maxAvailableRooms = 0;

    const fetchAvailability = () => {
        const roomTypeId = roomSelect?.value;
        const checkIn = checkInInput?.value;
        const checkOut = checkOutInput?.value;

        if (!roomTypeId || !checkIn || !checkOut) {
            availabilityText.innerHTML = `<span class="error-text">Please select room type and dates.</span>`;
            roomCountGroup.style.display = 'none';
            return;
        }

        availabilityText.innerHTML = 'Checking availability... <span class="loader"></span>';

        fetch(`{{ route('booking.availability') }}?room_type_id=${roomTypeId}&check_in=${checkIn}&check_out=${checkOut}`)
            .then(response => response.json())
            .then(data => {
                if (data.available_rooms > 0) {
                    maxAvailableRooms = data.available_rooms;
                    availabilityText.innerHTML = `Available: <strong>${data.available_rooms}</strong> out of <strong>${data.total_rooms}</strong> room(s)`;
                    roomCountInput.max = data.available_rooms;
                    roomCountInput.value = 1;
                    roomCountGroup.style.display = 'block';
                } else {
                    availabilityText.innerHTML = `<span class="error-text">No rooms available for selected dates.</span>`;
                    roomCountGroup.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Availability fetch failed:', error);
                availabilityText.innerHTML = `<span class="error-text">Error checking availability.</span>`;
                roomCountGroup.style.display = 'none';
            });
    };

    checkInInput?.addEventListener('change', () => {
        const checkInDate = new Date(checkInInput.value);
        const checkOutDate = new Date(checkOutInput.value);

        if (!checkOutInput.value || checkOutDate <= checkInDate) {
            checkOutInput.value = checkInInput.value;
            checkOutInput.min = checkInInput.value;
        }

        fetchAvailability();
    });

    checkOutInput?.addEventListener('change', fetchAvailability);
    if (roomSelect) roomSelect.addEventListener('change', fetchAvailability);

    roomCountInput?.addEventListener('input', () => {
        if (parseInt(roomCountInput.value) > maxAvailableRooms) {
            roomCountInput.classList.add('input-error');
            availabilityText.innerHTML = `<span class="error-text">You can't book more than ${maxAvailableRooms} room(s).</span>`;
        } else {
            roomCountInput.classList.remove('input-error');
            availabilityText.innerHTML = `Available: <strong>${maxAvailableRooms}</strong> room(s)`;
        }
    });

    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function (e) {
            e.preventDefault();
            errorContainer.innerHTML = '';
            let errors = [];
            let isValid = true;

            const fields = [
                { id: 'name', label: 'Name' },
                { id: 'email', label: 'Email' },
                { id: 'phone', label: 'Phone' },
                { id: 'check_in', label: 'Check-in date' },
                { id: 'check_out', label: 'Check-out date' },
                { id: 'room_type_id', label: 'Room type' },
            ];

            fields.forEach(field => {
                const input = document.getElementById(field.id) || document.querySelector(`[name="${field.id}"]`);
                if (input && !input.value.trim()) {
                    input.classList.add('input-error');
                    errors.push(`${field.label} is required.`);
                    isValid = false;
                } else {
                    input?.classList.remove('input-error');
                }
            });

            const checkInDate = new Date(checkInInput.value);
            const now = new Date();
            now.setHours(0, 0, 0, 0);
            if (checkInDate < now) {
                checkInInput.classList.add('input-error');
                errors.push("Check-in date cannot be in the past.");
                isValid = false;
            }

            if (parseInt(roomCountInput.value) > maxAvailableRooms) {
                roomCountInput.classList.add('input-error');
                errors.push(`You can't book more than ${maxAvailableRooms} room(s).`);
                isValid = false;
            } else {
                roomCountInput.classList.remove('input-error');
            }

            if (!isValid) {
                errorContainer.innerHTML = errors.map(err => `<div class="error-text">${err}</div>`).join('');
                return;
            }

            const formData = new FormData(bookingForm);
            fetch('{{ route('booking.store') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Booking successful!');
                    bookingForm.reset();
                    modal.style.display = 'none';
                } else {
                    errorContainer.innerHTML = `<div class="error-text">${data.message || 'Booking failed'}</div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorContainer.innerHTML = `<div class="error-text">Failed to submit booking.</div>`;
            });
        });
    }

    function showToast(message, duration = 3000) {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.classList.remove('hidden');
        toast.classList.add('show');

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.classList.add('hidden'), 400);
        }, duration);
    }
});
</script>
@endpush
