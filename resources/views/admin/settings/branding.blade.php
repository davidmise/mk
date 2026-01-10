@extends('admin.layouts.app')

@section('title', 'Branding Settings')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <a href="{{ route('admin.settings.index') }}">Settings</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>Branding</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Branding Settings</h1>
        <p class="page-subtitle">Manage your hotel's logo, colors, and visual identity</p>
    </div>
</div>

<div class="row">
    <div class="col-3">
        @include('admin.settings.partials.sidebar')
    </div>

    <div class="col-9">
        <form action="{{ route('admin.settings.branding.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Logo Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Logo</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Primary Logo</label>
                                <div class="logo-upload">
                                    <div class="logo-preview" id="logoPreview">
                                        @if($settings['logo'] ?? null)
                                            <img src="{{ asset('storage/' . $settings['logo']) }}" alt="Logo">
                                        @else
                                            <div class="upload-placeholder">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                                <span>Upload Logo</span>
                                            </div>
                                        @endif
                                    </div>
                                    <input type="file" name="logo" id="logoInput" accept="image/*" style="display: none;">
                                </div>
                                <small class="text-muted">Recommended: PNG with transparent background, 200x60px</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Logo (Light Version)</label>
                                <div class="logo-upload dark-bg">
                                    <div class="logo-preview" id="logoLightPreview">
                                        @if($settings['logo_light'] ?? null)
                                            <img src="{{ asset('storage/' . $settings['logo_light']) }}" alt="Logo Light">
                                        @else
                                            <div class="upload-placeholder">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                                <span>Upload Logo</span>
                                            </div>
                                        @endif
                                    </div>
                                    <input type="file" name="logo_light" id="logoLightInput" accept="image/*" style="display: none;">
                                </div>
                                <small class="text-muted">For dark backgrounds</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Favicon</label>
                                <div class="favicon-upload">
                                    <div class="favicon-preview" id="faviconPreview">
                                        @if($settings['favicon'] ?? null)
                                            <img src="{{ asset('storage/' . $settings['favicon']) }}" alt="Favicon">
                                        @else
                                            <div class="upload-placeholder">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <input type="file" name="favicon" id="faviconInput" accept="image/x-icon,image/png" style="display: none;">
                                </div>
                                <small class="text-muted">ICO or PNG, 32x32px</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Color Scheme -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Color Scheme</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Primary Color</label>
                                <div class="color-picker-wrapper">
                                    <input type="color" name="primary_color" class="color-picker"
                                           value="{{ old('primary_color', $settings['primary_color'] ?? '#c8a97e') }}">
                                    <input type="text" class="form-control color-input"
                                           value="{{ old('primary_color', $settings['primary_color'] ?? '#c8a97e') }}"
                                           pattern="^#[0-9A-Fa-f]{6}$">
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Secondary Color</label>
                                <div class="color-picker-wrapper">
                                    <input type="color" name="secondary_color" class="color-picker"
                                           value="{{ old('secondary_color', $settings['secondary_color'] ?? '#1a1a2e') }}">
                                    <input type="text" class="form-control color-input"
                                           value="{{ old('secondary_color', $settings['secondary_color'] ?? '#1a1a2e') }}"
                                           pattern="^#[0-9A-Fa-f]{6}$">
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Accent Color</label>
                                <div class="color-picker-wrapper">
                                    <input type="color" name="accent_color" class="color-picker"
                                           value="{{ old('accent_color', $settings['accent_color'] ?? '#e8d4b8') }}">
                                    <input type="text" class="form-control color-input"
                                           value="{{ old('accent_color', $settings['accent_color'] ?? '#e8d4b8') }}"
                                           pattern="^#[0-9A-Fa-f]{6}$">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="color-preview-bar mt-3">
                        <div class="color-swatch" id="primarySwatch" style="background: {{ $settings['primary_color'] ?? '#c8a97e' }};">
                            Primary
                        </div>
                        <div class="color-swatch" id="secondarySwatch" style="background: {{ $settings['secondary_color'] ?? '#1a1a2e' }};">
                            Secondary
                        </div>
                        <div class="color-swatch" id="accentSwatch" style="background: {{ $settings['accent_color'] ?? '#e8d4b8' }};">
                            Accent
                        </div>
                    </div>
                </div>
            </div>

            <!-- Typography -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Typography</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Heading Font</label>
                                <select name="heading_font" class="form-control">
                                    @foreach(['Playfair Display', 'Cormorant Garamond', 'Cinzel', 'Libre Baskerville', 'Lora'] as $font)
                                        <option value="{{ $font }}" {{ ($settings['heading_font'] ?? 'Playfair Display') == $font ? 'selected' : '' }}>
                                            {{ $font }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Body Font</label>
                                <select name="body_font" class="form-control">
                                    @foreach(['Inter', 'Open Sans', 'Lato', 'Source Sans Pro', 'Roboto'] as $font)
                                        <option value="{{ $font }}" {{ ($settings['body_font'] ?? 'Inter') == $font ? 'selected' : '' }}>
                                            {{ $font }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="typography-preview mt-3">
                        <h2 class="preview-heading" style="font-family: '{{ $settings['heading_font'] ?? 'Playfair Display' }}', serif;">
                            The Quick Brown Fox Jumps Over
                        </h2>
                        <p class="preview-body" style="font-family: '{{ $settings['body_font'] ?? 'Inter' }}', sans-serif;">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Custom CSS -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-between align-items-center">
                    <h3 class="card-title mb-0">Custom CSS</h3>
                    <span class="badge badge-secondary">Advanced</span>
                </div>
                <div class="card-body">
                    <div class="form-group mb-0">
                        <textarea name="custom_css" class="form-control code-editor" rows="10"
                                  placeholder="/* Add your custom CSS here */">{{ old('custom_css', $settings['custom_css'] ?? '') }}</textarea>
                        <small class="text-muted">This CSS will be injected into the frontend pages</small>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-between">
                <button type="button" class="btn btn-secondary" id="resetBranding">
                    <i class="fas fa-undo"></i> Reset to Defaults
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Branding Settings
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.logo-upload {
    border: 2px dashed var(--gray-300);
    border-radius: 0.5rem;
    padding: 1rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.15s;
    background: #fff;
}

.logo-upload.dark-bg {
    background: var(--gray-800);
}

.logo-upload:hover {
    border-color: var(--primary);
}

.logo-preview {
    min-height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.logo-preview img {
    max-height: 80px;
    max-width: 100%;
}

.favicon-upload {
    width: 80px;
    height: 80px;
    border: 2px dashed var(--gray-300);
    border-radius: 0.5rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s;
}

.favicon-upload:hover {
    border-color: var(--primary);
}

.favicon-preview img {
    width: 32px;
    height: 32px;
}

.upload-placeholder {
    color: var(--gray-400);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.upload-placeholder i {
    font-size: 1.5rem;
}

.color-picker-wrapper {
    display: flex;
    gap: 0.5rem;
}

.color-picker {
    width: 50px;
    height: 38px;
    padding: 0;
    border: 1px solid var(--gray-200);
    border-radius: 0.375rem;
    cursor: pointer;
}

.color-input {
    flex: 1;
    font-family: monospace;
}

.color-preview-bar {
    display: flex;
    border-radius: 0.5rem;
    overflow: hidden;
}

.color-swatch {
    flex: 1;
    padding: 1rem;
    text-align: center;
    font-weight: 600;
    color: white;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.typography-preview {
    padding: 1.5rem;
    background: var(--gray-50);
    border-radius: 0.5rem;
}

.preview-heading {
    margin-bottom: 0.75rem;
    font-size: 1.5rem;
}

.preview-body {
    margin: 0;
    line-height: 1.6;
}

.code-editor {
    font-family: 'Monaco', 'Consolas', monospace;
    font-size: 0.875rem;
    line-height: 1.5;
    background: var(--gray-900);
    color: #a8ff60;
    border: none;
}
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Logo uploads
    setupImageUpload('logoPreview', 'logoInput');
    setupImageUpload('logoLightPreview', 'logoLightInput');
    setupImageUpload('faviconPreview', 'faviconInput');

    function setupImageUpload(previewId, inputId) {
        const preview = document.getElementById(previewId);
        const input = document.getElementById(inputId);

        preview.parentElement.addEventListener('click', () => input.click());

        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="">`;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Color picker sync
    document.querySelectorAll('.color-picker').forEach(picker => {
        const input = picker.nextElementSibling;
        const swatchId = picker.name.replace('_color', 'Swatch');
        const swatch = document.getElementById(swatchId);

        picker.addEventListener('input', function() {
            input.value = this.value;
            if (swatch) swatch.style.background = this.value;
        });

        input.addEventListener('input', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                picker.value = this.value;
                if (swatch) swatch.style.background = this.value;
            }
        });
    });

    // Font preview
    document.querySelector('select[name="heading_font"]').addEventListener('change', function() {
        document.querySelector('.preview-heading').style.fontFamily = `'${this.value}', serif`;
    });

    document.querySelector('select[name="body_font"]').addEventListener('change', function() {
        document.querySelector('.preview-body').style.fontFamily = `'${this.value}', sans-serif`;
    });

    // Reset to defaults
    document.getElementById('resetBranding').addEventListener('click', function() {
        if (confirm('Reset all branding settings to defaults? This cannot be undone.')) {
            document.querySelector('input[name="primary_color"]').value = '#c8a97e';
            document.querySelector('input[name="secondary_color"]').value = '#1a1a2e';
            document.querySelector('input[name="accent_color"]').value = '#e8d4b8';
            document.querySelector('select[name="heading_font"]').value = 'Playfair Display';
            document.querySelector('select[name="body_font"]').value = 'Inter';
            document.querySelector('textarea[name="custom_css"]').value = '';

            // Update color pickers and swatches
            document.querySelectorAll('.color-picker').forEach(picker => {
                picker.dispatchEvent(new Event('input'));
            });
        }
    });
});
</script>
@endpush
