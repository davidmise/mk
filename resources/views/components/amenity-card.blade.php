@props([
    'amenity'
])

<div class="col-md-6 col-lg-4 mb-5" data-aos="fade-up" data-aos-delay="100">
    <div class="media-29191 text-center h-100">
        <div class="media-29191-icon">
            <img src="{{ asset($amenity->icon) }}" alt="{{ $amenity->name }}" class="img-fluid">
        </div>
        <h3>{{ $amenity->name }}</h3>
        <p>{{ $amenity->description }}</p>
    </div>
</div>
