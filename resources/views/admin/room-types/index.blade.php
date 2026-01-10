@extends('admin.layouts.app')

@section('title', 'Room Types')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>Room Types</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Room Types</h1>
        <p class="page-subtitle">Manage hotel room categories and pricing</p>
    </div>
    <div class="page-actions">
        @can('room-types.create')
        <a href="{{ route('admin.room-types.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Room Type
        </a>
        @endcan
    </div>
</div>

<!-- Room Types Grid -->
<div class="room-types-grid">
    @forelse($roomTypes ?? [] as $roomType)
    <div class="card room-type-card">
        <div class="room-type-image">
            @if($roomType->featured_image)
                <img src="{{ $roomType->featured_image }}" alt="{{ $roomType->name }}">
            @else
                <div class="no-image">
                    <i class="fas fa-bed"></i>
                </div>
            @endif
            <div class="room-type-badge">
                <span class="badge badge-{{ $roomType->is_active ? 'success' : 'secondary' }}">
                    {{ $roomType->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <h3 class="room-type-name">{{ $roomType->name }}</h3>
            <p class="room-type-description">{{ Str::limit($roomType->description, 100) }}</p>

            <div class="room-type-details">
                <div class="detail-item">
                    <i class="fas fa-users"></i>
                    <span>{{ $roomType->max_occupancy }} guests</span>
                </div>
                <div class="detail-item">
                    <i class="fas fa-bed"></i>
                    <span>{{ $roomType->bed_type ?? 'Standard' }}</span>
                </div>
                <div class="detail-item">
                    <i class="fas fa-ruler-combined"></i>
                    <span>{{ $roomType->size ?? 'N/A' }} m²</span>
                </div>
            </div>

            <div class="room-type-pricing">
                <div class="price-main">
                    <span class="price-label">Base Price</span>
                    <span class="price-value">${{ number_format($roomType->base_price, 0) }}</span>
                    <span class="price-period">/night</span>
                </div>
                @if($roomType->weekend_price)
                <div class="price-secondary">
                    <span>Weekend: ${{ number_format($roomType->weekend_price, 0) }}</span>
                </div>
                @endif
            </div>

            <div class="room-type-stats">
                <div class="stat">
                    <span class="stat-value">{{ $roomType->rooms_count ?? 0 }}</span>
                    <span class="stat-label">Rooms</span>
                </div>
                <div class="stat">
                    <span class="stat-value">{{ $roomType->available_rooms_count ?? 0 }}</span>
                    <span class="stat-label">Available</span>
                </div>
                <div class="stat">
                    <span class="stat-value">{{ $roomType->amenities_count ?? (is_array($roomType->amenities) ? count($roomType->amenities) : 0) }}</span>
                    <span class="stat-label">Amenities</span>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-between">
            <a href="{{ route('admin.room-types.show', $roomType) }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-eye"></i> View
            </a>
            <div class="d-flex gap-1">
                @can('room-types.edit')
                <a href="{{ route('admin.room-types.edit', $roomType) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @endcan
                @can('room-types.delete')
                <form action="{{ route('admin.room-types.destroy', $roomType) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Are you sure? This will also affect rooms of this type.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
                @endcan
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-4">
                <i class="fas fa-bed fa-3x text-muted mb-3"></i>
                <p class="text-muted">No room types found</p>
                @can('room-types.create')
                <a href="{{ route('admin.room-types.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add First Room Type
                </a>
                @endcan
            </div>
        </div>
    </div>
    @endforelse
</div>

<style>
.room-types-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
}

.room-type-card {
    overflow: hidden;
}

.room-type-image {
    position: relative;
    height: 200px;
    background: var(--gray-100);
}

.room-type-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.room-type-image .no-image {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-400);
    font-size: 3rem;
}

.room-type-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
}

.room-type-name {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.room-type-description {
    color: var(--gray-600);
    font-size: 0.875rem;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.room-type-details {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--gray-200);
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--gray-600);
}

.detail-item i {
    color: var(--gray-400);
}

.room-type-pricing {
    margin-bottom: 1rem;
}

.price-main {
    display: flex;
    align-items: baseline;
    gap: 0.25rem;
}

.price-label {
    font-size: 0.75rem;
    color: var(--gray-500);
    margin-right: 0.5rem;
}

.price-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary);
}

.price-period {
    font-size: 0.875rem;
    color: var(--gray-500);
}

.price-secondary {
    font-size: 0.75rem;
    color: var(--gray-500);
    margin-top: 0.25rem;
}

.room-type-stats {
    display: flex;
    gap: 1.5rem;
}

.room-type-stats .stat {
    text-align: center;
}

.room-type-stats .stat-value {
    display: block;
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--gray-900);
}

.room-type-stats .stat-label {
    font-size: 0.75rem;
    color: var(--gray-500);
}
</style>
@endsection
