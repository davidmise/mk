<x-layout :title="$title" :metaDescription="$metaDescription" :metaKeywords="$metaKeywords" :currentPage="$currentPage">

    <x-booking-modal :roomTypes="$roomTypes" />

    <div class="untree_co--site-main">

        <!-- Hero Slider -->
        <div class="owl-carousel owl-hero">
            @forelse($heroSlides as $slide)
                <div class="untree_co--site-hero overlay" style="background-image: url('{{ asset($slide->image) }}')">
                    <div class="container">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-md-8">
                                <div class="site-hero-contents text-center" data-aos="fade-up">
                                    <h1 class="hero-heading">{{ $slide->heading }}</h1>
                                    @if($slide->subtext)
                                    <div class="sub-text">
                                        <p>{{ $slide->subtext }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="untree_co--site-hero overlay" style="background-image: url('{{ asset('images/mk_hotel/up.jpg') }}')">
                    <div class="container">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-md-8">
                                <div class="site-hero-contents text-center" data-aos="fade-up">
                                    <h1 class="hero-heading">Welcome to MK Hotel</h1>
                                    <div class="sub-text">
                                        <p>Experience comfort, elegance, and exceptional hospitality at MK Hotel — your perfect destination for business, leisure, or a relaxing escape.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Featured Rooms Section -->
        <div class="untree_co--site-section float-left pb-0 featured-rooms">
            <div class="container pt-0 pb-5">
                <x-section-heading title="Featured Rooms" />
            </div>

            <div class="container-fluid pt-5">
                @foreach($featuredRooms as $index => $room)
                    @if($index == 0)
                        <div class="suite-wrap overlap-image-1">
                            <div class="suite">
                                <div class="image-stack">
                                    <div class="image-stack-item image-stack-item-top" data-jarallax-element="-50">
                                        <div class="overlay"></div>
                                        <img src="{{ asset($room->image) }}" alt="{{ $room->image_alt ?? $room->name }}" class="img-fluid pic1">
                                    </div>
                                    <div class="image-stack-item image-stack-item-bottom">
                                        <div class="overlay"></div>
                                        <img src="{{ asset($room->extra_data['secondary_image'] ?? $room->image) }}" alt="{{ $room->name }}" class="img-fluid pic2">
                                    </div>
                                </div>
                            </div>
                            <div class="suite-contents" data-jarallax-element="50">
                                <h2 class="suite-title">{{ $room->name }}</h2>
                                <div class="suite-excerpt">
                                    <p>{{ $room->description }}</p>
                                    <p><a href="{{ route('rooms') }}#{{ $room->slug }}" class="readmore">Room Details</a></p>
                                </div>
                            </div>
                        </div>
                    @elseif($index == 1)
                        <div class="suite-wrap overlap-image-2">
                            <div class="suite">
                                <div class="image-stack">
                                    <div class="image-stack-item image-stack-item-top">
                                        <div class="overlay"></div>
                                        <img src="{{ asset($room->image) }}" alt="{{ $room->name }}" class="img-fluid pic1">
                                    </div>
                                    <div class="image-stack-item image-stack-item-bottom" data-jarallax-element="-50">
                                        <div class="overlay"></div>
                                        <img src="{{ asset($room->extra_data['secondary_image'] ?? $room->image) }}" alt="{{ $room->name }}" class="img-fluid pic2">
                                    </div>
                                </div>
                            </div>
                            <div class="suite-contents" data-jarallax-element="50">
                                <h2 class="suite-title">{{ $room->name }}</h2>
                                <div class="suite-excerpt">
                                    <p>{{ $room->description }}</p>
                                    <p><a href="{{ route('rooms') }}#{{ $room->slug }}" class="readmore">Room Details</a></p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Amenities Section -->
        <div class="untree_co--site-section">
            <div class="container">
                <x-section-heading title="Hotel Amenities" />

                <div class="row custom-row-02192 align-items-stretch">
                    @foreach($amenities as $amenity)
                        <x-amenity-card :amenity="$amenity" />
                    @endforeach
                </div>

                <div class="row justify-content-center mt-4">
                    <div class="col-md-6 text-center">
                        <p><a href="{{ route('amenities') }}" class="btn btn-primary">View All Amenities</a></p>
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

    </div>

</x-layout>
