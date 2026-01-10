<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use App\Models\Page;
use App\Models\SiteSetting;

class RoomController extends Controller
{
    public function index()
    {
        $page = Page::findBySlug('rooms');
        $rooms = RoomType::active()->ordered()->get();

        return view('pages.rooms', [
            'currentPage' => 'rooms',
            'page' => $page,
            'rooms' => $rooms,
            'title' => $page->title ?? SiteSetting::get('rooms_title', 'Rooms – MK Hotel Musoma'),
            'metaDescription' => $page->meta_description ?? SiteSetting::get('rooms_meta_description', 'Explore our spacious and elegant rooms at MK Hotel Musoma.'),
            'metaKeywords' => $page->meta_keywords ?? SiteSetting::get('rooms_meta_keywords', 'hotel rooms Musoma, MK Hotel rooms'),
        ]);
    }

    public function show($slug)
    {
        $room = RoomType::where('slug', $slug)->active()->firstOrFail();
        $otherRooms = RoomType::active()->where('id', '!=', $room->id)->ordered()->take(3)->get();

        return view('pages.room-detail', [
            'currentPage' => 'rooms',
            'room' => $room,
            'otherRooms' => $otherRooms,
            'title' => $room->name . ' – MK Hotel Musoma',
            'metaDescription' => $room->description,
        ]);
    }
}
