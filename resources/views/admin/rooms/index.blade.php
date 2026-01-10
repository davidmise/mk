@extends('admin.layouts.app')

@section('title', 'Rooms')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>Rooms</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Room Management</h1>
        <p class="page-subtitle">Manage your hotel room inventory</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Room
        </a>
    </div>
</div>

<!-- Room Status Overview -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-door-open"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Available</div>
            <div class="stat-value">{{ $stats['available'] ?? 0 }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon red">
            <i class="fas fa-user"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Occupied</div>
            <div class="stat-value">{{ $stats['occupied'] ?? 0 }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Reserved</div>
            <div class="stat-value">{{ $stats['reserved'] ?? 0 }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon cyan">
            <i class="fas fa-broom"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Cleaning</div>
            <div class="stat-value">{{ $stats['cleaning'] ?? 0 }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-wrench"></i>
        </div>
        <div class="stat-content">
            <div class="stat-label">Maintenance</div>
            <div class="stat-value">{{ $stats['maintenance'] ?? 0 }}</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.rooms.index') }}" method="GET" class="d-flex gap-3 align-center" style="flex-wrap: wrap;">
            <div class="form-group mb-0" style="min-width: 150px;">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Search room number..."
                    value="{{ request('search') }}"
                >
            </div>

            <div class="form-group mb-0">
                <select name="room_type_id" class="form-control">
                    <option value="">All Room Types</option>
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}" {{ request('room_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-0">
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                    <option value="occupied" {{ request('status') === 'occupied' ? 'selected' : '' }}>Occupied</option>
                    <option value="reserved" {{ request('status') === 'reserved' ? 'selected' : '' }}>Reserved</option>
                    <option value="cleaning" {{ request('status') === 'cleaning' ? 'selected' : '' }}>Cleaning</option>
                    <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>

            <div class="form-group mb-0">
                <select name="floor" class="form-control">
                    <option value="">All Floors</option>
                    @foreach($floors ?? [] as $floor)
                        <option value="{{ $floor }}" {{ request('floor') == $floor ? 'selected' : '' }}>
                            Floor {{ $floor }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Filter
            </button>

            @if(request()->hasAny(['search', 'room_type_id', 'status', 'floor']))
                <a href="{{ route('admin.rooms.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </form>
    </div>
</div>

<!-- Rooms Grid -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Rooms ({{ $rooms->total() }})</h3>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-secondary" id="gridViewBtn">
                <i class="fas fa-th-large"></i>
            </button>
            <button type="button" class="btn btn-sm btn-secondary" id="listViewBtn">
                <i class="fas fa-list"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Grid View -->
        <div id="gridView" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
            @forelse($rooms as $room)
            <div class="room-card" style="
                background: white;
                border: 1px solid var(--gray-200);
                border-radius: 12px;
                padding: 1rem;
                position: relative;
                transition: all 0.2s;
            ">
                <div class="d-flex justify-between align-center mb-2">
                    <strong style="font-size: 1.25rem;">{{ $room->room_number }}</strong>
                    @switch($room->status)
                        @case('available')
                            <span class="badge badge-success">Available</span>
                            @break
                        @case('occupied')
                            <span class="badge badge-danger">Occupied</span>
                            @break
                        @case('reserved')
                            <span class="badge badge-warning">Reserved</span>
                            @break
                        @case('cleaning')
                            <span class="badge badge-info">Cleaning</span>
                            @break
                        @case('maintenance')
                            <span class="badge badge-secondary">Maintenance</span>
                            @break
                    @endswitch
                </div>

                <p class="text-muted mb-2" style="font-size: 0.875rem;">
                    {{ $room->roomType->name ?? '-' }}
                </p>

                <p class="text-muted mb-2" style="font-size: 0.75rem;">
                    <i class="fas fa-layer-group"></i> Floor {{ $room->floor }}
                </p>

                @if($room->currentBooking)
                <div style="background: var(--gray-50); border-radius: 8px; padding: 0.5rem; margin-top: 0.5rem; font-size: 0.75rem;">
                    <div class="d-flex justify-between">
                        <span class="text-muted">Guest:</span>
                        <strong>{{ $room->currentBooking->guest?->full_name ?? $room->currentBooking->guest_name }}</strong>
                    </div>
                    <div class="d-flex justify-between">
                        <span class="text-muted">Check-out:</span>
                        <strong>{{ $room->currentBooking->check_out_date->format('M d') }}</strong>
                    </div>
                </div>
                @endif

                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('admin.rooms.show', $room) }}" class="btn btn-sm btn-secondary" style="flex: 1;">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.rooms.edit', $room) }}" class="btn btn-sm btn-secondary" style="flex: 1;">
                        <i class="fas fa-edit"></i>
                    </a>
                    <div class="dropdown" style="flex: 1;">
                        <button type="button" class="btn btn-sm btn-secondary w-100" onclick="this.nextElementSibling.classList.toggle('show')">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu" style="min-width: 150px;">
                            <form action="{{ route('admin.rooms.update-status', $room) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="available">
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-check text-success"></i> Set Available
                                </button>
                            </form>
                            <form action="{{ route('admin.rooms.update-status', $room) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="cleaning">
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-broom text-info"></i> Set Cleaning
                                </button>
                            </form>
                            <form action="{{ route('admin.rooms.update-status', $room) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="maintenance">
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-wrench text-warning"></i> Set Maintenance
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--gray-500);">
                <i class="fas fa-door-closed" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                No rooms found
            </div>
            @endforelse
        </div>

        <!-- List View (Hidden by default) -->
        <div id="listView" style="display: none;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Type</th>
                            <th>Floor</th>
                            <th>Status</th>
                            <th>Current Guest</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rooms as $room)
                        <tr>
                            <td><strong>{{ $room->room_number }}</strong></td>
                            <td>{{ $room->roomType->name ?? '-' }}</td>
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
                                        <span class="badge badge-warning">Reserved</span>
                                        @break
                                    @case('cleaning')
                                        <span class="badge badge-info">Cleaning</span>
                                        @break
                                    @case('maintenance')
                                        <span class="badge badge-secondary">Maintenance</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @if($room->currentBooking)
                                    {{ $room->currentBooking->guest?->full_name ?? $room->currentBooking->guest_name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.rooms.show', $room) }}" class="btn btn-sm btn-secondary btn-icon">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.rooms.edit', $room) }}" class="btn btn-sm btn-secondary btn-icon">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($rooms->hasPages())
    <div class="card-body" style="border-top: 1px solid var(--gray-200);">
        {{ $rooms->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const gridViewBtn = document.getElementById('gridViewBtn');
    const listViewBtn = document.getElementById('listViewBtn');
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');

    gridViewBtn.addEventListener('click', function() {
        gridView.style.display = 'grid';
        listView.style.display = 'none';
        gridViewBtn.classList.add('btn-primary');
        gridViewBtn.classList.remove('btn-secondary');
        listViewBtn.classList.add('btn-secondary');
        listViewBtn.classList.remove('btn-primary');
    });

    listViewBtn.addEventListener('click', function() {
        gridView.style.display = 'none';
        listView.style.display = 'block';
        listViewBtn.classList.add('btn-primary');
        listViewBtn.classList.remove('btn-secondary');
        gridViewBtn.classList.add('btn-secondary');
        gridViewBtn.classList.remove('btn-primary');
    });
});
</script>
@endpush
