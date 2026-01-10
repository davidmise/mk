<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use App\Models\Page;
use App\Models\SiteSetting;

class GalleryController extends Controller
{
    public function index()
    {
        $page = Page::findBySlug('gallery');
        $sliderImages = GalleryImage::active()->slider()->ordered()->get();
        $galleryImages = GalleryImage::active()->ordered()->get();

        return view('pages.gallery', [
            'currentPage' => 'gallery',
            'page' => $page,
            'sliderImages' => $sliderImages,
            'galleryImages' => $galleryImages,
            'includeFancybox' => true,
            'title' => $page->title ?? SiteSetting::get('gallery_title', 'Gallery – MK Hotel Musoma'),
            'metaDescription' => $page->meta_description ?? SiteSetting::get('gallery_meta_description', 'View beautiful photos of MK Hotel Musoma.'),
            'metaKeywords' => $page->meta_keywords ?? SiteSetting::get('gallery_meta_keywords', 'MK Hotel photos, Musoma hotel gallery'),
        ]);
    }
}
