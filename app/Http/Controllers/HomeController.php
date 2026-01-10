<?php

namespace App\Http\Controllers;

use App\Models\HeroSlide;
use App\Models\RoomType;
use App\Models\Amenity;
use App\Models\SiteSetting;

class HomeController extends Controller
{
    public function index()
    {
        $heroSlides = HeroSlide::active()->ordered()->get();
        $featuredRooms = RoomType::active()->featured()->ordered()->get();
        $roomTypes = RoomType::active()->ordered()->get();
        $amenities = Amenity::active()->ordered()->take(6)->get();

        return view('pages.home', [
            'currentPage' => 'home',
            'heroSlides' => $heroSlides,
            'featuredRooms' => $featuredRooms,
            'roomTypes' => $roomTypes,
            'amenities' => $amenities,
            'title' => SiteSetting::get('home_title', 'MK Hotel Musoma – 4-Star Lakefront Luxury Hotel'),
            'metaDescription' => SiteSetting::get('home_meta_description', 'MK Hotel is a 4-star hotel in Musoma located in Mwisenge near Lake Victoria.'),
            'metaKeywords' => SiteSetting::get('home_meta_keywords', 'MK Hotel, Musoma hotel, hotel near Lake Victoria'),
        ]);
    }
}
