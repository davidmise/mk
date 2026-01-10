<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'site_name', 'value' => 'MK Hotel', 'type' => 'text'],
            ['key' => 'site_author', 'value' => 'MK Hotel, Musoma', 'type' => 'text'],
            ['key' => 'meta_description', 'value' => 'MK Hotel is a 4-star hotel in Musoma located in Mwisenge near Lake Victoria. Enjoy our swimming pool, free Wi-Fi, luxurious rooms, and an amazing on-site restaurant.', 'type' => 'text'],
            ['key' => 'meta_keywords', 'value' => 'MK Hotel, Musoma hotel, hotel near Lake Victoria, 4-star hotel Musoma', 'type' => 'text'],

            // Logo
            ['key' => 'logo_desktop', 'value' => 'images/mk_hotel/logo/mk_text.png', 'type' => 'image'],
            ['key' => 'logo_mobile_text', 'value' => 'MK Hotel', 'type' => 'text'],
            ['key' => 'footer_logo', 'value' => 'images/mk_hotel/logo/mk_text.png', 'type' => 'image'],

            // Contact Info
            ['key' => 'email', 'value' => 'mkhotelltd@gmail.com', 'type' => 'text'],
            ['key' => 'phone_primary', 'value' => '+255 747 685 401', 'type' => 'text'],
            ['key' => 'phone_secondary', 'value' => '+255 776 310 757', 'type' => 'text'],
            ['key' => 'address', 'value' => "1007 Mwisenge, Musoma Urban,\nMara Tanzania", 'type' => 'text'],

            // Footer
            ['key' => 'footer_about', 'value' => 'This establishment provides paid lodging on a short-term basis. Facilities provided may range from a modest-quality.', 'type' => 'text'],
            ['key' => 'copyright_text', 'value' => 'MK Hotel', 'type' => 'text'],
            ['key' => 'design_credit', 'value' => 'Pamoja INC', 'type' => 'text'],

            // Page Titles
            ['key' => 'home_title', 'value' => 'MK Hotel Musoma – 4-Star Lakefront Luxury Hotel', 'type' => 'text'],
            ['key' => 'about_title', 'value' => 'About MK Hotel – Experience Comfort by Lake Victoria', 'type' => 'text'],
            ['key' => 'rooms_title', 'value' => 'Rooms – MK Hotel Musoma', 'type' => 'text'],
            ['key' => 'amenities_title', 'value' => 'Amenities – MK Hotel Musoma', 'type' => 'text'],
            ['key' => 'gallery_title', 'value' => 'Gallery – MK Hotel Musoma', 'type' => 'text'],
            ['key' => 'contact_title', 'value' => 'Contact MK Hotel – Musoma', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']]
            );
        }
    }
}
