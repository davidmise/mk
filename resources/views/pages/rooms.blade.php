<x-layout :title="$title" :metaDescription="$metaDescription" :metaKeywords="$metaKeywords" :currentPage="$currentPage">

    <main class="untree_co--site-main">

        <x-hero-banner
            :heading="$page->hero_heading ?? 'Our Rooms'"
            :image="$page->hero_image ?? 'images/mk_hotel/vip.jpg'"
            :centered="true"
        />

        <div class="untree_co--site-section pb-0">
            <div class="container-fluid px-md-0">
                <!-- Intro Section -->
                <div class="row justify-content-center text-center site-section pt-0">
                    <div class="col-md-6">
                        <h2 class="display-4" data-aos="fade-up">{{ $page->activeSection('intro')?->title ?? 'Enjoy Your Stay' }}</h2>
                        <p data-aos="fade-up" data-aos-delay="100">
                            {{ $page->activeSection('intro')?->content ?? 'At MK Hotel, we believe every guest deserves more than just a place to sleep. Whether you\'re visiting for business or leisure, our thoughtful service, elegant spaces, and relaxing atmosphere are designed to make every moment of your stay truly memorable.' }}
                        </p>
                    </div>
                </div>

                <!-- Room Listings -->
                @foreach($rooms as $index => $room)
                    <x-room-card :room="$room" :reverse="$index % 2 !== 0" :showDetails="$index !== count($rooms) - 1" />
                @endforeach
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

        </div>

    </main>

</x-layout>
