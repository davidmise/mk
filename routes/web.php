<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\AmenityController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\NewsletterController;

// Main pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms');
Route::get('/rooms/{slug}', [RoomController::class, 'show'])->name('rooms.show');
Route::get('/amenities', [AmenityController::class, 'index'])->name('amenities');
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');

// Form submissions
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/availability', [BookingController::class, 'checkAvailability'])->name('booking.availability');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
