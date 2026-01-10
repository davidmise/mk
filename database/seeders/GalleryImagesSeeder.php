<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GalleryImage;

class GalleryImagesSeeder extends Seeder
{
    public function run(): void
    {
        // Slider Images
        $sliderImages = [
            [
                'title' => 'VIP Room',
                'image' => 'images/mk_hotel/VIP/vip.JPG',
                'category' => 'rooms',
                'is_slider' => true,
                'order' => 1,
            ],
            [
                'title' => 'VIP Room View',
                'image' => 'images/mk_hotel/VIP/vip2.jpg',
                'category' => 'rooms',
                'is_slider' => true,
                'order' => 2,
            ],
            [
                'title' => 'VIP Suite',
                'image' => 'images/mk_hotel/VIP/vip3.jpg',
                'category' => 'rooms',
                'is_slider' => true,
                'order' => 3,
            ],
            [
                'title' => 'Restaurant',
                'image' => 'images/mk_hotel/RESTAURANT/retaurant.JPG',
                'category' => 'amenities',
                'is_slider' => true,
                'order' => 4,
            ],
        ];

        // Gallery Images
        $galleryImages = [
            [
                'title' => 'Hotel Exterior',
                'image' => 'images/mk_hotel/up.jpg',
                'category' => 'exterior',
                'is_slider' => false,
                'order' => 1,
            ],
            [
                'title' => 'Executive Suite',
                'image' => 'images/mk_hotel/VIP/vip4.JPG',
                'category' => 'rooms',
                'is_slider' => false,
                'order' => 2,
            ],
            [
                'title' => 'Delux Room',
                'image' => 'images/mk_hotel/DELUX/delux.JPG',
                'category' => 'rooms',
                'is_slider' => false,
                'order' => 3,
            ],
            [
                'title' => 'Twin Bedroom',
                'image' => 'images/mk_hotel/twin_bed/twin.JPG',
                'category' => 'rooms',
                'is_slider' => false,
                'order' => 4,
            ],
            [
                'title' => 'Standard Room',
                'image' => 'images/mk_hotel/MINI/standard2.JPG',
                'category' => 'rooms',
                'is_slider' => false,
                'order' => 5,
            ],
            [
                'title' => 'Mini Standard',
                'image' => 'images/mk_hotel/MINI/mini.JPG',
                'category' => 'rooms',
                'is_slider' => false,
                'order' => 6,
            ],
        ];

        foreach (array_merge($sliderImages, $galleryImages) as $image) {
            GalleryImage::create($image);
        }
    }
}
