<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\ContactMessage;
use App\Models\SiteSetting;
use App\Http\Requests\ContactFormRequest;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $page = Page::findBySlug('contact');

        return view('pages.contact', [
            'currentPage' => 'contact',
            'page' => $page,
            'includeFancybox' => true,
            'title' => $page->title ?? SiteSetting::get('contact_title', 'Contact MK Hotel – Musoma'),
            'metaDescription' => $page->meta_description ?? SiteSetting::get('contact_meta_description', 'Contact MK Hotel located in Mwisenge, Musoma near Lake Victoria.'),
            'metaKeywords' => $page->meta_keywords ?? SiteSetting::get('contact_meta_keywords', 'contact MK Hotel, hotel in Musoma'),
        ]);
    }

    public function store(ContactFormRequest $request)
    {
        ContactMessage::create($request->validated());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for your message. We will get back to you soon!'
            ]);
        }

        return redirect()->back()->with('success', 'Thank you for your message. We will get back to you soon!');
    }
}
