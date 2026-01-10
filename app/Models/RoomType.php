<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RoomType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'price_label',
        'description',
        'secondary_description',
        'image',
        'image_alt',
        'amenities',
        'total_rooms',
        'order',
        'is_featured',
        'extra_data',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'amenities' => 'array',
        'extra_data' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get rooms for this room type.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Get amenities for this room type.
     */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class);
    }

    /**
     * Get seasonal prices for this room type.
     */
    public function seasonalPrices(): HasMany
    {
        return $this->hasMany(SeasonalPrice::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'TZS ' . number_format($this->price, 0) . $this->price_label;
    }

    /**
     * Get available rooms count for a date range
     */
    public function getAvailableRooms($checkIn, $checkOut): int
    {
        $bookedRooms = $this->bookings()
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                    });
            })
            ->sum('number_of_rooms');

        return max(0, $this->total_rooms - $bookedRooms);
    }
}
