<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="{{ $settings['site_author'] ?? 'MK Hotel, Musoma' }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <meta name="description" content="{{ $metaDescription ?? $settings['meta_description'] ?? 'MK Hotel is a 4-star hotel in Musoma located in Mwisenge near Lake Victoria.' }}" />
    <meta name="keywords" content="{{ $metaKeywords ?? $settings['meta_keywords'] ?? 'MK Hotel, Musoma hotel, hotel near Lake Victoria' }}" />

    @if(isset($ogTitle))
    <meta property="og:title" content="{{ $ogTitle }}" />
    <meta property="og:description" content="{{ $ogDescription ?? '' }}" />
    <meta property="og:image" content="{{ $ogImage ?? asset('images/mk_hotel/up.jpg') }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    @endif

    <link href="https://fonts.googleapis.com/css?family=Cormorant+Garamond:400,500i,700|Roboto:300,400,500,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/vendor/icomoon/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vendor/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vendor/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vendor/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vendor/bootstrap.css') }}">
    @if(isset($includeFancybox) && $includeFancybox)
    <link rel="stylesheet" href="{{ asset('css/vendor/jquery.fancybox.min.css') }}">
    @endif

    <!-- Theme Style -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/php_styles.css') }}">

    <title>{{ $title ?? $settings['site_name'] ?? 'MK Hotel Musoma' }}</title>

    @stack('styles')
</head>

<body>

    <div id="untree_co--overlayer"></div>
    <div class="loader">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

    <x-mobile-menu />

    <div class="untree_co--site-wrap">

        <x-navigation :currentPage="$currentPage ?? ''" />

        {{ $slot }}

        <x-footer />

    </div>

    <div id="toast" class="toast hidden">{{ session('toast_message', 'Success!') }}</div>

    <!-- Scripts -->
    <script src="{{ asset('js/vendor/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('js/vendor/popper.min.js') }}"></script>
    <script src="{{ asset('js/vendor/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/vendor/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/vendor/jarallax.min.js') }}"></script>
    <script src="{{ asset('js/vendor/jarallax-element.min.js') }}"></script>
    <script src="{{ asset('js/vendor/ofi.min.js') }}"></script>
    <script src="{{ asset('js/vendor/aos.js') }}"></script>
    <script src="{{ asset('js/vendor/jquery.lettering.js') }}"></script>
    <script src="{{ asset('js/vendor/jquery.sticky.js') }}"></script>
    <script src="{{ asset('js/vendor/TweenMax.min.js') }}"></script>
    <script src="{{ asset('js/vendor/ScrollMagic.min.js') }}"></script>
    <script src="{{ asset('js/vendor/scrollmagic.animation.gsap.min.js') }}"></script>
    <script src="{{ asset('js/vendor/debug.addIndicators.min.js') }}"></script>
    @if(isset($includeFancybox) && $includeFancybox)
    <script src="{{ asset('js/vendor/jquery.fancybox.min.js') }}"></script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/main.js') }}"></script>

    @stack('scripts')

</body>

</html>
