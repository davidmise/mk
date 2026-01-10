<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Section;
use App\Models\SiteSetting;

class AboutController extends Controller
{
    public function index()
    {
        $page = Page::findBySlug('about');

        return view('pages.about', [
            'currentPage' => 'about',
            'page' => $page,
            'title' => $page->title ?? SiteSetting::get('about_title', 'About MK Hotel – Experience Comfort by Lake Victoria'),
            'metaDescription' => $page->meta_description ?? SiteSetting::get('about_meta_description', 'MK Hotel in Musoma offers a perfect blend of comfort, elegance, and local hospitality.'),
            'metaKeywords' => $page->meta_keywords ?? SiteSetting::get('about_meta_keywords', 'About MK Hotel, Musoma hotel information'),
        ]);
    }
}
