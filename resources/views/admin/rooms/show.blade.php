@extends('admin.layouts.app')

@section('title', 'Room Details - ' . $room->room_number)

@section('breadcrumb')
    <a href="{{ route('admin.rooms.index') }}">Rooms</a>
    <i class="fas fa-chevron-right"></i>
    <span>{{ $room->room_number }}</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Room {{ $room->room_number }}</h1>
        <p class="page-subtitle">{{ $room->roomType->name ?? 'Unknown Type' }} &bull; Floor {{ $room->floor }}</p>
    </div>
    <div class="d-flex gap-2">
        @if($room->status === 'available')
            <a href="{{ route('admin.bookings.create', ['room_id' => $room->id]) }}" class="btn btn-success">
                <i class="fas fa-calendar-plus"></i> Create Booking
            </a>
        @endif
        @can('rooms.edit')
        <a href="{{ route('admin.rooms.edit', $room) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Room
        </a>
        @endcan
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                <i class="fas fa-cog"></i> Actions
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="#" onclick="event.preventDefault(); updateStatus('available');">
                    <i class="fas fa-check-circle text-success"></i> Mark Available
                </a>
                <a class="dropdown-item" href="#" onclick="event.preventDefault(); updateStatus('maintenance');">
                    <i class="fas fa-tools text-warning"></i> Set to Maintenance
                </a>
                <a class="dropdown-item" href="#" onclick="event.preventDefault(); updateStatus('out_of_order');">
                    <i class="fas fa-ban text-danger"></i> Mark Out of Order
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('updateCleanForm').submit();">
                    <i class="fas fa-broom text-info"></i> Mark as Clean
                </a>
                <a class="dropdown-item" href="#" onclick="event.preventDefault(); updateCleanliness('dirty');">
                    <i class="fas fa-exclamation-triangle text-warning"></i> Mark as Dirty
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Main Info -->
    <div class="col-8">
        <!-- Room Status Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Room Status</h3>
                <div class="d-flex gap-2">
                    @switch($room->status)
                        @case('available')
                            <span class="badge badge-success badge-lg"><i class="fas fa-check-circle"></i> Available</span>
                            @break
                        @case('occupied')
                            <span class="badge badge-danger badge-lg"><i class="fas fa-user"></i> Occupied</span>
                            @break
                        @case('reserved')
                            <span class="badge badge-info badge-lg"><i class="fas fa-clock"></i> Reserved</span>
                            @break
                        @case('maintenance')
                            <span class="badge badge-warning badge-lg"><i class="fas fa-tools"></i> Maintenance</span>
                            @break
                        @case('out_of_order')
                            <span class="badge badge-secondary badge-lg"><i class="fas fa-ban"></i> Out of Order</span>
                            @break
                    @endswitch

                    @if($room->cleanliness === 'clean')
                        <span class="badge badge-success"><i class="fas fa-broom"></i> Clean</span>
                    @elseif($room->cleanliness === 'dirty')
                        <span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Dirty</span>
                    @else
                        <span class="badge badge-info"><i class="fas fa-spray-can"></i> Inspected</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if($room->currentBooking)
                    <div class="current-booking-alert">
                        <div class="alert alert-info mb-0">
                            <div class="d-flex align-center justify-between">
                                <div>
                                    <strong><i class="fas fa-user"></i> Current Guest</strong>
                                    <p class="mb-0">{{ $room->currentBooking->guest_name }}</p>
                                    <small>
                                        Check-out: {{ $room->currentBooking->check_out->format('M d, Y') }}
                                        ({{ $room->currentBooking->check_out->diffForHumans() }})
                                    </small>
                                </div>
                                <a href="{{ route('admin.bookings.show', $room->currentBooking) }}" class="btn btn-primary btn-sm">
                                    View Booking
                                </a>
                            </div>
                        </div>
                    </div>
                @elseif($room->nextBooking)
                    <div class="next-booking-alert">
                        <div class="alert alert-warning mb-0">
                            <div class="d-flex align-center justify-between">
                                <div>
                                    <strong><i class="fas fa-calendar-alt"></i> Next Booking</strong>
                                    <p class="mb-0">{{ $room->nextBooking->guest_name }}</p>
                                    <small>
                                        Check-in: {{ $room->nextBooking->check_in->format('M d, Y') }}
                                        ({{ $room->nextBooking->check_in->diffForHumans() }})
                                    </small>
                                </div>
                                <a href="{{ route('admin.bookings.show', $room->nextBooking) }}" class="btn btn-warning btn-sm">
                                    View Booking
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-calendar-check fa-3x mb-3" style="opacity: 0.3;"></i>
                        <p>No current or upcoming bookings</p>
                        <a href="{{ route('admin.bookings.create', ['room_id' => $room->id]) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Booking
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Room Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Room Details</h3>
            </div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Room Number</label>
                        <span>{{ $room->room_number }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Room Type</label>
                        <span>{{ $room->roomType->name ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Floor</label>
                        <span>{{ $room->floor }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Building/Wing</label>
                        <span>{{ $room->building ?? 'Main' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Size</label>
                        <span>{{ $room->size ?? 'N/A' }} m²</span>
                    </div>
                    <div class="detail-item">
                        <label>View</label>
                        <span>{{ ucfirst($room->view ?? 'N/A') }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Max Occupancy</label>
                        <span>{{ $room->max_occupancy }} guests</span>
                    </div>
                    <div class="detail-item">
                        <label>Base Price</label>
                        <span>${{ number_format($room->price_override ?? $room->roomType->base_price ?? 0, 2) }}/night</span>
                    </div>
                </div>

                @if($room->beds)
                    <div class="mt-4">
                        <label class="mb-2"><strong>Bed Configuration</strong></label>
                        <div class="beds-display">
                            @foreach($room->beds as $bed)
                                <span class="bed-tag">
                                    <i class="fas fa-bed"></i> {{ $bed['quantity'] }}× {{ ucfirst($bed['type']) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($room->notes)
                    <div class="mt-4">
                        <label class="mb-2"><strong>Notes</strong></label>
                        <p class="text-muted">{{ $room->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Amenities -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Room Amenities</h3>
            </div>
            <div class="card-body">
                @if($room->roomType && $room->roomType->amenities->count())
                    <div class="amenities-grid">
                        @foreach($room->roomType->amenities as $amenity)
                            <div class="amenity-item">
                                <i class="{{ $amenity->icon ?? 'fas fa-check' }}"></i>
                                <span>{{ $amenity->name }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center">No amenities configured for this room type.</p>
                @endif
            </div>
        </div>

        <!-- Booking History -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Booking History</h3>
                <a href="{{ route('admin.bookings.index', ['room_id' => $room->id]) }}" class="btn btn-sm btn-secondary">View All</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Guest</th>
                                <th>Dates</th>
                                <th>Status</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($room->bookings()->latest()->take(10)->get() as $booking)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.bookings.show', $booking) }}">
                                            {{ $booking->booking_reference }}
                                        </a>
                                    </td>
                                    <td>{{ $booking->guest_name }}</td>
                                    <td>
                                        {{ $booking->check_in->format('M d') }} - {{ $booking->check_out->format('M d, Y') }}
                                        <small class="d-block text-muted">{{ $booking->nights }} night(s)</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $booking->status_color }}">{{ ucfirst($booking->status) }}</span>
                                    </td>
                                    <td>${{ number_format($booking->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No booking history</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-4">
        <!-- Room Images -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Room Images</h3>
            </div>
            <div class="card-body">
                @if($room->images && count($room->images) > 0)
                    <div class="room-gallery">
                        @foreach($room->images as $index => $image)
                            <img src="{{ asset('storage/' . $image) }}"
                                 alt="Room {{ $room->room_number }}"
                                 class="room-image {{ $index === 0 ? 'main' : '' }}"
                                 onclick="openImageModal('{{ asset('storage/' . $image) }}')">
                        @endforeach
                    </div>
                @elseif($room->roomType && $room->roomType->images)
                    <div class="room-gallery">
                        @foreach($room->roomType->images as $index => $image)
                            <img src="{{ asset('storage/' . $image) }}"
                                 alt="{{ $room->roomType->name }}"
                                 class="room-image {{ $index === 0 ? 'main' : '' }}"
                                 onclick="openImageModal('{{ asset('storage/' . $image) }}')">
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-image fa-3x mb-2" style="opacity: 0.3;"></i>
                        <p>No images available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Statistics</h3>
            </div>
            <div class="card-body">
                <div class="stat-list">
                    <div class="stat-list-item">
                        <span class="stat-list-label">Total Bookings</span>
                        <span class="stat-list-value">{{ $room->bookings()->count() }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">This Month</span>
                        <span class="stat-list-value">{{ $room->bookings()->whereMonth('created_at', now()->month)->count() }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Avg. Rating</span>
                        <span class="stat-list-value">
                            @if($room->avg_rating)
                                <i class="fas fa-star text-warning"></i> {{ number_format($room->avg_rating, 1) }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Last Occupied</span>
                        <span class="stat-list-value">
                            @if($room->lastBooking)
                                {{ $room->lastBooking->check_out->diffForHumans() }}
                            @else
                                <span class="text-muted">Never</span>
                            @endif
                        </span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Revenue (MTD)</span>
                        <span class="stat-list-value">${{ number_format($room->monthlyRevenue ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Activity</h3>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @forelse($room->activityLogs()->latest()->take(5)->get() as $log)
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $log->type === 'status_change' ? 'warning' : 'primary' }}"></div>
                            <div class="timeline-content">
                                <p class="timeline-title">{{ $log->description }}</p>
                                <small class="timeline-time">
                                    {{ $log->created_at->diffForHumans() }}
                                    @if($log->user)
                                        by {{ $log->user->name }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">No recent activity</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Forms -->
<form id="updateStatusForm" action="{{ route('admin.rooms.update-status', $room) }}" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status" id="statusInput">
</form>

<form id="updateCleanForm" action="{{ route('admin.rooms.update-cleanliness', $room) }}" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="cleanliness" value="clean">
</form>

<form id="updateCleanlinessForm" action="{{ route('admin.rooms.update-cleanliness', $room) }}" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="cleanliness" id="cleanlinessInput">
</form>

<!-- Image Modal -->
<div id="imageModal" class="modal" onclick="closeImageModal()">
    <span class="modal-close">&times;</span>
    <img id="modalImage" class="modal-content">
</div>

@endsection

@push('styles')
<style>
.badge-lg {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.detail-item label {
    font-size: 0.75rem;
    color: var(--gray-500);
    text-transform: uppercase;
    font-weight: 600;
}

.detail-item span {
    font-weight: 500;
    color: var(--gray-800);
}

.beds-display {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.bed-tag {
    background: var(--gray-100);
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    color: var(--gray-700);
}

.bed-tag i {
    margin-right: 0.25rem;
    color: var(--gray-500);
}

.amenities-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.amenity-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    background: var(--gray-50);
    border-radius: 6px;
}

.amenity-item i {
    width: 20px;
    color: var(--primary);
}

.room-gallery {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
}

.room-image {
    width: 100%;
    height: 80px;
    object-fit: cover;
    border-radius: 6px;
    cursor: pointer;
    transition: var(--transition);
}

.room-image:hover {
    opacity: 0.8;
}

.room-image.main {
    grid-column: span 3;
    height: 180px;
}

.stat-list {
    display: flex;
    flex-direction: column;
}

.stat-list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--gray-100);
}

.stat-list-item:last-child {
    border-bottom: none;
}

.stat-list-label {
    color: var(--gray-600);
    font-size: 0.875rem;
}

.stat-list-value {
    font-weight: 600;
    color: var(--gray-800);
}

.timeline {
    position: relative;
    padding-left: 1.5rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 6px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--gray-200);
}

.timeline-item {
    position: relative;
    padding-bottom: 1rem;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -1.5rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    top: 0.25rem;
}

.timeline-marker.primary {
    background: var(--primary);
}

.timeline-marker.warning {
    background: var(--warning);
}

.timeline-marker.success {
    background: var(--success);
}

.timeline-title {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.timeline-time {
    color: var(--gray-500);
    font-size: 0.75rem;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 9999;
    padding-top: 60px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.9);
}

.modal-content {
    margin: auto;
    display: block;
    max-width: 80%;
    max-height: 80%;
}

.modal-close {
    position: absolute;
    top: 15px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
}

.dropdown-menu {
    display: none;
    position: absolute;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    min-width: 200px;
    z-index: 100;
}

.dropdown:hover .dropdown-menu,
.dropdown.active .dropdown-menu {
    display: block;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    color: var(--gray-700);
    text-decoration: none;
    transition: var(--transition);
}

.dropdown-item:hover {
    background: var(--gray-100);
}

.dropdown-divider {
    border-top: 1px solid var(--gray-200);
    margin: 0.25rem 0;
}
</style>
@endpush

@push('scripts')
<script>
function updateStatus(status) {
    if (confirm('Are you sure you want to change the room status to ' + status + '?')) {
        document.getElementById('statusInput').value = status;
        document.getElementById('updateStatusForm').submit();
    }
}

function updateCleanliness(status) {
    document.getElementById('cleanlinessInput').value = status;
    document.getElementById('updateCleanlinessForm').submit();
}

function openImageModal(src) {
    document.getElementById('imageModal').style.display = 'block';
    document.getElementById('modalImage').src = src;
}

function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});

// Dropdown toggle
document.querySelectorAll('.dropdown-toggle').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        this.closest('.dropdown').classList.toggle('active');
    });
});

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown.active').forEach(function(d) {
            d.classList.remove('active');
        });
    }
});
</script>
@endpush
