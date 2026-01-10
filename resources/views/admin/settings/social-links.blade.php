@extends('admin.layouts.app')

@section('title', 'Social Links')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <a href="{{ route('admin.settings.index') }}">Settings</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>Social Links</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Social Links</h1>
        <p class="page-subtitle">Configure social media links for your website</p>
    </div>
</div>

<div class="row">
    <div class="col-3">
        @include('admin.settings.partials.sidebar')
    </div>

    <div class="col-9">
        <form action="{{ route('admin.settings.social-links.update-all') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Social Media Profiles</h3>
                </div>
                <div class="card-body">
                    <div class="social-links-list" id="socialLinksList">
                        @php
                            $socialPlatforms = [
                                'facebook' => ['icon' => 'fab fa-facebook-f', 'color' => '#1877f2', 'placeholder' => 'https://facebook.com/yourhotel'],
                                'instagram' => ['icon' => 'fab fa-instagram', 'color' => '#e4405f', 'placeholder' => 'https://instagram.com/yourhotel'],
                                'twitter' => ['icon' => 'fab fa-twitter', 'color' => '#1da1f2', 'placeholder' => 'https://twitter.com/yourhotel'],
                                'linkedin' => ['icon' => 'fab fa-linkedin-in', 'color' => '#0077b5', 'placeholder' => 'https://linkedin.com/company/yourhotel'],
                                'youtube' => ['icon' => 'fab fa-youtube', 'color' => '#ff0000', 'placeholder' => 'https://youtube.com/c/yourhotel'],
                                'tiktok' => ['icon' => 'fab fa-tiktok', 'color' => '#000000', 'placeholder' => 'https://tiktok.com/@yourhotel'],
                                'pinterest' => ['icon' => 'fab fa-pinterest-p', 'color' => '#e60023', 'placeholder' => 'https://pinterest.com/yourhotel'],
                                'tripadvisor' => ['icon' => 'fab fa-tripadvisor', 'color' => '#00af87', 'placeholder' => 'https://tripadvisor.com/Hotel_Review-yourhotel'],
                                'whatsapp' => ['icon' => 'fab fa-whatsapp', 'color' => '#25d366', 'placeholder' => 'https://wa.me/1234567890'],
                                'telegram' => ['icon' => 'fab fa-telegram-plane', 'color' => '#0088cc', 'placeholder' => 'https://t.me/yourhotel'],
                            ];
                        @endphp

                        @foreach($socialPlatforms as $platform => $config)
                        <div class="social-link-item">
                            <div class="social-icon" style="background: {{ $config['color'] }};">
                                <i class="{{ $config['icon'] }}"></i>
                            </div>
                            <div class="social-input">
                                <label class="form-label">{{ ucfirst($platform) }}</label>
                                <div class="d-flex gap-2">
                                    <input type="url" name="social[{{ $platform }}][url]" class="form-control"
                                           value="{{ old("social.$platform.url", $socialLinks[$platform]['url'] ?? '') }}"
                                           placeholder="{{ $config['placeholder'] }}">
                                    <label class="toggle-switch" title="Enable/Disable">
                                        <input type="checkbox" name="social[{{ $platform }}][active]" value="1"
                                               {{ old("social.$platform.active", $socialLinks[$platform]['active'] ?? false) ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Custom Links -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-between align-items-center">
                    <h3 class="card-title mb-0">Custom Social Links</h3>
                    <button type="button" class="btn btn-secondary btn-sm" id="addCustomLink">
                        <i class="fas fa-plus"></i> Add Link
                    </button>
                </div>
                <div class="card-body">
                    <div id="customLinksContainer">
                        @foreach($customLinks ?? [] as $index => $link)
                        <div class="custom-link-row" data-index="{{ $index }}">
                            <div class="row align-items-end">
                                <div class="col-3">
                                    <div class="form-group mb-0">
                                        <label class="form-label">Platform Name</label>
                                        <input type="text" name="custom[{{ $index }}][name]" class="form-control"
                                               value="{{ $link['name'] ?? '' }}" placeholder="Yelp">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group mb-0">
                                        <label class="form-label">Icon Class</label>
                                        <input type="text" name="custom[{{ $index }}][icon]" class="form-control"
                                               value="{{ $link['icon'] ?? '' }}" placeholder="fab fa-yelp">
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="form-group mb-0">
                                        <label class="form-label">URL</label>
                                        <input type="url" name="custom[{{ $index }}][url]" class="form-control"
                                               value="{{ $link['url'] ?? '' }}" placeholder="https://yelp.com/biz/yourhotel">
                                    </div>
                                </div>
                                <div class="col-1">
                                    <button type="button" class="btn btn-danger btn-sm remove-custom-link" style="margin-bottom: 0;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="icon-help mt-3">
                        <p class="text-muted mb-1" style="font-size: 0.75rem;">
                            <i class="fas fa-info-circle"></i>
                            Find icon classes at <a href="https://fontawesome.com/icons" target="_blank">Font Awesome Icons</a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Display Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Display Settings</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Display Location</label>
                                <div class="checkbox-group">
                                    <label class="form-check">
                                        <input type="checkbox" name="display_locations[]" value="header" class="form-check-input"
                                               {{ in_array('header', old('display_locations', $displayLocations ?? ['header', 'footer'])) ? 'checked' : '' }}>
                                        <span class="form-check-label">Header</span>
                                    </label>
                                    <label class="form-check">
                                        <input type="checkbox" name="display_locations[]" value="footer" class="form-check-input"
                                               {{ in_array('footer', old('display_locations', $displayLocations ?? ['header', 'footer'])) ? 'checked' : '' }}>
                                        <span class="form-check-label">Footer</span>
                                    </label>
                                    <label class="form-check">
                                        <input type="checkbox" name="display_locations[]" value="contact" class="form-check-input"
                                               {{ in_array('contact', old('display_locations', $displayLocations ?? [])) ? 'checked' : '' }}>
                                        <span class="form-check-label">Contact Page</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Icon Style</label>
                                <select name="icon_style" class="form-control">
                                    <option value="solid" {{ ($iconStyle ?? 'solid') == 'solid' ? 'selected' : '' }}>Solid Color</option>
                                    <option value="outline" {{ ($iconStyle ?? '') == 'outline' ? 'selected' : '' }}>Outline</option>
                                    <option value="circle" {{ ($iconStyle ?? '') == 'circle' ? 'selected' : '' }}>Circle Background</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-check">
                            <input type="checkbox" name="open_new_tab" value="1" class="form-check-input"
                                   {{ old('open_new_tab', $openNewTab ?? true) ? 'checked' : '' }}>
                            <span class="form-check-label">Open links in new tab</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Preview -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Preview</h3>
                </div>
                <div class="card-body">
                    <div class="social-preview" id="socialPreview">
                        @foreach($socialPlatforms as $platform => $config)
                            @if(!empty($socialLinks[$platform]['url']) && ($socialLinks[$platform]['active'] ?? false))
                            <a href="#" class="social-preview-icon" style="background: {{ $config['color'] }};" title="{{ ucfirst($platform) }}">
                                <i class="{{ $config['icon'] }}"></i>
                            </a>
                            @endif
                        @endforeach
                    </div>
                    <p class="text-muted mt-2 mb-0" style="font-size: 0.75rem;">
                        Active social links will appear like this on your website
                    </p>
                </div>
            </div>

            <div class="d-flex justify-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Social Links
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.social-link-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid var(--gray-100);
}

.social-link-item:last-child {
    border-bottom: none;
}

.social-icon {
    width: 40px;
    height: 40px;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.125rem;
    flex-shrink: 0;
    margin-top: 1.5rem;
}

.social-input {
    flex: 1;
}

.social-input .form-label {
    font-weight: 500;
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 38px;
    flex-shrink: 0;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 7px;
    left: 0;
    right: 0;
    bottom: 7px;
    background-color: var(--gray-300);
    transition: 0.2s;
    border-radius: 24px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.2s;
    border-radius: 50%;
}

.toggle-switch input:checked + .toggle-slider {
    background-color: var(--success);
}

.toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(20px);
}

