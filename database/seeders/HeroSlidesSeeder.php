<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HeroSlide;

class HeroSlidesSeeder extends Seeder
{
    public function run(): void
    {
        $slides = [
            [
                'heading' => 'Welcome to MK Hotel',
                'subtext' => 'Experience comfort, elegance, and exceptional hospitality at MK Hotel — your perfect destination for business, leisure, or a relaxing escape. Let us make your stay unforgettable.',
                'image' => 'images/mk_hotel/up.jpg',
                'order' => 1,
            ],
            [
                'heading' => 'Enjoy Your Stay',
                'subtext' => 'From executive suites to serene spaces, MK Hotel offers a wide range of amenities tailored to your comfort and relaxation.',
                'image' => 'images/mk_hotel/vip.jpg',
                'order' => 2,
            ],
            [
                'heading' => 'Away from the Hustle and Bustle of City Life',
                'subtext' => 'Let our dedicated staff provide you with the warm hospitality and exceptional care that makes every guest feel at home.',
                'image' => 'images/mk_hotel/recep.jpg',
                'order' => 3,
            ],
        ];

        foreach ($slides as $slide) {
            HeroSlide::create($slide);
        }
    }
}
