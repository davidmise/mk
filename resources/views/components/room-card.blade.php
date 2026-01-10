@props([
    'room',
    'reverse' => false,
    'showDetails' => true
])

<div class="container-fluid px-md-0">
    <div class="row no-gutters align-items-stretch room-animate site-section {{ !$showDetails ? 'pb-0' : '' }}">
        <div class="col-md-7 {{ $reverse ? 'order-md-2' : '' }} img-wrap" data-jarallax-element="-100">
            <div class="bg-image h-100" style="background-color: #efefef; background-image: url('{{ asset($room->image) }}');"></div>
        </div>
        <div class="col-md-5">
            <div class="row justify-content-center">
                <div class="col-md-8 py-5">
                    <h3 id="{{ $room->slug }}" class="display-4 heading">{{ $room->name }}</h3>
                    <div class="room-exerpt">
                        <div class="room-price mb-4">{{ $room->formatted_price }}</div>
                        <p>{{ $room->description }}</p>
                        @if($room->secondary_description)
                            <p>{{ $room->secondary_description }}</p>
                        @endif

                        @if($room->amenities && count($room->amenities) > 0)
                        <div class="row mt-5">
                            <div class="col-12">
                                <h3 class="mb-4">Amenities</h3>
                                <ul class="list-unstyled ul-check">
                                    @foreach($room->amenities as $amenity)
                                        <li>{{ $amenity }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
