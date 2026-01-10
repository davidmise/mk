@props([
    'heading' => '',
    'subtext' => '',
    'image' => '',
    'class' => 'inner-page',
    'centered' => false
])

<div class="untree_co--site-hero {{ $class }}" style="background-image: url('{{ asset($image) }}')">
    <div class="container">
        <div class="row align-items-center {{ $centered ? 'justify-content-center' : '' }}">
            <div class="col-md-{{ $centered ? '7 text-center' : '9' }}">
                <div class="site-hero-contents" data-aos="fade-up">
                    <h1 class="hero-heading text-white">{{ $heading }}</h1>
                    @if($subtext)
                    <div class="sub-text">
                        <p class="text-white">{{ $subtext }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
