@extends('admin.layouts.app')

@section('title', 'Settings')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>Settings</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">System Settings</h1>
        <p class="page-subtitle">Configure your hotel website and system preferences</p>
    </div>
</div>

<div class="row">
    <div class="col-3">
        @include('admin.settings.partials.sidebar')
    </div>

    <div class="col-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">General Settings</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label">Site Name <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            name="site_name"
                            class="form-control @error('site_name') is-invalid @enderror"
                            value="{{ old('site_name', $settings['site_name'] ?? 'MK Hotel') }}"
                            required
                        >
                        @error('site_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Site Tagline</label>
                        <input
                            type="text"
                            name="site_tagline"
                            class="form-control @error('site_tagline') is-invalid @enderror"
                            value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}"
                            placeholder="Your tagline here..."
                        >
                        @error('site_tagline')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Site Description</label>
                        <textarea
                            name="site_description"
                            class="form-control @error('site_description') is-invalid @enderror"
                            rows="3"
                            placeholder="Brief description of your hotel..."
                        >{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                        @error('site_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-3">Contact Information</h5>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Email Address</label>
                                <input
                                    type="email"
                                    name="site_email"
                                    class="form-control @error('site_email') is-invalid @enderror"
                                    value="{{ old('site_email', $settings['site_email'] ?? '') }}"
                                    placeholder="contact@mkhotel.com"
                                >
                                @error('site_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Primary Phone</label>
                                <input
                                    type="tel"
                                    name="site_phone"
                                    class="form-control @error('site_phone') is-invalid @enderror"
                                    value="{{ old('site_phone', $settings['site_phone'] ?? '') }}"
                                    placeholder="+1 (555) 123-4567"
                                >
                                @error('site_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Secondary Phone</label>
                        <input
                            type="tel"
                            name="site_phone_secondary"
                            class="form-control @error('site_phone_secondary') is-invalid @enderror"
                            value="{{ old('site_phone_secondary', $settings['site_phone_secondary'] ?? '') }}"
                            placeholder="+1 (555) 987-6543"
                        >
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-3">Address</h5>

                    <div class="form-group">
                        <label class="form-label">Street Address</label>
                        <input
                            type="text"
                            name="site_address"
                            class="form-control @error('site_address') is-invalid @enderror"
                            value="{{ old('site_address', $settings['site_address'] ?? '') }}"
                            placeholder="123 Hotel Street"
                        >
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">City</label>
                                <input
                                    type="text"
                                    name="site_city"
                                    class="form-control @error('site_city') is-invalid @enderror"
                                    value="{{ old('site_city', $settings['site_city'] ?? '') }}"
                                    placeholder="New York"
                                >
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Country</label>
                                <input
                                    type="text"
                                    name="site_country"
                                    class="form-control @error('site_country') is-invalid @enderror"
                                    value="{{ old('site_country', $settings['site_country'] ?? '') }}"
                                    placeholder="United States"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Google Maps Embed Code</label>
                        <textarea
                            name="google_maps_embed"
                            class="form-control @error('google_maps_embed') is-invalid @enderror"
                            rows="3"
                            placeholder="Paste your Google Maps embed iframe code here..."
                        >{{ old('google_maps_embed', $settings['google_maps_embed'] ?? '') }}</textarea>
                        <small class="text-muted">Go to Google Maps, search for your location, click "Share" > "Embed a map" and paste the iframe code here.</small>
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-3">Footer</h5>

                    <div class="form-group">
                        <label class="form-label">Footer Text</label>
                        <textarea
                            name="footer_text"
                            class="form-control @error('footer_text') is-invalid @enderror"
                            rows="2"
                            placeholder="Text to display in the footer..."
                        >{{ old('footer_text', $settings['footer_text'] ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Copyright Text</label>
                        <input
                            type="text"
                            name="copyright_text"
                            class="form-control @error('copyright_text') is-invalid @enderror"
                            value="{{ old('copyright_text', $settings['copyright_text'] ?? '') }}"
                            placeholder="© 2024 MK Hotel. All rights reserved."
                        >
                    </div>

                    <div class="d-flex justify-between mt-4">
                        <button type="button" class="btn btn-secondary" onclick="location.reload()">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-item:hover {
        background: var(--gray-100);
    }
    .nav-item.active {
        background: rgba(37, 99, 235, 0.1);
        color: var(--primary) !important;
    }
</style>
@endsection
