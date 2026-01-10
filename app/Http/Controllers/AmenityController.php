<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\Page;
use App\Models\SiteSetting;

class AmenityController extends Controller
{
    public function index()
    {
        $page = Page::findBySlug('amenities');
        $amenities = Amenity::active()->ordered()->get();

        return view('pages.amenities', [
            'currentPage' => 'amenities',
            'page' => $page,
            'amenities' => $amenities,
            'title' => $page->title ?? SiteSetting::get('amenities_title', 'Amenities – MK Hotel Musoma'),
            'metaDescription' => $page->meta_description ?? SiteSetting::get('amenities_meta_description', 'Enjoy our top-notch amenities: swimming pool, free Wi-Fi, 24/7 room service.'),
            'metaKeywords' => $page->meta_keywords ?? SiteSetting::get('amenities_meta_keywords', 'MK Hotel amenities, Musoma hotel with pool'),
        ]);
    }
}
