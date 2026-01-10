@extends('admin.layouts.app')

@section('title', 'SEO Settings')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <a href="{{ route('admin.settings.index') }}">Settings</a>
    <i class="fas fa-chevron-right" style="font-size: 0.625rem;"></i>
    <span>SEO</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">SEO Settings</h1>
        <p class="page-subtitle">Optimize your website for search engines</p>
    </div>
</div>

<div class="row">
    <div class="col-3">
        @include('admin.settings.partials.sidebar')
    </div>

    <div class="col-9">
        <form action="{{ route('admin.settings.seo.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Default Meta Tags -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Default Meta Tags</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Site Title</label>
                        <input type="text" name="site_title" class="form-control @error('site_title') is-invalid @enderror"
                               value="{{ old('site_title', $settings['site_title'] ?? '') }}"
                               placeholder="MK Hotel - Luxury Accommodation">
                        <small class="text-muted">This will appear in browser tabs and search results</small>
                        @error('site_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Title Separator</label>
                        <select name="title_separator" class="form-control" style="width: 200px;">
                            <option value=" | " {{ ($settings['title_separator'] ?? ' | ') == ' | ' ? 'selected' : '' }}>Pipe ( | )</option>
                            <option value=" - " {{ ($settings['title_separator'] ?? '') == ' - ' ? 'selected' : '' }}>Dash ( - )</option>
                            <option value=" » " {{ ($settings['title_separator'] ?? '') == ' » ' ? 'selected' : '' }}>Guillemet ( » )</option>
                            <option value=" • " {{ ($settings['title_separator'] ?? '') == ' • ' ? 'selected' : '' }}>Bullet ( • )</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Default Meta Description</label>
                        <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror" rows="3"
                                  placeholder="Discover luxury and comfort at MK Hotel. Experience world-class amenities, exceptional service, and unforgettable stays in the heart of the city.">{{ old('meta_description', $settings['meta_description'] ?? '') }}</textarea>
                        <div class="d-flex justify-between mt-1">
                            <small class="text-muted">Recommended: 150-160 characters</small>
                            <small class="char-count"><span id="descCharCount">0</span>/160</small>
                        </div>
                        @error('meta_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Default Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control @error('meta_keywords') is-invalid @enderror"
                               value="{{ old('meta_keywords', $settings['meta_keywords'] ?? '') }}"
                               placeholder="hotel, luxury accommodation, vacation, rooms, booking">
                        <small class="text-muted">Separate keywords with commas</small>
                        @error('meta_keywords')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Social Media / Open Graph -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Social Media Preview (Open Graph)</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">OG Image</label>
                        <div class="og-image-upload">
                            <div class="og-image-preview" id="ogImagePreview">
                                @if($settings['og_image'] ?? null)
                                    <img src="{{ asset('storage/' . $settings['og_image']) }}" alt="OG Image">
                                @else
                                    <div class="upload-placeholder">
                                        <i class="fas fa-image"></i>
                                        <span>Upload OG Image</span>
                                        <small>1200 x 630 px</small>
                                    </div>
                                @endif
                            </div>
                            <input type="file" name="og_image" id="ogImageInput" accept="image/*" style="display: none;">
                        </div>
                        <small class="text-muted">This image appears when your website is shared on social media</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">OG Title</label>
                        <input type="text" name="og_title" class="form-control @error('og_title') is-invalid @enderror"
                               value="{{ old('og_title', $settings['og_title'] ?? '') }}"
                               placeholder="Leave blank to use site title">
                        @error('og_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">OG Description</label>
                        <textarea name="og_description" class="form-control @error('og_description') is-invalid @enderror" rows="2"
                                  placeholder="Leave blank to use meta description">{{ old('og_description', $settings['og_description'] ?? '') }}</textarea>
                        @error('og_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Social Preview Card -->
                    <div class="social-preview mt-4">
                        <h4 class="mb-3" style="font-size: 0.875rem; color: var(--gray-600);">Preview on Facebook/LinkedIn:</h4>
                        <div class="social-preview-card">
                            <div class="social-preview-image">
                                @if($settings['og_image'] ?? null)
                                    <img src="{{ asset('storage/' . $settings['og_image']) }}" alt="">
                                @else
                                    <div class="placeholder-image">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="social-preview-content">
                                <span class="social-preview-domain">{{ request()->getHost() }}</span>
                                <h5 class="social-preview-title">{{ $settings['og_title'] ?? $settings['site_title'] ?? 'Your Site Title' }}</h5>
                                <p class="social-preview-desc">{{ Str::limit($settings['og_description'] ?? $settings['meta_description'] ?? 'Your site description will appear here...', 100) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schema / Structured Data -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Schema Markup (Structured Data)</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="schema_enabled" value="1" class="form-check-input"
                                   {{ old('schema_enabled', $settings['schema_enabled'] ?? true) ? 'checked' : '' }}>
                            <span class="form-check-label">Enable Schema.org markup for better SEO</span>
                        </label>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Business Type</label>
                                <select name="schema_type" class="form-control">
                                    <option value="Hotel" {{ ($settings['schema_type'] ?? 'Hotel') == 'Hotel' ? 'selected' : '' }}>Hotel</option>
                                    <option value="Resort" {{ ($settings['schema_type'] ?? '') == 'Resort' ? 'selected' : '' }}>Resort</option>
                                    <option value="Motel" {{ ($settings['schema_type'] ?? '') == 'Motel' ? 'selected' : '' }}>Motel</option>
                                    <option value="BedAndBreakfast" {{ ($settings['schema_type'] ?? '') == 'BedAndBreakfast' ? 'selected' : '' }}>Bed & Breakfast</option>
                                    <option value="LodgingBusiness" {{ ($settings['schema_type'] ?? '') == 'LodgingBusiness' ? 'selected' : '' }}>Lodging Business</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Price Range</label>
                                <select name="price_range" class="form-control">
                                    <option value="$" {{ ($settings['price_range'] ?? '') == '$' ? 'selected' : '' }}>$ - Budget</option>
                                    <option value="$$" {{ ($settings['price_range'] ?? '$$') == '$$' ? 'selected' : '' }}>$$ - Moderate</option>
                                    <option value="$$$" {{ ($settings['price_range'] ?? '') == '$$$' ? 'selected' : '' }}>$$$ - Upscale</option>
                                    <option value="$$$$" {{ ($settings['price_range'] ?? '') == '$$$$' ? 'selected' : '' }}>$$$$ - Luxury</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Robots & Indexing -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Robots & Indexing</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="robots_index" value="1" class="form-check-input"
                                   {{ old('robots_index', $settings['robots_index'] ?? true) ? 'checked' : '' }}>
                            <span class="form-check-label">Allow search engines to index this site</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="robots_follow" value="1" class="form-check-input"
                                   {{ old('robots_follow', $settings['robots_follow'] ?? true) ? 'checked' : '' }}>
                            <span class="form-check-label">Allow search engines to follow links</span>
                        </label>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Custom robots.txt content</label>
                        <textarea name="robots_txt" class="form-control code-editor" rows="6"
                                  placeholder="# Custom robots.txt rules">{{ old('robots_txt', $settings['robots_txt'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Analytics & Tracking -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Analytics & Tracking</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Google Analytics ID</label>
                        <input type="text" name="google_analytics_id" class="form-control @error('google_analytics_id') is-invalid @enderror"
                               value="{{ old('google_analytics_id', $settings['google_analytics_id'] ?? '') }}"
                               placeholder="G-XXXXXXXXXX or UA-XXXXXX-X">
                        @error('google_analytics_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Google Tag Manager ID</label>
                        <input type="text" name="gtm_id" class="form-control @error('gtm_id') is-invalid @enderror"
                               value="{{ old('gtm_id', $settings['gtm_id'] ?? '') }}"
                               placeholder="GTM-XXXXXXX">
                        @error('gtm_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Facebook Pixel ID</label>
                        <input type="text" name="facebook_pixel_id" class="form-control @error('facebook_pixel_id') is-invalid @enderror"
                               value="{{ old('facebook_pixel_id', $settings['facebook_pixel_id'] ?? '') }}"
                               placeholder="XXXXXXXXXXXXXXXX">
                        @error('facebook_pixel_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save SEO Settings
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.og-image-upload {
    border: 2px dashed var(--gray-300);
    border-radius: 0.5rem;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.15s;
}

.og-image-upload:hover {
    border-color: var(--primary);
}

.og-image-preview {
    aspect-ratio: 1200/630;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gray-50);
}

.og-image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.upload-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: var(--gray-400);
    gap: 0.5rem;
}

.upload-placeholder i {
    font-size: 2rem;
}

.char-count {
    color: var(--gray-500);
    font-size: 0.75rem;
}

.social-preview-card {
    border: 1px solid var(--gray-200);
    border-radius: 0.5rem;
    overflow: hidden;
    max-width: 500px;
}

.social-preview-image {
    aspect-ratio: 1200/630;
    background: var(--gray-100);
}

.social-preview-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.social-preview-image .placeholder-image {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-400);
    font-size: 2rem;
}

.social-preview-content {
    padding: 0.75rem 1rem;
    background: white;
}

.social-preview-domain {
    font-size: 0.75rem;
    color: var(--gray-500);
    text-transform: uppercase;
}

.social-preview-title {
    margin: 0.25rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-900);
}

.social-preview-desc {
    margin: 0;
    font-size: 0.875rem;
    color: var(--gray-600);
}

.code-editor {
    font-family: 'Monaco', 'Consolas', monospace;
    font-size: 0.875rem;
}
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character count for meta description
    const descInput = document.querySelector('textarea[name="meta_description"]');
    const descCounter = document.getElementById('descCharCount');

    function updateCharCount() {
        descCounter.textContent = descInput.value.length;
        if (descInput.value.length > 160) {
            descCounter.parentElement.style.color = 'var(--danger)';
        } else if (descInput.value.length > 140) {
            descCounter.parentElement.style.color = 'var(--warning)';
        } else {
            descCounter.parentElement.style.color = 'var(--gray-500)';
        }
    }

    descInput.addEventListener('input', updateCharCount);
    updateCharCount();

    // OG Image upload
    const ogPreview = document.getElementById('ogImagePreview');
    const ogInput = document.getElementById('ogImageInput');

    ogPreview.parentElement.addEventListener('click', () => ogInput.click());

    ogInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                ogPreview.innerHTML = `<img src="${e.target.result}" alt="">`;
                // Update social preview
                document.querySelector('.social-preview-image').innerHTML = `<img src="${e.target.result}" alt="">`;
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
});
</script>
@endpush
