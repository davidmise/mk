<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // RBAC Seeders (run first)
            RoleSeeder::class,
            PermissionSeeder::class,
            AdminUserSeeder::class,
            SystemPreferenceSeeder::class,

            // Content Seeders
            SiteSettingsSeeder::class,
            NavigationSeeder::class,
            SocialLinksSeeder::class,
            HeroSlidesSeeder::class,
            PagesSeeder::class,
            RoomTypesSeeder::class,
            AmenitiesSeeder::class,
            GalleryImagesSeeder::class,
        ]);
    }
}
