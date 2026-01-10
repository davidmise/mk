<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'room_type_id',
        'room_number',
        'floor',
        'status',
        'notes',
        'features',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    const STATUS_AVAILABLE = 'available';
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_RESERVED = 'reserved';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_CLEANING = 'cleaning';

    /**
     * Get the room type.
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Get all bookings for this room.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get current booking if room is occupied.
     */
    public function currentBooking()
    {
        return $this->bookings()
            ->where('status', 'checked_in')
            ->whereDate('check_in', '<=', now())
            ->whereDate('check_out', '>=', now())
            ->first();
    }

    /**
     * Check if room is available for given dates.
     */
    public function isAvailableForDates($checkIn, $checkOut): bool
    {
        if ($this->status !== self::STATUS_AVAILABLE && $this->status !== self::STATUS_RESERVED) {
            return false;
        }

        return !$this->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                    });
            })
            ->exists();
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get available rooms.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    /**
     * Scope to get active rooms.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by room type.
     */
    public function scopeOfType($query, int $roomTypeId)
    {
        return $query->where('room_type_id', $roomTypeId);
    }

    /**
     * Scope to filter by floor.
     */
    public function scopeOnFloor($query, string $floor)
    {
        return $query->where('floor', $floor);
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            self::STATUS_AVAILABLE => 'success',
            self::STATUS_OCCUPIED => 'danger',
            self::STATUS_RESERVED => 'warning',
            self::STATUS_MAINTENANCE => 'secondary',
            self::STATUS_CLEANING => 'info',
            default => 'secondary',
        };
    }

    /**
     * Get status icon for UI.
     */
    public function getStatusIcon(): string
    {
        return match($this->status) {
            self::STATUS_AVAILABLE => 'check-circle',
            self::STATUS_OCCUPIED => 'user',
            self::STATUS_RESERVED => 'calendar',
            self::STATUS_MAINTENANCE => 'wrench',
            self::STATUS_CLEANING => 'broom',
            default => 'circle',
        };
    }

    /**
     * Mark room as cleaning.
     */
    public function markAsCleaning(): void
    {
        $this->update(['status' => self::STATUS_CLEANING]);
    }

    /**
     * Mark room as available.
     */
    public function markAsAvailable(): void
    {
        $this->update(['status' => self::STATUS_AVAILABLE]);
    }

    /**
     * Mark room as maintenance.
     */
    public function markAsMaintenance(): void
    {
        $this->update(['status' => self::STATUS_MAINTENANCE]);
    }
}
