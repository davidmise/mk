<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeasonalPrice extends Model
{
    protected $fillable = [
        'room_type_id',
        'name',
        'start_date',
        'end_date',
        'price',
        'discount_percentage',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the room type.
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Check if price is active for a given date.
     */
    public function isActiveForDate($date): bool
    {
        $date = is_string($date) ? \Carbon\Carbon::parse($date) : $date;

        return $this->is_active
            && $date->between($this->start_date, $this->end_date);
    }

    /**
     * Get the effective price (with discount if applicable).
     */
    public function getEffectivePrice(): float
    {
        if ($this->discount_percentage) {
            return $this->price * (1 - $this->discount_percentage / 100);
        }
        return $this->price;
    }

    /**
     * Scope to get active prices.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get prices valid for a date.
     */
    public function scopeValidForDate($query, $date)
    {
        return $query->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date);
    }

    /**
     * Scope to order by priority.
     */
    public function scopeByPriority($query)
    {
        return $query->orderByDesc('priority');
    }

    /**
     * Get the applicable seasonal price for a room type and date.
     */
    public static function getApplicablePrice(int $roomTypeId, $date): ?self
    {
        return static::where('room_type_id', $roomTypeId)
            ->active()
            ->validForDate($date)
            ->byPriority()
            ->first();
    }
}
