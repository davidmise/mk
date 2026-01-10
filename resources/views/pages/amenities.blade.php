<x-layout :title="$title" :metaDescription="$metaDescription" :metaKeywords="$metaKeywords" :currentPage="$currentPage">

    <main class="site-untree_co--main">

        <x-hero-banner
            :heading="$page->hero_heading ?? 'Amenities'"
            :image="$page->hero_image ?? 'images/mk_hotel/amenities.jpg'"
            :centered="true"
        />

        <div class="untree_co--site-section">
            <div class="container">
                <x-section-heading title="Hotel Amenities" />

                <div class="row custom-row-02192 align-items-stretch">
                    @forelse($amenities as $amenity)
                        <x-amenity-card :amenity="$amenity" />
                    @empty
                        <div class="col-12 text-center">
                            <p>Amenities information coming soon.</p>
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
