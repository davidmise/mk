<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'phone_secondary',
        'id_type',
        'id_number',
        'nationality',
        'address',
        'city',
        'country',
        'company',
        'notes',
        'preferences',
        'total_stays',
        'total_spent',
        'vip_status',
    ];

    protected $casts = [
        'preferences' => 'array',
        'total_spent' => 'decimal:2',
    ];

    const VIP_REGULAR = 'regular';
    const VIP_SILVER = 'silver';
    const VIP_GOLD = 'gold';
    const VIP_PLATINUM = 'platinum';

    /**
     * Get guest's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get all bookings for this guest.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all payments by this guest.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get all invoices for this guest.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Update guest statistics after a stay.
     */
    public function updateStayStatistics(float $amountSpent): void
    {
        $this->increment('total_stays');
        $this->increment('total_spent', $amountSpent);
        $this->updateVipStatus();
    }

    /**
     * Automatically update VIP status based on stays and spending.
     */
    public function updateVipStatus(): void
    {
        $status = match(true) {
            $this->total_stays >= 20 || $this->total_spent >= 5000000 => self::VIP_PLATINUM,
            $this->total_stays >= 10 || $this->total_spent >= 2000000 => self::VIP_GOLD,
            $this->total_stays >= 5 || $this->total_spent >= 500000 => self::VIP_SILVER,
            default => self::VIP_REGULAR,
        };

        if ($this->vip_status !== $status) {
            $this->update(['vip_status' => $status]);
        }
    }

    /**
     * Get VIP status color.
     */
    public function getVipStatusColor(): string
    {
        return match($this->vip_status) {
            self::VIP_PLATINUM => 'purple',
            self::VIP_GOLD => 'warning',
            self::VIP_SILVER => 'secondary',
            default => 'light',
        };
    }

    /**
     * Scope to search guests.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to filter by VIP status.
     */
    public function scopeVip($query, string $status)
    {
        return $query->where('vip_status', $status);
    }

    /**
     * Create or find a guest by phone/email.
     */
    public static function findOrCreateFromBooking(array $data): self
    {
        $guest = null;

        if (!empty($data['email'])) {
            $guest = static::where('email', $data['email'])->first();
        }

        if (!$guest && !empty($data['phone'])) {
            $guest = static::where('phone', $data['phone'])->first();
        }

        if (!$guest) {
            $nameParts = explode(' ', $data['name'] ?? '', 2);
            $guest = static::create([
                'first_name' => $nameParts[0] ?? '',
                'last_name' => $nameParts[1] ?? '',
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? '',
            ]);
        }

        return $guest;
    }
}
