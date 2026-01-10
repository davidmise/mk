@props([
    'title' => '',
    'class' => ''
])

<div class="row justify-content-center text-center pt-0 pb-5 {{ $class }}">
    <div class="col-lg-6 section-heading" data-aos="fade-up">
        <h3 class="text-center">{{ $title }}</h3>
    </div>
</div>
