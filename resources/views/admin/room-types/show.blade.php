@extends('admin.layouts.app')

@section('title', 'Room Type - ' . $roomType->name)

@section('breadcrumb')
    <a href="{{ route('admin.room-types.index') }}">Room Types</a>
    <i class="fas fa-chevron-right"></i>
    <span>{{ $roomType->name }}</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $roomType->name }}</h1>
        <p class="page-subtitle">Room type configuration and statistics</p>
    </div>
    <div class="d-flex gap-2">
        @can('room_types.edit')
        <a href="{{ route('admin.room-types.edit', $roomType) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Room Type
        </a>
        @endcan
        <a href="{{ route('admin.rooms.index', ['room_type_id' => $roomType->id]) }}" class="btn btn-secondary">
            <i class="fas fa-door-open"></i> View Rooms
        </a>
    </div>
</div>

<div class="row">
    <!-- Main Content -->
    <div class="col-8">
        <!-- Room Type Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Room Type Details</h3>
                <span class="badge {{ $roomType->is_active ? 'badge-success' : 'badge-secondary' }}">
                    {{ $roomType->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="card-body">
                <div class="room-type-preview">
                    @if($roomType->images && count($roomType->images) > 0)
                        <div class="preview-gallery">
                            <img src="{{ asset('storage/' . $roomType->images[0]) }}" alt="{{ $roomType->name }}" class="preview-main">
                            @if(count($roomType->images) > 1)
                                <div class="preview-thumbs">
                                    @foreach(array_slice($roomType->images, 1, 4) as $image)
                                        <img src="{{ asset('storage/' . $image) }}" alt="{{ $roomType->name }}">
                                    @endforeach
                                    @if(count($roomType->images) > 5)
                                        <div class="more-images">+{{ count($roomType->images) - 5 }}</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="preview-placeholder">
                            <i class="fas fa-bed fa-4x"></i>
                            <p>No images uploaded</p>
                        </div>
                    @endif
                </div>

                <div class="detail-grid mt-4">
                    <div class="detail-item">
                        <label>Name</label>
                        <span>{{ $roomType->name }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Code</label>
                        <span>{{ $roomType->code }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Base Price</label>
                        <span>${{ number_format($roomType->base_price, 2) }}/night</span>
                    </div>
                    <div class="detail-item">
                        <label>Max Occupancy</label>
                        <span>{{ $roomType->max_occupancy }} guests</span>
                    </div>
                    <div class="detail-item">
                        <label>Size</label>
                        <span>{{ $roomType->size }} m²</span>
                    </div>
                    <div class="detail-item">
                        <label>Bed Configuration</label>
                        <span>{{ $roomType->bed_configuration ?? 'Not specified' }}</span>
                    </div>
                    <div class="detail-item full-width">
                        <label>Description</label>
                        <span>{{ $roomType->description ?? 'No description provided' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Amenities -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Amenities</h3>
                <span class="text-muted">{{ $roomType->amenities->count() }} amenities</span>
            </div>
            <div class="card-body">
                @if($roomType->amenities->count())
                    <div class="amenities-grid">
                        @foreach($roomType->amenities as $amenity)
                            <div class="amenity-item">
                                <i class="{{ $amenity->icon ?? 'fas fa-check' }}"></i>
                                <span>{{ $amenity->name }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center py-4">No amenities assigned to this room type.</p>
                @endif
            </div>
        </div>

        <!-- Rooms of This Type -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Rooms</h3>
                <a href="{{ route('admin.rooms.create', ['room_type_id' => $roomType->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Add Room
                </a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Room Number</th>
                                <th>Floor</th>
                                <th>Status</th>
                                <th>Cleanliness</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roomType->rooms as $room)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.rooms.show', $room) }}">
                                            <strong>{{ $room->room_number }}</strong>
                                        </a>
                                    </td>
                                    <td>Floor {{ $room->floor }}</td>
                                    <td>
                                        @switch($room->status)
                                            @case('available')
                                                <span class="badge badge-success">Available</span>
                                                @break
                                            @case('occupied')
                                                <span class="badge badge-danger">Occupied</span>
                                                @break
                                            @case('reserved')
                                                <span class="badge badge-info">Reserved</span>
                                                @break
                                            @case('maintenance')
                                                <span class="badge badge-warning">Maintenance</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ ucfirst($room->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($room->cleanliness === 'clean')
                                            <span class="text-success"><i class="fas fa-check-circle"></i> Clean</span>
                                        @elseif($room->cleanliness === 'dirty')
                                            <span class="text-warning"><i class="fas fa-exclamation-circle"></i> Dirty</span>
                                        @else
                                            <span class="text-info"><i class="fas fa-search"></i> Inspected</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.rooms.edit', $room) }}" class="btn btn-sm btn-icon btn-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No rooms of this type yet.
                                        <a href="{{ route('admin.rooms.create', ['room_type_id' => $roomType->id]) }}">Add one</a>
                                    </td>
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
        <!-- Stats -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Statistics</h3>
            </div>
            <div class="card-body">
                <div class="stat-list">
                    <div class="stat-list-item">
                        <span class="stat-list-label">Total Rooms</span>
                        <span class="stat-list-value">{{ $roomType->rooms->count() }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Available</span>
                        <span class="stat-list-value text-success">{{ $roomType->rooms->where('status', 'available')->count() }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Occupied</span>
                        <span class="stat-list-value text-danger">{{ $roomType->rooms->where('status', 'occupied')->count() }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label">Maintenance</span>
                        <span class="stat-list-value text-warning">{{ $roomType->rooms->where('status', 'maintenance')->count() }}</span>
                    </div>
                </div>

                <div class="occupancy-bar mt-4">
                    @php
                        $totalRooms = $roomType->rooms->count();
                        $occupied = $roomType->rooms->where('status', 'occupied')->count();
                        $occupancyRate = $totalRooms > 0 ? round(($occupied / $totalRooms) * 100) : 0;
                    @endphp
                    <div class="d-flex justify-between mb-2">
                        <span>Current Occupancy</span>
                        <strong>{{ $occupancyRate }}%</strong>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: {{ $occupancyRate }}%"></div>
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
                <div class="stat-list">
                    <div class="stat-list-item">
                        <span class="stat-list-label">Base Rate</span>
                        <span class="stat-list-value">${{ number_format($roomType->base_price, 2) }}</span>
                    </div>
                    @if($roomType->weekend_price)
                        <div class="stat-list-item">
                            <span class="stat-list-label">Weekend Rate</span>
                            <span class="stat-list-value">${{ number_format($roomType->weekend_price, 2) }}</span>
                        </div>
                    @endif
                    <div class="stat-list-item">
                        <span class="stat-list-label">Extra Person</span>
                        <span class="stat-list-value">${{ number_format($roomType->extra_person_charge ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Revenue (This Month)</h3>
            </div>
            <div class="card-body">
                <div class="revenue-display">
                    <div class="revenue-amount">${{ number_format($monthlyRevenue ?? 0, 2) }}</div>
                    <div class="revenue-label">Total revenue from {{ $bookingsThisMonth ?? 0 }} bookings</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('admin.bookings.create', ['room_type_id' => $roomType->id]) }}" class="btn btn-success btn-block">
                        <i class="fas fa-calendar-plus"></i> Create Booking
                    </a>
                    <a href="{{ route('admin.rooms.create', ['room_type_id' => $roomType->id]) }}" class="btn btn-primary btn-block">
                        <i class="fas fa-plus"></i> Add Room
                    </a>
                    @can('room_types.edit')
                    <a href="{{ route('admin.room-types.edit', $roomType) }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-edit"></i> Edit Room Type
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.room-type-preview {
    margin-bottom: 1.5rem;
}

.preview-gallery {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.preview-main {
    width: 100%;
    height: 300px;
    object-fit: cover;
    border-radius: 8px;
}

.preview-thumbs {
    display: flex;
    gap: 0.5rem;
}

.preview-thumbs img {
    width: calc(25% - 0.375rem);
    height: 80px;
    object-fit: cover;
    border-radius: 6px;
}

.more-images {
    width: calc(25% - 0.375rem);
    height: 80px;
    background: var(--gray-200);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: var(--gray-600);
}

.preview-placeholder {
    height: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: var(--gray-100);
    border-radius: 8px;
    color: var(--gray-400);
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

.detail-item.full-width {
    grid-column: span 2;
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

.amenities-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
}

.amenity-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: var(--gray-50);
    border-radius: 6px;
}

.amenity-item i {
    width: 20px;
    color: var(--primary);
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

.progress-bar-bg {
    height: 8px;
    background: var(--gray-200);
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.revenue-display {
    text-align: center;
    padding: 1rem 0;
}

.revenue-amount {
    font-size: 2rem;
    font-weight: 700;
    color: var(--success);
}

.revenue-label {
    font-size: 0.875rem;
    color: var(--gray-500);
    margin-top: 0.25rem;
}

.btn-block {
    width: 100%;
    text-align: center;
}
</style>
@endpush
