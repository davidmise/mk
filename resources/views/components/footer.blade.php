@php
    $socialLinks = \App\Models\SocialLink::active()->ordered()->get();
    $navItems = \App\Models\NavigationItem::active()->rootItems()->ordered()->get();

    $footerAbout = \App\Models\SiteSetting::get('footer_about', 'This establishment provides paid lodging on a short-term basis. Facilities provided may range from a modest-quality.');
    $footerLogo = \App\Models\SiteSetting::get('footer_logo', 'images/mk_hotel/logo/mk_text.png');
    $address = \App\Models\SiteSetting::get('address', '1007 Mwisenge, Musoma Urban, Mara Tanzania');
    $phone1 = \App\Models\SiteSetting::get('phone_primary', '+255 776 310 757');
    $phone2 = \App\Models\SiteSetting::get('phone_secondary', '+255 747 685 401');
    $copyrightText = \App\Models\SiteSetting::get('copyright_text', 'MK Hotel');
    $designCredit = \App\Models\SiteSetting::get('design_credit', 'Pamoja INC');
@endphp

<footer class="untree_co--site-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 pr-md-5">
                <h3>About Us</h3>
                <p>{{ $footerAbout }}</p>
                <p>
                    <a href="{{ route('home') }}" class="readmore">
                        <img src="{{ asset($footerLogo) }}" class="img-fluid" height="500" alt="MK Logo">
                    </a>
                </p>
            </div>
            <div class="col-md-8 ml-auto">
                <div class="row">
                    <div class="col-md-3">
                        <h3>Navigation</h3>
                        <ul class="list-unstyled">
                            @foreach($navItems as $item)
                                <li><a href="{{ $item->url }}">{{ $item->label }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-9 ml-auto">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h3>Address</h3>
                                <address>{!! nl2br(e($address)) !!}</address>
                            </div>
                            <div class="col-md-6">
                                <h3>Telephone</h3>
                                <p>
                                    <a href="tel:{{ preg_replace('/\s+/', '', $phone1) }}">{{ $phone1 }}</a> <br>
                                    <a href="tel:{{ preg_replace('/\s+/', '', $phone2) }}">{{ $phone2 }}</a>
                                </p>
                            </div>
                        </div>

                        <h3 class="mb-0">Join our newsletter</h3>
                        <p>Be the first to know our latest updates and news!</p>
                        <form action="{{ route('newsletter.subscribe') }}" method="POST" class="form-subscribe">
                            @csrf
                            <div class="form-group d-flex">
                                <input type="email" name="email" class="form-control mr-2" placeholder="Enter your email" required>
                                <input type="submit" value="Subscribe" class="btn btn-black px-4 text-white">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-5 pt-5 align-items-center">
            <div class="col-md-6 text-md-left">
                <p>
                    Copyright &copy; {{ date('Y') }} <a href="{{ route('home') }}">{{ $copyrightText }}</a>. All Rights Reserved. Design by <a href="#" target="_blank" class="text-primary">{{ $designCredit }}</a>
                </p>
            </div>
            <div class="col-md-6 text-md-right">
                <ul class="icons-top icons-dark">
                    @foreach($socialLinks as $social)
                        <li>
                            <a href="{{ $social->url }}" target="_blank"><span class="{{ $social->icon }}"></span></a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</footer>
