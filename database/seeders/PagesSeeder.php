<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;
use App\Models\Section;

class PagesSeeder extends Seeder
{
    public function run(): void
    {
        // About Page
        $about = Page::create([
            'slug' => 'about',
            'title' => 'About MK Hotel – Experience Comfort by Lake Victoria',
            'meta_description' => 'MK Hotel in Musoma offers a perfect blend of comfort, elegance, and local hospitality. Discover our mission, values, and what sets us apart.',
            'meta_keywords' => 'About MK Hotel, Musoma hotel information, hotel near Lake Victoria, best hotels Tanzania',
            'hero_heading' => 'About MK Hotel',
            'hero_subtext' => 'Facilities provided may range from a modest-quality mattress in a small room to large suites with bigger.',
            'hero_image' => 'images/mk_hotel/outside.jpg',
        ]);

        Section::create([
            'page_id' => $about->id,
            'key' => 'management_image',
            'image' => 'images/mk_hotel/DELUX/delux.JPG',
            'order' => 1,
        ]);

        Section::create([
            'page_id' => $about->id,
            'key' => 'philosophy',
            'title' => 'Philosophy',
            'content' => '<p>At MK Hotel, our philosophy is simple: to deliver an exceptional guest experience through genuine hospitality, attention to detail, and a passion for excellence. We believe that comfort, service, and care should be at the heart of every stay.</p><p>Our team is dedicated to creating a warm and welcoming environment where guests feel valued and cared for — whether they\'re here for business, leisure, or a special occasion.</p>',
            'order' => 2,
        ]);

        Section::create([
            'page_id' => $about->id,
            'key' => 'philosophy_secondary',
            'content' => '<p>We strive to blend modern luxury with local charm, offering well-appointed rooms, thoughtful amenities, and personalized services. From our management to our staff, every member of the MK Hotel family shares a commitment to quality, comfort, and your total satisfaction.</p>',
            'order' => 3,
        ]);

        Section::create([
            'page_id' => $about->id,
            'key' => 'commitment',
            'title' => 'Our Commitment to Comfort',
            'content' => '<p>We are committed to ensuring every guest enjoys a restful, comfortable, and memorable stay. From our plush bedding and modern amenities to our attentive service, every detail is thoughtfully designed with your well-being in mind.</p>',
            'order' => 4,
        ]);

        // Rooms Page
        $rooms = Page::create([
            'slug' => 'rooms',
            'title' => 'Rooms – MK Hotel Musoma',
            'meta_description' => 'Explore our spacious and elegant rooms at MK Hotel Musoma. Perfect for family, business, or leisure stays near Lake Victoria.',
            'meta_keywords' => 'hotel rooms Musoma, MK Hotel rooms, accommodation near Lake Victoria, luxury rooms Tanzania',
            'hero_heading' => 'Our Rooms',
            'hero_image' => 'images/mk_hotel/vip.jpg',
        ]);

        Section::create([
            'page_id' => $rooms->id,
            'key' => 'intro',
            'title' => 'Enjoy Your Stay',
            'content' => 'At MK Hotel, we believe every guest deserves more than just a place to sleep. Whether you\'re visiting for business or leisure, our thoughtful service, elegant spaces, and relaxing atmosphere are designed to make every moment of your stay truly memorable.',
            'order' => 1,
        ]);

        // Amenities Page
        Page::create([
            'slug' => 'amenities',
            'title' => 'Amenities – MK Hotel Musoma',
            'meta_description' => 'Enjoy our top-notch amenities: swimming pool, free Wi-Fi, 24/7 room service, secure parking, and more at MK Hotel Musoma.',
            'meta_keywords' => 'MK Hotel amenities, Musoma hotel with pool, hotel with Wi-Fi, hotel with restaurant Tanzania',
            'hero_heading' => 'Amenities',
            'hero_image' => 'images/mk_hotel/amenities.jpg',
        ]);

        // Gallery Page
        Page::create([
            'slug' => 'gallery',
            'title' => 'Gallery – MK Hotel Musoma',
            'meta_description' => 'View beautiful photos of MK Hotel Musoma, including our lakefront views, rooms, amenities, and dining experience.',
            'meta_keywords' => 'MK Hotel photos, Musoma hotel gallery, Lake Victoria hotel images, hotel interior photos Tanzania',
            'hero_heading' => 'Gallery',
            'hero_image' => 'images/mk_hotel/vip.jpg',
        ]);

        // Contact Page
        Page::create([
            'slug' => 'contact',
            'title' => 'Contact MK Hotel – Musoma',
            'meta_description' => 'Contact MK Hotel located in Mwisenge, Musoma near Lake Victoria. Reach us for bookings, directions, and customer service.',
            'meta_keywords' => 'contact MK Hotel, hotel in Musoma, Musoma hotel phone number, Lake Victoria hotel contact',
            'hero_heading' => 'Contact Us',
            'hero_subtext' => 'Have a question, request, or feedback? We\'re here to help. Reach out to our team anytime — we\'d love to hear from you.',
            'hero_image' => 'images/mk_hotel/outside.jpg',
        ]);
    }
}
