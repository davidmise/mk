<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NavigationItem;

class NavigationSeeder extends Seeder
{
    public function run(): void
    {
        // Main navigation items
        $home = NavigationItem::create([
            'label' => 'Home',
            'url' => '/',
            'order' => 1,
        ]);

        $rooms = NavigationItem::create([
            'label' => 'Rooms',
            'url' => '/rooms',
            'order' => 2,
        ]);

        // Room sub-items
        $roomTypes = [
            ['label' => 'Executive Suite', 'url' => '/rooms#exec', 'order' => 1],
            ['label' => 'Twin Bedroom', 'url' => '/rooms#twin', 'order' => 2],
            ['label' => 'Delux', 'url' => '/rooms#delux', 'order' => 3],
            ['label' => 'Standard', 'url' => '/rooms#standard', 'order' => 4],
            ['label' => 'Mini Standard', 'url' => '/rooms#mini-standard', 'order' => 5],
        ];

        foreach ($roomTypes as $type) {
            NavigationItem::create([
                'parent_id' => $rooms->id,
                'label' => $type['label'],
                'url' => $type['url'],
                'order' => $type['order'],
            ]);
        }

        NavigationItem::create([
            'label' => 'Amenities',
            'url' => '/amenities',
            'order' => 3,
        ]);

        NavigationItem::create([
            'label' => 'Gallery',
            'url' => '/gallery',
            'order' => 4,
        ]);

        NavigationItem::create([
            'label' => 'About Us',
            'url' => '/about',
            'order' => 5,
        ]);

        NavigationItem::create([
            'label' => 'Contact',
            'url' => '/contact',
            'order' => 6,
        ]);
    }
}
