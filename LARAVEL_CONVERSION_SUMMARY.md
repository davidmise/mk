# MK Hotel - Laravel Application Summary

## Project Structure

The static HTML website has been transformed into a fully dynamic Laravel application located at:
`c:\laragon\www\mk_hotel_app`

## Database Configuration

- **Database Name:** `mk`
- **Connection:** MySQL
- **Host:** 127.0.0.1
- **Port:** 3306

## Routes

| Method | URI | Controller | Name |
|--------|-----|------------|------|
| GET | / | HomeController@index | home |
| GET | /about | AboutController@index | about |
| GET | /rooms | RoomController@index | rooms |
| GET | /rooms/{slug} | RoomController@show | rooms.show |
| GET | /amenities | AmenityController@index | amenities |
| GET | /gallery | GalleryController@index | gallery |
| GET | /contact | ContactController@index | contact |
| POST | /contact | ContactController@store | contact.store |
| POST | /booking | BookingController@store | booking.store |
| GET | /booking/availability | BookingController@checkAvailability | booking.availability |
| POST | /newsletter/subscribe | NewsletterController@subscribe | newsletter.subscribe |

## Database Tables

| Table | Purpose |
|-------|---------|
| site_settings | Key-value store for site configuration |
| pages | Page metadata (title, description, hero) |
| sections | Dynamic page sections with content |
| room_types | Hotel room categories with pricing |
| amenities | Hotel amenities/facilities |
| gallery_images | Photo gallery images |
| hero_slides | Homepage carousel slides |
| navigation_items | Dynamic navigation menu |
| social_links | Social media links |
| bookings | Room booking records |
| contact_messages | Contact form submissions |
| newsletter_subscribers | Newsletter subscriptions |

## Models

| Model | File |
|-------|------|
| SiteSetting | app/Models/SiteSetting.php |
| Page | app/Models/Page.php |
| Section | app/Models/Section.php |
| RoomType | app/Models/RoomType.php |
| Amenity | app/Models/Amenity.php |
| GalleryImage | app/Models/GalleryImage.php |
| HeroSlide | app/Models/HeroSlide.php |
| NavigationItem | app/Models/NavigationItem.php |
| SocialLink | app/Models/SocialLink.php |
| Booking | app/Models/Booking.php |
| ContactMessage | app/Models/ContactMessage.php |
| NewsletterSubscriber | app/Models/NewsletterSubscriber.php |

## Controllers

| Controller | Purpose |
|------------|---------|
| HomeController | Homepage with hero slides, featured rooms, amenities |
| AboutController | About page with dynamic sections |
| RoomController | Room listing and detail pages |
| AmenityController | Amenities listing page |
| GalleryController | Photo gallery page |
| ContactController | Contact page and form handling |
| BookingController | Room booking and availability check |
| NewsletterController | Newsletter subscription handling |

## Blade Components

| Component | Location | Purpose |
|-----------|----------|---------|
| layout | components/layout.blade.php | Main HTML wrapper |
| mobile-menu | components/mobile-menu.blade.php | Mobile navigation |
| navigation | components/navigation.blade.php | Desktop/mobile nav |
| footer | components/footer.blade.php | Site footer |
| hero-banner | components/hero-banner.blade.php | Page hero sections |
| booking-modal | components/booking-modal.blade.php | Booking form modal |
| room-card | components/room-card.blade.php | Room display card |
| amenity-card | components/amenity-card.blade.php | Amenity display |
| section-heading | components/section-heading.blade.php | Section titles |
| gallery-item | components/gallery-item.blade.php | Gallery image |

## Views

| Page | File |
|------|------|
| Home | resources/views/pages/home.blade.php |
| About | resources/views/pages/about.blade.php |
| Rooms | resources/views/pages/rooms.blade.php |
| Amenities | resources/views/pages/amenities.blade.php |
| Gallery | resources/views/pages/gallery.blade.php |
| Contact | resources/views/pages/contact.blade.php |

## Form Validation

| Request | Rules |
|---------|-------|
| ContactFormRequest | name (required), email (required, email), subject (optional), message (required) |
| BookingFormRequest | name (required), phone (required), room_type_id (required, exists), check_in/out (required, date), number_of_rooms (required, min:1) |

## Sample Eloquent Queries

### Get Active Navigation
```php
NavigationItem::with('children')
    ->active()
    ->rootItems()
    ->ordered()
    ->get();
```

### Get Featured Rooms
```php
RoomType::active()->featured()->ordered()->get();
```

### Check Room Availability
```php
$roomType->getAvailableRooms($checkIn, $checkOut);
```

### Get Page with Sections
```php
Page::findBySlug('about');
$page->sections()->where('key', 'philosophy')->first();
```

### Get Site Setting
```php
SiteSetting::get('phone_primary', '+255 747 685 401');
```

## Running the Application

```bash
# Start development server
cd c:\laragon\www\mk_hotel_app
php artisan serve --port=8000

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Clear caches
php artisan config:cache
php artisan view:clear
```

## Assets Location

All original CSS, JS, and images are preserved at:
- `public/css/` - Stylesheets
- `public/js/` - JavaScript files
- `public/images/` - All images including hotel photos

## Key Features Preserved

1. ✅ Responsive navigation with dropdown menus
2. ✅ Hero slider on homepage
3. ✅ Featured rooms display
4. ✅ Room booking modal with availability check
5. ✅ Contact form with validation
6. ✅ Newsletter subscription
7. ✅ Gallery with fancybox
8. ✅ Social media links
9. ✅ All original styling and JavaScript behavior
