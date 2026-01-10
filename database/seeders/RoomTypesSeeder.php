<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomType;

class RoomTypesSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            [
                'name' => 'Executive Suite',
                'slug' => 'exec',
                'price' => 200000,
                'price_label' => '/night',
                'description' => 'Our Executive Suite offers a perfect blend of elegance, comfort, and privacy — ideal for business travelers and luxury seekers. With a spacious layout, lounge area, and premium furnishings, it redefines upscale living.',
                'secondary_description' => 'Enjoy an upgraded hospitality experience with exclusive amenities and top-tier services tailored to your needs.',
                'image' => 'images/mk_hotel/vip.jpg',
                'amenities' => [
                    'Private lounge with seating area',
                    'Smart 55-inch TV with international channels',
                    'Rainfall shower and bathtub',
                    'In-room workspace with high-speed Wi-Fi',
                    'Complimentary welcome drink and fruit basket',
                ],
                'total_rooms' => 5,
                'order' => 1,
                'is_featured' => true,
                'extra_data' => json_encode(['secondary_image' => 'images/mk_hotel/VIP/vip.JPG']),
            ],
            [
                'name' => 'Twin Bedroom',
                'slug' => 'twin',
                'price' => 150000,
                'price_label' => '/night',
                'description' => 'Our Twin Bedroom is ideal for friends or colleagues traveling together. It features two separate beds, a cozy interior, and modern finishes to ensure a comfortable stay for all guests.',
                'secondary_description' => 'Perfect for short-term visits or group travel, it combines convenience and functionality at a great value.',
                'image' => 'images/mk_hotel/twin_bed/twin.JPG',
                'amenities' => [
                    'Two single beds with luxury bedding',
                    'Flat-screen TV',
                    'High-speed Wi-Fi',
                    'Modern bathroom with walk-in shower',
                    'Complimentary bottled water',
                ],
                'total_rooms' => 8,
                'order' => 2,
                'is_featured' => true,
                'extra_data' => json_encode(['secondary_image' => 'images/mk_hotel/twin_bed/twin.JPG']),
            ],
            [
                'name' => 'Delux Room',
                'slug' => 'delux',
                'price' => 120000,
                'price_label' => '/night',
                'description' => 'The Delux Room strikes the perfect balance between elegance and comfort. With refined décor, a plush bed, and spacious interiors, it\'s perfect for both couples and solo travelers looking for a touch of luxury.',
                'secondary_description' => 'Enjoy thoughtful touches and elevated service to enhance your stay in every way.',
                'image' => 'images/mk_hotel/DELUX/delux.JPG',
                'amenities' => [
                    'Queen-sized bed',
                    '40-inch HD TV',
                    'Rain shower & premium toiletries',
                    'Mini-bar and coffee station',
                    'Air conditioning and blackout curtains',
                ],
                'total_rooms' => 10,
                'order' => 3,
                'is_featured' => false,
            ],
            [
                'name' => 'Standard Room',
                'slug' => 'standard',
                'price' => 80000,
                'price_label' => '/night',
                'description' => 'Our Standard Room offers everything you need for a comfortable and affordable stay. It\'s the ideal choice for travelers who appreciate simplicity without compromising on quality and cleanliness.',
                'secondary_description' => 'Designed for efficiency and ease, this room is perfect for short stays or business trips.',
                'image' => 'images/mk_hotel/MINI/standard2.JPG',
                'amenities' => [
                    'Double bed with high-quality linens',
                    'LED TV with local and satellite channels',
                    'Compact work desk',
                    'High-speed Wi-Fi',
                    'Private bathroom with shower',
                ],
                'total_rooms' => 12,
                'order' => 4,
                'is_featured' => false,
            ],
            [
                'name' => 'Mini Standard Room',
                'slug' => 'mini-standard',
                'price' => 60000,
                'price_label' => '/night',
                'description' => 'The Mini Standard Room is compact yet thoughtfully designed for solo travelers or quick overnight stays. It offers all the essentials you need to rest and recharge in a cozy environment.',
                'secondary_description' => 'This budget-friendly option delivers comfort, privacy, and value without compromise.',
                'image' => 'images/mk_hotel/MINI/mini.JPG',
                'amenities' => [
                    'Single bed with soft linens',
                    'Wall-mounted flat screen TV',
                    'Private bathroom with hot shower',
                    'Free Wi-Fi',
                    'Ceiling fan or A/C (based on availability)',
                ],
                'total_rooms' => 15,
                'order' => 5,
                'is_featured' => false,
            ],
        ];

        foreach ($rooms as $room) {
            $extraData = $room['extra_data'] ?? null;
            unset($room['extra_data']);

            $roomType = RoomType::create($room);

            if ($extraData) {
                $roomType->update(['extra_data' => $extraData]);
            }
        }
    }
}
