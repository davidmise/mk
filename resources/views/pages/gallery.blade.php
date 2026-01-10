<x-layout :title="$title" :metaDescription="$metaDescription" :metaKeywords="$metaKeywords" :currentPage="$currentPage" :includeFancybox="$includeFancybox">

    <main class="untree_co--site-main">

        <x-hero-banner
            :heading="$page->hero_heading ?? 'Gallery'"
            :image="$page->hero_image ?? 'images/mk_hotel/vip.jpg'"
            :centered="true"
        />

        <!-- Slider Gallery -->
        @if($sliderImages->count() > 0)
        <div class="untree_co--site-section">
            <div class="container-fluid px-0">
                <x-section-heading title="Slider Gallery" />

                <div class="row align-items-stretch">
                    <div class="col-9 relative" data-aos="fade-up">
                        <div class="owl-carousel owl-gallery-big">
                            @foreach($sliderImages as $image)
                                <div class="slide-thumb bg-image" style="background-image: url('{{ asset($image->image) }}')"></div>
                            @endforeach
                        </div>
                        <div class="slider-counter text-center"></div>
                    </div>
                    <div class="col-3 relative" data-aos="fade-up" data-aos-delay="100">
                        <div class="owl-carousel owl-gallery-small">
                            @foreach($sliderImages as $image)
                                <div class="slide-thumb bg-image" style="background-image: url('{{ asset($image->thumbnail ?? $image->image) }}')"><a href="#"></a></div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- More Galleries -->
        <div class="untree_co--site-section">
            <div class="container">
                <x-section-heading title="More Galleries" />

                <div class="row gutter-2">
                    @forelse($galleryImages as $index => $image)
                        @if($index % 3 == 0)
                            <div class="col-md-6" data-aos="fade-up">
                                <x-gallery-item :image="$image->image" :title="$image->title" />
                            </div>
                        @elseif($index % 3 == 1)
                            <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                                <x-gallery-item :image="$image->image" :title="$image->title" />
                            </div>
                        @else
                            <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ ($index % 3) * 100 }}">
                                <x-gallery-item :image="$image->image" :title="$image->title" />
                            </div>
                        @endif
                    @empty
                        <div class="col-12 text-center">
                            <p>Gallery images coming soon.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="untree_co--site-section py-5 bg-body-darker cta">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h3 class="m-0 p-0">If you have any special requests, please feel free to call us. <a href="tel://{{ preg_replace('/\s+/', '', \App\Models\SiteSetting::get('phone_primary', '+255747685401')) }}">{{ \App\Models\SiteSetting::get('phone_primary', '+255 747 685 401') }}</a></h3>
                    </div>
                </div>
            </div>
        </div>

    </main>

</x-layout>