.custom-link-row {
    padding: 1rem;
    background: var(--gray-50);
    border-radius: 0.5rem;
    margin-bottom: 1rem;
}

.custom-link-row:last-child {
    margin-bottom: 0;
}

.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.social-preview {
    display: flex;
    gap: 0.75rem;
    padding: 1rem;
    background: var(--gray-900);
    border-radius: 0.5rem;
}

.social-preview-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: transform 0.15s, opacity 0.15s;
}

.social-preview-icon:hover {
    transform: scale(1.1);
    opacity: 0.9;
}
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let customLinkIndex = {{ count($customLinks ?? []) }};

    // Add custom link
    document.getElementById('addCustomLink').addEventListener('click', function() {
        const container = document.getElementById('customLinksContainer');
        const row = document.createElement('div');
        row.className = 'custom-link-row';
        row.dataset.index = customLinkIndex;
        row.innerHTML = `
            <div class="row align-items-end">
                <div class="col-3">
                    <div class="form-group mb-0">
                        <label class="form-label">Platform Name</label>
                        <input type="text" name="custom[${customLinkIndex}][name]" class="form-control" placeholder="Yelp">
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group mb-0">
                        <label class="form-label">Icon Class</label>
                        <input type="text" name="custom[${customLinkIndex}][icon]" class="form-control" placeholder="fab fa-yelp">
                    </div>
                </div>
                <div class="col-5">
                    <div class="form-group mb-0">
                        <label class="form-label">URL</label>
                        <input type="url" name="custom[${customLinkIndex}][url]" class="form-control" placeholder="https://yelp.com/biz/yourhotel">
                    </div>
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-danger btn-sm remove-custom-link" style="margin-bottom: 0;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;

        container.appendChild(row);
        customLinkIndex++;
    });

    // Remove custom link
    document.getElementById('customLinksContainer').addEventListener('click', function(e) {
        if (e.target.closest('.remove-custom-link')) {
            e.target.closest('.custom-link-row').remove();
        }
    });

    // Update preview on toggle change
    document.querySelectorAll('.social-link-item .toggle-switch input').forEach(toggle => {
        toggle.addEventListener('change', updatePreview);
    });

    function updatePreview() {
        // In a real implementation, this would rebuild the preview based on active links
    }
});
</script>
@endpush
