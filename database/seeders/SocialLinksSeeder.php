<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SocialLink;

class SocialLinksSeeder extends Seeder
{
    public function run(): void
    {
        $socialLinks = [
            [
                'platform' => 'Facebook',
                'url' => '#',
                'icon' => 'icon-facebook',
                'order' => 1,
            ],
            [
                'platform' => 'Twitter',
                'url' => '#',
                'icon' => 'icon-twitter',
                'order' => 2,
            ],
            [
                'platform' => 'Instagram',
                'url' => 'https://www.instagram.com/mkhotel_ltd?igsh=bXc3NGZyaDRqMHN0',
                'icon' => 'icon-instagram',
                'order' => 3,
            ],
            [
                'platform' => 'TripAdvisor',
                'url' => '#',
                'icon' => 'icon-tripadvisor',
                'order' => 4,
            ],
        ];

        foreach ($socialLinks as $link) {
            SocialLink::create($link);
        }
    }
}
