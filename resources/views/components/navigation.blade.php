@props(['currentPage' => ''])

@php
    $navItems = \App\Models\NavigationItem::with('children')
        ->active()
        ->rootItems()
        ->ordered()
        ->get();

    $socialLinks = \App\Models\SocialLink::active()->ordered()->get();
    $logoDesktop = \App\Models\SiteSetting::get('logo_desktop', 'images/mk_hotel/logo/mk_text.png');
    $logoMobileText = \App\Models\SiteSetting::get('logo_mobile_text', 'MK Hotel');
@endphp

<nav class="untree_co--site-nav js-sticky-nav">
    <div class="container d-flex align-items-center justify-content-between">

        <!-- Logo -->
        <div class="logo-wrap">
            <a href="{{ route('home') }}" class="untree_co--site-logo d-flex align-items-center">
                <!-- Image logo for desktop only -->
                <img src="{{ asset($logoDesktop) }}" alt="MK Logo" class="d-none d-md-block" height="40">
                <!-- Text logo for mobile only -->
                <span class="d-block d-md-none h5 mb-0 ml-2 text-dark">{{ $logoMobileText }}</span>
            </a>
        </div>

        <!-- Book Now button - center on mobile only -->
        <div class="d-flex d-lg-none justify-content-center flex-grow-1">
            <button id="openModalBtn" class="btn btn-dark btn-sm mx-auto">Book Now</button>
        </div>

        <!-- Hamburger Icon - right on mobile -->
        <div class="icons-wrap d-lg-none">
            <a href="#" class="burger js-menu-toggle" data-toggle="collapse" data-target="#main-navbar">
                <span></span>
            </a>
        </div>

        <!-- Full menu for desktop -->
        <div class="site-nav-ul-wrap text-center d-none d-lg-block ms-auto">
            <ul class="site-nav-ul js-clone-nav">
                @foreach($navItems as $item)
                    @if($item->hasChildren())
                        <li class="has-children {{ $currentPage === $item->url ? 'active' : '' }}">
                            <a href="{{ $item->url }}">{{ $item->label }}</a>
                            <ul class="dropdown">
                                @foreach($item->children as $child)
                                    <li><a href="{{ $child->url }}" target="{{ $child->target }}">{{ $child->label }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        <li class="{{ $currentPage === $item->url ? 'active' : '' }}">
                            <a href="{{ $item->url }}" target="{{ $item->target }}">{{ $item->label }}</a>
                        </li>
                    @endif
                @endforeach
                <!-- Only visible on desktop (nav expanded) -->
                <li class="d-none d-lg-inline-block">
                    <button id="openModalBtn2" class="btn btn-dark rounded">Book Now</button>
                </li>
            </ul>
        </div>

        <div class="icons-wrap text-md-right">
            <ul class="icons-top d-none d-lg-block">
                @foreach($socialLinks as $social)
                    <li>
                        <a href="{{ $social->url }}" target="_blank"><span class="{{ $social->icon }}"></span></a>
                    </li>
                @endforeach
            </ul>

            <!-- Mobile Toggle -->
            <a href="#" class="d-block d-lg-none burger js-menu-toggle" data-toggle="collapse" data-target="#main-navbar">
                <span></span>
            </a>
        </div>
    </div>
</nav>
