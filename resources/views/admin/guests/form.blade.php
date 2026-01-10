@extends('admin.layouts.app')

@section('title', isset($guest) ? 'Edit Guest' : 'Add Guest')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <a href="{{ route('admin.guests.index') }}">Guests</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>{{ isset($guest) ? 'Edit Guest' : 'Add Guest' }}</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ isset($guest) ? 'Edit Guest' : 'Add Guest' }}</h1>
        <p class="page-subtitle">{{ isset($guest) ? 'Update guest profile information' : 'Create a new guest profile' }}</p>
    </div>
</div>

<form action="{{ isset($guest) ? route('admin.guests.update', $guest) : route('admin.guests.store') }}" method="POST">
    @csrf
    @if(isset($guest))
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-8">
            <!-- Personal Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Personal Information</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                                       value="{{ old('first_name', $guest->first_name ?? '') }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                                       value="{{ old('last_name', $guest->last_name ?? '') }}" required>
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
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $guest->email ?? '') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $guest->phone ?? '') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror"
                                       value="{{ old('date_of_birth', isset($guest->date_of_birth) ? $guest->date_of_birth->format('Y-m-d') : '') }}">
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-control @error('gender') is-invalid @enderror">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $guest->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $guest->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $guest->gender ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Identification -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Identification</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">ID Type</label>
                                <select name="id_type" class="form-control @error('id_type') is-invalid @enderror">
                                    <option value="">Select ID Type</option>
                                    <option value="passport" {{ old('id_type', $guest->id_type ?? '') == 'passport' ? 'selected' : '' }}>Passport</option>
                                    <option value="national_id" {{ old('id_type', $guest->id_type ?? '') == 'national_id' ? 'selected' : '' }}>National ID</option>
                                    <option value="drivers_license" {{ old('id_type', $guest->id_type ?? '') == 'drivers_license' ? 'selected' : '' }}>Driver's License</option>
                                    <option value="other" {{ old('id_type', $guest->id_type ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('id_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">ID Number</label>
                                <input type="text" name="id_number" class="form-control @error('id_number') is-invalid @enderror"
                                       value="{{ old('id_number', $guest->id_number ?? '') }}">
                                @error('id_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Address</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Street Address</label>
                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                               value="{{ old('address', $guest->address ?? '') }}">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                                       value="{{ old('city', $guest->city ?? '') }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">State/Province</label>
                                <input type="text" name="state" class="form-control @error('state') is-invalid @enderror"
                                       value="{{ old('state', $guest->state ?? '') }}">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Postal Code</label>
                                <input type="text" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror"
                                       value="{{ old('postal_code', $guest->postal_code ?? '') }}">
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Country</label>
                                <select name="country" class="form-control @error('country') is-invalid @enderror">
                                    <option value="">Select Country</option>
                                    @foreach($countries ?? [] as $code => $name)
                                        <option value="{{ $name }}" data-code="{{ $code }}" {{ old('country', $guest->country ?? '') == $name ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="country_code" value="{{ old('country_code', $guest->country_code ?? '') }}">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Additional Information</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Company</label>
                        <input type="text" name="company" class="form-control @error('company') is-invalid @enderror"
                               value="{{ old('company', $guest->company ?? '') }}">
                        @error('company')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Preferences & Notes</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                                  placeholder="Dietary requirements, room preferences, allergies, etc.">{{ old('notes', $guest->notes ?? '') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-4">
            <!-- Guest Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Status</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="is_vip" value="1" class="form-check-input"
                                   {{ old('is_vip', $guest->is_vip ?? false) ? 'checked' : '' }}>
                            <span class="form-check-label">
                                <strong><i class="fas fa-star text-warning"></i> VIP Guest</strong>
                                <p class="text-muted mb-0" style="font-size: 0.75rem;">Mark this guest as a VIP for special treatment</p>
                            </span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="marketing_consent" value="1" class="form-check-input"
                                   {{ old('marketing_consent', $guest->marketing_consent ?? false) ? 'checked' : '' }}>
                            <span class="form-check-label">
                                <strong>Marketing Consent</strong>
                                <p class="text-muted mb-0" style="font-size: 0.75rem;">Guest has agreed to receive promotional emails</p>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            @if(isset($guest))
            <!-- Guest Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Guest Summary</h3>
                </div>
                <div class="card-body">
                    <div class="summary-stats">
                        <div class="d-flex justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                            <span class="text-muted">Total Bookings</span>
                            <strong>{{ $guest->bookings_count ?? 0 }}</strong>
                        </div>
                        <div class="d-flex justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                            <span class="text-muted">Total Spent</span>
                            <strong>${{ number_format($guest->total_spent ?? 0, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-between py-2" style="border-bottom: 1px solid var(--gray-100);">
                            <span class="text-muted">First Visit</span>
                            <span>{{ $guest->first_stay ? \Carbon\Carbon::parse($guest->first_stay)->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-between py-2">
                            <span class="text-muted">Last Visit</span>
                            <span>{{ $guest->last_stay ? \Carbon\Carbon::parse($guest->last_stay)->format('M d, Y') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-save"></i> {{ isset($guest) ? 'Update Guest' : 'Create Guest' }}
                    </button>
                    <a href="{{ route('admin.guests.index') }}" class="btn btn-secondary mt-2" style="width: 100%;">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update country code when country is selected
    const countrySelect = document.querySelector('select[name="country"]');
    const countryCodeInput = document.querySelector('input[name="country_code"]');

    if (countrySelect && countryCodeInput) {
        countrySelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            countryCodeInput.value = selectedOption.dataset.code || '';
        });
    }
});
</script>
@endpush
