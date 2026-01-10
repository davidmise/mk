<x-layout :title="$title" :metaDescription="$metaDescription" :metaKeywords="$metaKeywords" :currentPage="$currentPage">

    <main class="untree_co--site-main">

        <x-hero-banner
            :heading="$page->hero_heading ?? 'About MK Hotel'"
            :subtext="$page->hero_subtext ?? 'Facilities provided may range from a modest-quality mattress in a small room to large suites with bigger.'"
            :image="$page->hero_image ?? 'images/mk_hotel/outside.jpg'"
        />

        <!-- Management Section -->
        <div class="untree_co--site-section">
            <div class="container-fluid px-md-0">
                <x-section-heading title="The Management" />

                <div class="row no-gutters">
                    <div class="col-md-4" data-aos="fade-up">
                        <img src="{{ asset($page->activeSection('management_image')?->image ?? 'images/mk_hotel/DELUX/delux.JPG') }}" alt="Image" class="img-fluid" data-jarallax-element="-140">
                    </div>
                    <div class="col-md-8">
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <h3 class="mb-4" data-aos="fade-up">{{ $page->activeSection('philosophy')?->title ?? 'Philosophy' }}</h3>
                                <div class="row">
                                    <div class="col-md-6" data-aos="fade-up">
                                        {!! $page->activeSection('philosophy')?->content ?? '<p>At MK Hotel, our philosophy is simple: to deliver an exceptional guest experience through genuine hospitality, attention to detail, and a passion for excellence. We believe that comfort, service, and care should be at the heart of every stay.</p><p>Our team is dedicated to creating a warm and welcoming environment where guests feel valued and cared for — whether they\'re here for business, leisure, or a special occasion.</p>' !!}
                                    </div>
                                    <div class="col-md-6" data-aos="fade-up">
                                        {!! $page->activeSection('philosophy_secondary')?->content ?? '<p>We strive to blend modern luxury with local charm, offering well-appointed rooms, thoughtful amenities, and personalized services. From our management to our staff, every member of the MK Hotel family shares a commitment to quality, comfort, and your total satisfaction.</p>' !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commitment Section -->
        <div class="untree_co--site-section pt-0">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-9">
                        <div class="row mb-5 align-items-center">
                            <div class="col-md-5 mr-auto">
                                <h2 class="display-4">{{ $page->activeSection('commitment')?->title ?? 'Our Commitment to Comfort' }}</h2>
                            </div>
                            <div class="col-md-7 ml-auto">
                                {!! $page->activeSection('commitment')?->content ?? '<p>We are committed to ensuring every guest enjoys a restful, comfortable, and memorable stay. From our plush bedding and modern amenities to our attentive service, every detail is thoughtfully designed with your well-being in mind.</p>' !!}
                            </div>
                        </div>
                    </div>
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
