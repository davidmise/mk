<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Amenity;

class AmenitiesSeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            [
                'name' => 'Swimming Pool',
                'slug' => 'swimming-pool',
                'description' => 'Take a refreshing dip in our crystal-clear swimming pool — the perfect spot to unwind, soak up the sun, or enjoy a serene evening swim. Whether you\'re here for leisure or business, our pool offers a tranquil escape from the day.',
                'icon' => 'images/svg/swimming.png',
                'order' => 1,
            ],
            [
                'name' => 'Free Parking',
                'slug' => 'free-parking',
                'description' => 'Enjoy the convenience of secure, complimentary parking throughout your stay. Whether you\'re arriving by car or rental, rest easy knowing your vehicle is safely accommodated just steps away from the hotel entrance.',
                'icon' => 'images/svg/parking.svg',
                'order' => 2,
            ],
            [
                'name' => 'Free WiFi',
                'slug' => 'free-wifi',
                'description' => 'Stay connected with high-speed, complimentary WiFi available in all rooms and public areas. Whether you\'re catching up on work or streaming your favorite shows, we\'ve got you covered with seamless internet access.',
                'icon' => 'images/svg/wifi.svg',
                'order' => 3,
            ],
            [
                'name' => 'Elevators',
                'slug' => 'elevators',
                'description' => 'Access every floor with ease using our modern, spacious elevators. Designed for comfort and accessibility, they ensure a smooth and convenient experience for all guests, including those with mobility needs.',
                'icon' => 'images/svg/elevator.svg',
                'order' => 4,
            ],
            [
                'name' => 'Restaurant',
                'slug' => 'restaurant',
                'description' => 'Savor delicious local and international cuisine at our on-site restaurant. From hearty breakfasts to gourmet dinners, our chefs prepare every meal with fresh ingredients and a passion for flavor.',
                'icon' => 'images/svg/restaurant.svg',
                'order' => 5,
            ],
            [
                'name' => '24/7 Room Service',
                'slug' => 'room-service',
                'description' => 'Enjoy the convenience of around-the-clock room service. Whether you\'re craving a midnight snack or an early breakfast, our dedicated staff is ready to serve you anytime, day or night.',
                'icon' => 'images/svg/service.svg',
                'order' => 6,
            ],
            [
                'name' => 'Bar & Lounge',
                'slug' => 'bar-lounge',
                'description' => 'Unwind at our stylish bar and lounge, offering a selection of premium drinks, cocktails, and light bites. It\'s the perfect setting to relax after a long day or socialize with fellow guests.',
                'icon' => 'images/svg/bar.svg',
                'order' => 7,
            ],
            [
                'name' => 'Conference Facilities',
                'slug' => 'conference-facilities',
                'description' => 'Host successful meetings and events in our fully equipped conference rooms. With modern AV technology, flexible seating arrangements, and professional support, we make business gatherings seamless.',
                'icon' => 'images/svg/conference.svg',
                'order' => 8,
            ],
        ];

        foreach ($amenities as $amenity) {
            Amenity::create($amenity);
        }
    }
}
