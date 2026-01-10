<x-layout :title="$title" :metaDescription="$metaDescription" :metaKeywords="$metaKeywords" :currentPage="$currentPage" :includeFancybox="$includeFancybox">

    <main class="untree_co--site-main">

        <x-hero-banner
            :heading="$page->hero_heading ?? 'Contact Us'"
            :subtext="$page->hero_subtext ?? 'Have a question, request, or feedback? We\'re here to help. Reach out to our team anytime — we\'d love to hear from you.'"
            :image="$page->hero_image ?? 'images/mk_hotel/outside.jpg'"
            class="inner-page bg-light"
        />

        <div class="untree_co--site-section">
            <div class="container">
                <div class="row">
                    <div class="col-12" data-aos="fade-up">
                        <h2 class="display-4 mb-5">Fill the form</h2>
                    </div>
                    <div class="col-md-6 mb-5 mb-md-0" data-aos="fade-up" data-aos-delay="100">

                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('contact.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">Your Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="email2">Your Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email2" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}">
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="message">Message *</label>
                                <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" cols="30" rows="10" required>{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <input type="submit" value="Send" class="btn btn-black px-5 text-white">
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4 ml-auto" data-aos="fade-up" data-aos-delay="200">
                        <div class="media-29190">
                            <span class="label">Email</span>
                            <p><a href="mailto:{{ \App\Models\SiteSetting::get('email', 'mkhotelltd@gmail.com') }}">{{ \App\Models\SiteSetting::get('email', 'mkhotelltd@gmail.com') }}</a></p>
                        </div>
                        <div class="media-29190">
                            <span class="label">Phone</span>
                            <p><a href="tel:{{ preg_replace('/\s+/', '', \App\Models\SiteSetting::get('phone_primary', '+255747685401')) }}">{{ \App\Models\SiteSetting::get('phone_primary', '+255 747 685 401') }}</a></p>
                        </div>
                        <div class="media-29190">
                            <span class="label">Address</span>
                            <address>{!! nl2br(e(\App\Models\SiteSetting::get('address', "1007 Mwisenge, Musoma Urban,\nMara Tanzania"))) !!}</address>
                        </div>
                        <div class="media-29190">
                            <span class="label">Follow Us</span>
                            <ul class="icons-top icons-dark mt-3">
                                @foreach(\App\Models\SocialLink::active()->ordered()->get() as $social)
                                    <li>
                                        <a href="{{ $social->url }}" target="_blank"><span class="{{ $social->icon }}"></span></a>
                                    </li>
                                @endforeach
                            </ul>
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
