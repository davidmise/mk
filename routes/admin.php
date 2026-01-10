<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\AmenityController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\GuestController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CalendarController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group and "admin" prefix.
|
*/

// Authentication Routes (Unauthenticated)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [AuthController::class, 'login'])->name('admin.login.submit');
    Route::get('forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('admin.password.request');
    Route::post('forgot-password', [AuthController::class, 'sendResetLink'])->name('admin.password.email');
    Route::get('reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('admin.password.reset');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('admin.password.update');
});

// Authenticated Admin Routes
Route::middleware(['admin.auth'])->group(function () {

    // Logout
    Route::post('logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Debug route - check user permissions
    Route::get('/debug/user', function() {
        $user = auth()->user();
        return response()->json([
            'user_id' => $user->id,
            'email' => $user->email,
            'roles_count' => $user->roles()->count(),
            'roles' => $user->roles->map(function($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug,
                    'level' => $role->level,
                    'permissions_count' => $role->permissions()->count(),
                    'permissions' => $role->permissions->pluck('slug')->toArray(),
                ];
            }),
            'primary_role' => $user->getPrimaryRole() ? [
                'id' => $user->getPrimaryRole()->id,
                'name' => $user->getPrimaryRole()->name,
                'slug' => $user->getPrimaryRole()->slug,
            ] : null,
            'is_super_admin' => $user->isSuperAdmin(),
            'has_dashboard_view' => $user->hasPermission('dashboard.view'),
        ]);
    })->name('admin.debug.user');

    // Debug route - check all seeded data
    Route::get('/debug/data', function() {
        return response()->json([
            'users_total' => \App\Models\User::count(),
            'roles_total' => \App\Models\Role::count(),
            'permissions_total' => \App\Models\Permission::count(),
            'user_roles_total' => \DB::table('user_roles')->count(),
            'role_permissions_total' => \DB::table('role_permissions')->count(),
            'users' => \App\Models\User::with('roles')->limit(10)->get()->map(function($user) {
                return [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'roles' => $user->roles->pluck('name')->toArray(),
                ];
            }),
            'roles' => \App\Models\Role::all()->map(function($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug,
                    'permissions_count' => $role->permissions()->count(),
                ];
            }),
        ]);
    })->name('admin.debug.data');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('dashboard/stats', [DashboardController::class, 'getStats'])->name('admin.dashboard.stats');
    Route::get('dashboard/revenue-chart', [DashboardController::class, 'revenueChart'])->name('admin.dashboard.revenue-chart');
    Route::get('dashboard/booking-chart', [DashboardController::class, 'bookingChart'])->name('admin.dashboard.booking-chart');
    Route::get('dashboard/activity', [DashboardController::class, 'recentActivity'])->name('admin.dashboard.activity');

    // Profile Management
    Route::get('profile', [AuthController::class, 'showProfile'])->name('admin.profile');
    Route::put('profile', [AuthController::class, 'updateProfile'])->name('admin.profile.update');
    Route::put('profile/password', [AuthController::class, 'updatePassword'])->name('admin.profile.password');

    /*
    |--------------------------------------------------------------------------
    | User Management (RBAC)
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:users.view')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('admin.users.show');
    });

    Route::middleware('permission:users.create')->group(function () {
        Route::get('users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('users', [UserController::class, 'store'])->name('admin.users.store');
    });

    Route::middleware('permission:users.edit')->group(function () {
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::post('users/{user}/suspend', [UserController::class, 'suspend'])->name('admin.users.suspend');
        Route::post('users/{user}/activate', [UserController::class, 'activate'])->name('admin.users.activate');
        Route::post('users/{user}/unlock', [UserController::class, 'unlock'])->name('admin.users.unlock');
        Route::post('users/{user}/send-password-reset', [UserController::class, 'sendPasswordReset'])->name('admin.users.send-password-reset');
    });

    Route::middleware('permission:users.delete')->group(function () {
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Role Management
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:roles.view')->group(function () {
        Route::get('roles', [RoleController::class, 'index'])->name('admin.roles.index');
        Route::get('roles/{role}', [RoleController::class, 'show'])->name('admin.roles.show');
    });

    Route::middleware('permission:roles.create')->group(function () {
        Route::get('roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('admin.roles.store');
    });

    Route::middleware('permission:roles.edit')->group(function () {
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
    });

    Route::middleware('permission:roles.delete')->group(function () {
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Booking Management
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:bookings.view')->group(function () {
        Route::get('bookings', [BookingController::class, 'index'])->name('admin.bookings.index');
        Route::get('bookings/{booking}', [BookingController::class, 'show'])->name('admin.bookings.show');
    });

    Route::middleware('permission:bookings.create')->group(function () {
        Route::get('bookings/create', [BookingController::class, 'create'])->name('admin.bookings.create');
        Route::post('bookings', [BookingController::class, 'store'])->name('admin.bookings.store');
        Route::post('bookings/check-availability', [BookingController::class, 'checkAvailability'])->name('admin.bookings.check-availability');
        Route::post('bookings/calculate-price', [BookingController::class, 'calculatePrice'])->name('admin.bookings.calculate-price');
    });

    Route::middleware('permission:bookings.edit')->group(function () {
        Route::get('bookings/{booking}/edit', [BookingController::class, 'edit'])->name('admin.bookings.edit');
        Route::put('bookings/{booking}', [BookingController::class, 'update'])->name('admin.bookings.update');
        Route::post('bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('admin.bookings.confirm');
        Route::post('bookings/{booking}/check-in', [BookingController::class, 'checkIn'])->name('admin.bookings.check-in');
        Route::post('bookings/{booking}/check-out', [BookingController::class, 'checkOut'])->name('admin.bookings.check-out');
        Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('admin.bookings.cancel');
        Route::post('bookings/{booking}/assign-room', [BookingController::class, 'assignRoom'])->name('admin.bookings.assign-room');
        Route::post('bookings/{booking}/add-payment', [BookingController::class, 'addPayment'])->name('admin.bookings.add-payment');
        Route::post('bookings/{booking}/add-extra', [BookingController::class, 'addExtra'])->name('admin.bookings.add-extra');
    });

    Route::middleware('permission:bookings.delete')->group(function () {
        Route::delete('bookings/{booking}', [BookingController::class, 'destroy'])->name('admin.bookings.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Room Management
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:rooms.view')->group(function () {
        Route::get('rooms', [RoomController::class, 'index'])->name('admin.rooms.index');
        Route::get('rooms/{room}', [RoomController::class, 'show'])->name('admin.rooms.show');
    });

    Route::middleware('permission:rooms.create')->group(function () {
        Route::get('rooms/create', [RoomController::class, 'create'])->name('admin.rooms.create');
        Route::post('rooms', [RoomController::class, 'store'])->name('admin.rooms.store');
        Route::post('rooms/bulk', [RoomController::class, 'bulkStore'])->name('admin.rooms.bulk-store');
    });

    Route::middleware('permission:rooms.edit')->group(function () {
        Route::get('rooms/{room}/edit', [RoomController::class, 'edit'])->name('admin.rooms.edit');
        Route::put('rooms/{room}', [RoomController::class, 'update'])->name('admin.rooms.update');
        Route::patch('rooms/{room}/status', [RoomController::class, 'updateStatus'])->name('admin.rooms.update-status');
        Route::patch('rooms/{room}/cleanliness', [RoomController::class, 'updateCleanliness'])->name('admin.rooms.update-cleanliness');
    });

    Route::middleware('permission:rooms.delete')->group(function () {
        Route::delete('rooms/{room}', [RoomController::class, 'destroy'])->name('admin.rooms.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Room Type Management
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:room_types.view')->group(function () {
        Route::get('room-types', [RoomTypeController::class, 'index'])->name('admin.room-types.index');
        Route::get('room-types/{roomType}', [RoomTypeController::class, 'show'])->name('admin.room-types.show');
    });

    Route::middleware('permission:room_types.create')->group(function () {
        Route::get('room-types/create', [RoomTypeController::class, 'create'])->name('admin.room-types.create');
        Route::post('room-types', [RoomTypeController::class, 'store'])->name('admin.room-types.store');
    });

    Route::middleware('permission:room_types.edit')->group(function () {
        Route::get('room-types/{roomType}/edit', [RoomTypeController::class, 'edit'])->name('admin.room-types.edit');
        Route::put('room-types/{roomType}', [RoomTypeController::class, 'update'])->name('admin.room-types.update');
        Route::post('room-types/{roomType}/seasonal-prices', [RoomTypeController::class, 'storeSeasonalPrice'])->name('admin.room-types.seasonal-prices.store');
        Route::put('room-types/{roomType}/seasonal-prices/{price}', [RoomTypeController::class, 'updateSeasonalPrice'])->name('admin.room-types.seasonal-prices.update');
        Route::delete('room-types/{roomType}/seasonal-prices/{price}', [RoomTypeController::class, 'destroySeasonalPrice'])->name('admin.room-types.seasonal-prices.destroy');
    });

    Route::middleware('permission:room_types.delete')->group(function () {
        Route::delete('room-types/{roomType}', [RoomTypeController::class, 'destroy'])->name('admin.room-types.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Amenity Management
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:amenities.view')->group(function () {
        Route::get('amenities', [AmenityController::class, 'index'])->name('admin.amenities.index');
    });

    Route::middleware('permission:amenities.create')->group(function () {
        Route::get('amenities/create', [AmenityController::class, 'create'])->name('admin.amenities.create');
        Route::post('amenities', [AmenityController::class, 'store'])->name('admin.amenities.store');
    });

    Route::middleware('permission:amenities.edit')->group(function () {
        Route::get('amenities/{amenity}/edit', [AmenityController::class, 'edit'])->name('admin.amenities.edit');
        Route::put('amenities/{amenity}', [AmenityController::class, 'update'])->name('admin.amenities.update');
        Route::patch('amenities/{amenity}/toggle', [AmenityController::class, 'toggleStatus'])->name('admin.amenities.toggle');
        Route::post('amenities/update-order', [AmenityController::class, 'updateOrder'])->name('admin.amenities.update-order');
    });

    Route::middleware('permission:amenities.delete')->group(function () {
        Route::delete('amenities/{amenity}', [AmenityController::class, 'destroy'])->name('admin.amenities.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Guest Management
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:guests.view')->group(function () {
        Route::get('guests', [GuestController::class, 'index'])->name('admin.guests.index');
        Route::get('guests/{guest}', [GuestController::class, 'show'])->name('admin.guests.show');
    });

    Route::middleware('permission:guests.create')->group(function () {
        Route::get('guests/create', [GuestController::class, 'create'])->name('admin.guests.create');
        Route::post('guests', [GuestController::class, 'store'])->name('admin.guests.store');
    });

    Route::middleware('permission:guests.edit')->group(function () {
        Route::get('guests/{guest}/edit', [GuestController::class, 'edit'])->name('admin.guests.edit');
        Route::put('guests/{guest}', [GuestController::class, 'update'])->name('admin.guests.update');
        Route::patch('guests/{guest}/vip-status', [GuestController::class, 'updateVipStatus'])->name('admin.guests.update-vip');
        Route::post('guests/merge', [GuestController::class, 'merge'])->name('admin.guests.merge');
    });

    Route::middleware('permission:guests.delete')->group(function () {
        Route::delete('guests/{guest}', [GuestController::class, 'destroy'])->name('admin.guests.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | CMS - Pages
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:pages.view')->group(function () {
        Route::get('pages', [PageController::class, 'index'])->name('admin.pages.index');
        Route::get('pages/{page}', [PageController::class, 'show'])->name('admin.pages.show');
    });

    Route::middleware('permission:pages.create')->group(function () {
        Route::get('pages/create', [PageController::class, 'create'])->name('admin.pages.create');
        Route::post('pages', [PageController::class, 'store'])->name('admin.pages.store');
    });

    Route::middleware('permission:pages.edit')->group(function () {
        Route::get('pages/{page}/edit', [PageController::class, 'edit'])->name('admin.pages.edit');
        Route::put('pages/{page}', [PageController::class, 'update'])->name('admin.pages.update');
        Route::patch('pages/{page}/toggle', [PageController::class, 'toggleStatus'])->name('admin.pages.toggle');
        Route::post('pages/reorder', [PageController::class, 'reorder'])->name('admin.pages.reorder');

        // Section management
        Route::post('pages/{page}/sections', [PageController::class, 'addSection'])->name('admin.pages.sections.add');
        Route::put('pages/{page}/sections/{section}', [PageController::class, 'updateSection'])->name('admin.pages.sections.update');
        Route::delete('pages/{page}/sections/{section}', [PageController::class, 'deleteSection'])->name('admin.pages.sections.delete');
        Route::post('pages/{page}/sections/reorder', [PageController::class, 'reorderSections'])->name('admin.pages.sections.reorder');
    });

    Route::middleware('permission:pages.edit')->group(function () {
        Route::post('pages/bulk-update', [PageController::class, 'bulkUpdate'])->name('admin.pages.bulkUpdate');
    });

    Route::middleware('permission:pages.delete')->group(function () {
        Route::delete('pages/{page}', [PageController::class, 'destroy'])->name('admin.pages.destroy');
        Route::post('pages/bulk-destroy', [PageController::class, 'bulkDestroy'])->name('admin.pages.bulkDestroy');
    });

    /*
    |--------------------------------------------------------------------------
    | CMS - News/Blog
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:news.view')->group(function () {
        Route::get('news', [NewsController::class, 'index'])->name('admin.news.index');
        Route::get('news/{article}', [NewsController::class, 'show'])->name('admin.news.show');
    });

    Route::middleware('permission:news.create')->group(function () {
        Route::get('news/create', [NewsController::class, 'create'])->name('admin.news.create');
        Route::post('news', [NewsController::class, 'store'])->name('admin.news.store');
    });

    Route::middleware('permission:news.edit')->group(function () {
        Route::get('news/{article}/edit', [NewsController::class, 'edit'])->name('admin.news.edit');
        Route::put('news/{article}', [NewsController::class, 'update'])->name('admin.news.update');
        Route::post('news/{article}/publish', [NewsController::class, 'publish'])->name('admin.news.publish');
        Route::post('news/{article}/unpublish', [NewsController::class, 'unpublish'])->name('admin.news.unpublish');
        Route::post('news/{article}/archive', [NewsController::class, 'archive'])->name('admin.news.archive');
        Route::patch('news/{article}/toggle-featured', [NewsController::class, 'toggleFeatured'])->name('admin.news.toggle-featured');
        Route::post('news/bulk', [NewsController::class, 'bulk'])->name('admin.news.bulk');
    });

    Route::middleware('permission:news.delete')->group(function () {
        Route::delete('news/{article}', [NewsController::class, 'destroy'])->name('admin.news.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | CMS - Gallery
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:gallery.view')->group(function () {
        Route::get('gallery', [GalleryController::class, 'index'])->name('admin.gallery.index');
    });

    Route::middleware('permission:gallery.create')->group(function () {
        Route::get('gallery/create', [GalleryController::class, 'create'])->name('admin.gallery.create');
        Route::post('gallery', [GalleryController::class, 'store'])->name('admin.gallery.store');
        Route::post('gallery/bulk', [GalleryController::class, 'bulkStore'])->name('admin.gallery.bulk-store');
    });

    Route::middleware('permission:gallery.edit')->group(function () {
        Route::get('gallery/{image}/edit', [GalleryController::class, 'edit'])->name('admin.gallery.edit');
        Route::put('gallery/{image}', [GalleryController::class, 'update'])->name('admin.gallery.update');
        Route::patch('gallery/{image}/toggle-slider', [GalleryController::class, 'toggleSlider'])->name('admin.gallery.toggle-slider');
        Route::post('gallery/update-order', [GalleryController::class, 'updateOrder'])->name('admin.gallery.update-order');
    });

    Route::middleware('permission:gallery.delete')->group(function () {
        Route::delete('gallery/{image}', [GalleryController::class, 'destroy'])->name('admin.gallery.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Calendar & Availability
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:calendar.view')->group(function () {
        Route::get('calendar', [CalendarController::class, 'index'])->name('admin.calendar.index');
        Route::get('calendar/availability', [CalendarController::class, 'availability'])->name('admin.calendar.availability');
        Route::get('calendar/room/{room}/bookings', [CalendarController::class, 'roomBookings'])->name('admin.calendar.room-bookings');
        Route::get('calendar/today', [CalendarController::class, 'todaysActivity'])->name('admin.calendar.today');
    });

    Route::middleware('permission:calendar.edit')->group(function () {
        Route::post('calendar/quick-book', [CalendarController::class, 'quickBook'])->name('admin.calendar.quick-book');
        Route::post('calendar/bulk-status', [CalendarController::class, 'bulkStatusUpdate'])->name('admin.calendar.bulk-status');
    });

    /*
    |--------------------------------------------------------------------------
    | Reports & Analytics
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:reports.view')->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('admin.reports.index');
        Route::get('reports/occupancy', [ReportController::class, 'occupancy'])->name('admin.reports.occupancy');
        Route::get('reports/revenue', [ReportController::class, 'revenue'])->name('admin.reports.revenue');
        Route::get('reports/financial', [ReportController::class, 'financial'])->name('admin.reports.financial');
        Route::get('reports/bookings', [ReportController::class, 'bookings'])->name('admin.reports.bookings');
        Route::get('reports/guests', [ReportController::class, 'guests'])->name('admin.reports.guests');
        Route::get('reports/rooms', [ReportController::class, 'rooms'])->name('admin.reports.rooms');
        Route::get('reports/export/{report}', [ReportController::class, 'export'])->name('admin.reports.export');
    });

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:settings.view')->group(function () {
        Route::get('settings', [SettingsController::class, 'index'])->name('admin.settings.index');
        Route::get('settings/branding', [SettingsController::class, 'branding'])->name('admin.settings.branding');
        Route::get('settings/seo', [SettingsController::class, 'seo'])->name('admin.settings.seo');
        Route::get('settings/preferences', [SettingsController::class, 'preferences'])->name('admin.settings.preferences');
        Route::get('settings/hero-slides', [SettingsController::class, 'heroSlides'])->name('admin.settings.hero-slides');
        Route::get('settings/social-links', [SettingsController::class, 'socialLinks'])->name('admin.settings.social-links');
        Route::get('settings/maintenance', [SettingsController::class, 'maintenance'])->name('admin.settings.maintenance');
    });

    Route::middleware('permission:settings.edit')->group(function () {
        Route::put('settings', [SettingsController::class, 'update'])->name('admin.settings.update');
        Route::put('settings/branding', [SettingsController::class, 'updateBranding'])->name('admin.settings.branding.update');
        Route::put('settings/seo', [SettingsController::class, 'updateSeo'])->name('admin.settings.seo.update');
        Route::put('settings/preferences', [SettingsController::class, 'updatePreferences'])->name('admin.settings.preferences.update');

        // Hero slides
        Route::post('settings/hero-slides', [SettingsController::class, 'storeHeroSlide'])->name('admin.settings.hero-slides.store');
        Route::put('settings/hero-slides/{slide}', [SettingsController::class, 'updateHeroSlide'])->name('admin.settings.hero-slides.update');
        Route::delete('settings/hero-slides/{slide}', [SettingsController::class, 'deleteHeroSlide'])->name('admin.settings.hero-slides.destroy');

        // Social links
        Route::post('settings/social-links', [SettingsController::class, 'storeSocialLink'])->name('admin.settings.social-links.store');
        Route::put('settings/social-links/{link}', [SettingsController::class, 'updateSocialLink'])->name('admin.settings.social-links.update');
        Route::delete('settings/social-links/{link}', [SettingsController::class, 'deleteSocialLink'])->name('admin.settings.social-links.destroy');

        // Maintenance
        Route::post('settings/clear-cache', [SettingsController::class, 'clearCache'])->name('admin.settings.clear-cache');
        Route::post('settings/clear-logs', [SettingsController::class, 'clearLogs'])->name('admin.settings.clear-logs');
    });
});
