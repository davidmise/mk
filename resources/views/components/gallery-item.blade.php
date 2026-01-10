@props([
    'image',
    'title' => '',
    'class' => ''
])

<a href="{{ asset($image) }}" data-fancybox="gallery" class="{{ $class }}">
    <img src="{{ asset($image) }}" alt="{{ $title }}" class="img-fluid">
</a>
