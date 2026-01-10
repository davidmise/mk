@extends('admin.layouts.app')

@section('title', 'Guests')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>Guests</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Guests</h1>
        <p class="page-subtitle">Manage hotel guests and their profiles</p>
    </div>
    <div class="page-actions">
        @can('guests.create')
        <a href="{{ route('admin.guests.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Guest
        </a>
        @endcan
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 1.5rem;">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $stats['total_guests'] ?? 0 }}</div>
            <div class="stat-label">Total Guests</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $stats['checked_in'] ?? 0 }}</div>
            <div class="stat-label">Currently Staying</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-star"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $stats['vip_guests'] ?? 0 }}</div>
            <div class="stat-label">VIP Guests</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon cyan">
            <i class="fas fa-redo"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $stats['repeat_guests'] ?? 0 }}</div>
            <div class="stat-label">Repeat Visitors</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.guests.index') }}" method="GET" class="d-flex align-center gap-3 flex-wrap">
            <div class="form-group mb-0" style="flex: 1; min-width: 200px;">
                <input type="text" name="search" class="form-control" placeholder="Search by name, email, phone..." value="{{ request('search') }}">
            </div>
            <div class="form-group mb-0" style="min-width: 150px;">
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Currently Staying</option>
                    <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                    <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming Stay</option>
                </select>
            </div>
            <div class="form-group mb-0" style="min-width: 150px;">
                <select name="type" class="form-control">
                    <option value="">All Types</option>
                    <option value="vip" {{ request('type') == 'vip' ? 'selected' : '' }}>VIP</option>
                    <option value="repeat" {{ request('type') == 'repeat' ? 'selected' : '' }}>Repeat</option>
                    <option value="new" {{ request('type') == 'new' ? 'selected' : '' }}>New</option>
                </select>
            </div>
            <div class="form-group mb-0">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
                @if(request()->hasAny(['search', 'status', 'type']))
                <a href="{{ route('admin.guests.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Guests Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Guest</th>
                        <th>Contact</th>
                        <th>Country</th>
                        <th>Bookings</th>
                        <th>Total Spent</th>
                        <th>Last Stay</th>
                        <th>Status</th>
                        <th width="100">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guests ?? [] as $guest)
                    <tr>
                        <td>
                            <div class="d-flex align-center gap-3">
                                <div class="avatar" style="width: 40px; height: 40px; border-radius: 50%; background: var(--gray-200); display: flex; align-items: center; justify-content: center; font-weight: 600; color: var(--gray-600);">
                                    {{ strtoupper(substr($guest->first_name, 0, 1) . substr($guest->last_name, 0, 1)) }}
                                </div>
                                <div>
                                    <strong>{{ $guest->full_name }}</strong>
                                    @if($guest->is_vip)
                                        <span class="badge badge-warning" style="margin-left: 0.25rem;">
                                            <i class="fas fa-star"></i> VIP
                                        </span>
                                    @endif
                                    @if($guest->id_type && $guest->id_number)
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            {{ ucwords(str_replace('_', ' ', $guest->id_type)) }}: {{ $guest->id_number }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 0.875rem;">
                                @if($guest->email)
                                    <div><i class="fas fa-envelope text-muted"></i> {{ $guest->email }}</div>
                                @endif
                                @if($guest->phone)
                                    <div><i class="fas fa-phone text-muted"></i> {{ $guest->phone }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($guest->country)
                                <span class="d-flex align-center gap-1">
                                    <span class="fi fi-{{ strtolower($guest->country_code ?? 'xx') }}"></span>
                                    {{ $guest->country }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div>
                                <strong>{{ $guest->bookings_count ?? 0 }}</strong>
                                @if(($guest->bookings_count ?? 0) > 1)
                                    <span class="badge badge-info" style="margin-left: 0.25rem;">Repeat</span>
                                @endif
                            </div>
                        </td>
                        <td>${{ number_format($guest->total_spent ?? 0, 2) }}</td>
                        <td>
                            @if($guest->last_stay)
                                {{ \Carbon\Carbon::parse($guest->last_stay)->format('M d, Y') }}
                            @else
                                <span class="text-muted">Never</span>
                            @endif
                        </td>
                        <td>
                            @if($guest->current_booking)
                                <span class="badge badge-success">Currently Staying</span>
                            @elseif($guest->upcoming_booking)
                                <span class="badge badge-info">Upcoming Stay</span>
                            @else
                                <span class="badge badge-secondary">Past Guest</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.guests.show', $guest) }}" class="btn btn-sm btn-secondary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('guests.edit')
                                <a href="{{ route('admin.guests.edit', $guest) }}" class="btn btn-sm btn-secondary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('bookings.create')
                                <a href="{{ route('admin.bookings.create', ['guest_id' => $guest->id]) }}" class="btn btn-sm btn-primary" title="New Booking">
                                    <i class="fas fa-plus"></i>
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <p>No guests found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(isset($guests) && $guests->hasPages())
    <div class="card-footer">
        {{ $guests->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
