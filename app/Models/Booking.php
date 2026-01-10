<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_reference',
        'guest_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'room_type_id',
        'room_id',
        'check_in',
        'check_out',
        'number_of_rooms',
        'adults',
        'children',
        'source',
        'external_reference',
        'total_price',
        'room_rate',
        'tax_amount',
        'service_charge',
        'discount_amount',
        'discount_reason',
        'payment_status',
        'payment_method',
        'amount_paid',
        'status',
        'notes',
        'confirmed_at',
        'checked_in_at',
        'checked_out_at',
        'cancelled_at',
        'created_by',
        'confirmed_by',
        'checked_in_by',
        'checked_out_by',
        'cancellation_reason',
        'special_requests',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'total_price' => 'decimal:2',
        'room_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CHECKED_IN = 'checked_in';
    const STATUS_CHECKED_OUT = 'checked_out';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_NO_SHOW = 'no_show';

    // Source constants
    const SOURCE_WEBSITE = 'website';
    const SOURCE_WALK_IN = 'walk_in';
    const SOURCE_PHONE = 'phone';
    const SOURCE_BOOKING_COM = 'booking_com';
    const SOURCE_EXPEDIA = 'expedia';
    const SOURCE_AGODA = 'agoda';
    const SOURCE_AIRBNB = 'airbnb';
    const SOURCE_OTHER_OTA = 'other_ota';
    const SOURCE_CORPORATE = 'corporate';
    const SOURCE_TRAVEL_AGENT = 'travel_agent';

    // Payment status constants
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PARTIAL = 'partial';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_REFUNDED = 'refunded';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = static::generateBookingReference();
            }
        });
    }

    /**
     * Generate unique booking reference.
     */
    public static function generateBookingReference(): string
    {
        $prefix = 'MKH';
        $date = now()->format('ymd');
        $random = strtoupper(\Str::random(4));

        return "{$prefix}{$date}{$random}";
    }

    /**
     * Get the guest.
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    /**
     * Get the room type.
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Get the assigned room.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get payments for this booking.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get invoices for this booking.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the creator.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the confirmer.
     */
    public function confirmedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    /**
     * Get the check-in handler.
     */
    public function checkedInByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    /**
     * Get the check-out handler.
     */
    public function checkedOutByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    /**
     * Calculate number of nights.
     */
    public function getNightsAttribute(): int
    {
        return max(1, $this->check_in->diffInDays($this->check_out));
    }

    /**
     * Get total amount including taxes.
     */
    public function getTotalAmountAttribute(): float
    {
        return $this->total_price + $this->tax_amount + $this->service_charge - $this->discount_amount;
    }

    /**
     * Get balance due.
     */
    public function getBalanceDueAttribute(): float
    {
        return $this->total_amount - $this->amount_paid;
    }

    /**
     * Calculate total price based on room rate and nights.
     */
    public function calculateTotalPrice(): float
    {
        $nights = $this->nights;
        $roomRate = $this->room_rate ?? $this->roomType->price;

        return $roomRate * $nights * $this->number_of_rooms;
    }

    /**
     * Calculate and set all pricing.
     */
    public function calculatePricing(): void
    {
        $basePrice = $this->calculateTotalPrice();

        // Get tax and service charge rates
        $taxRate = SystemPreference::get('tax_rate', 18) / 100;
        $serviceChargeRate = SystemPreference::get('service_charge_rate', 5) / 100;

        $this->total_price = $basePrice;
        $this->tax_amount = $basePrice * $taxRate;
        $this->service_charge = $basePrice * $serviceChargeRate;
    }

    /**
     * Confirm the booking.
     */
    public function confirm(): void
    {
        $this->update([
            'status' => self::STATUS_CONFIRMED,
            'confirmed_at' => now(),
            'confirmed_by' => auth()->id(),
        ]);

        ActivityLog::log('confirmed', "Booking {$this->booking_reference} confirmed", $this);
    }

    /**
     * Check in the guest.
     */
    public function checkIn(?int $roomId = null): void
    {
        $data = [
            'status' => self::STATUS_CHECKED_IN,
            'checked_in_at' => now(),
            'checked_in_by' => auth()->id(),
        ];

        if ($roomId) {
            $data['room_id'] = $roomId;
            Room::find($roomId)?->update(['status' => Room::STATUS_OCCUPIED]);
        }

        $this->update($data);

        ActivityLog::log('checked_in', "Guest checked in for booking {$this->booking_reference}", $this);
    }

    /**
     * Check out the guest.
     */
    public function checkOut(): void
    {
        // Release the room
        if ($this->room_id) {
            $this->room->markAsCleaning();
        }

        $this->update([
            'status' => self::STATUS_CHECKED_OUT,
            'checked_out_at' => now(),
            'checked_out_by' => auth()->id(),
        ]);

        // Update guest statistics
        $this->guest?->updateStayStatistics($this->total_amount);

        ActivityLog::log('checked_out', "Guest checked out for booking {$this->booking_reference}", $this);
    }

    /**
     * Cancel the booking.
     */
    public function cancel(?string $reason = null): void
    {
        // Release room if assigned
        if ($this->room_id && $this->status === self::STATUS_CHECKED_IN) {
            $this->room->markAsCleaning();
        }

        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        ActivityLog::log('cancelled', "Booking {$this->booking_reference} cancelled: {$reason}", $this);
    }

    /**
     * Mark as no-show.
     */
    public function markAsNoShow(): void
    {
        $this->update([
            'status' => self::STATUS_NO_SHOW,
        ]);

        ActivityLog::log('no_show', "Booking {$this->booking_reference} marked as no-show", $this);
    }

    /**
     * Record a payment.
     */
    public function recordPayment(float $amount): void
    {
        $this->increment('amount_paid', $amount);
        $this->updatePaymentStatus();
    }

    /**
     * Update payment status based on amount paid.
     */
    public function updatePaymentStatus(): void
    {
        $status = match(true) {
            $this->amount_paid <= 0 => self::PAYMENT_PENDING,
            $this->amount_paid >= $this->total_amount => self::PAYMENT_PAID,
            default => self::PAYMENT_PARTIAL,
        };

        $this->update(['payment_status' => $status]);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get pending bookings.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to get confirmed bookings.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    /**
     * Scope to get today's check-ins.
     */
    public function scopeTodayCheckIns($query)
    {
        return $query->whereIn('status', [self::STATUS_CONFIRMED, self::STATUS_PENDING])
            ->whereDate('check_in', today());
    }

    /**
     * Scope to get today's check-outs.
     */
    public function scopeTodayCheckOuts($query)
    {
        return $query->where('status', self::STATUS_CHECKED_IN)
            ->whereDate('check_out', today());
    }

    /**
     * Scope to get currently in-house guests.
     */
    public function scopeInHouse($query)
    {
        return $query->where('status', self::STATUS_CHECKED_IN);
    }

    /**
     * Scope to filter by source.
     */
    public function scopeSource($query, string $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->where(function ($q) use ($from, $to) {
            $q->whereBetween('check_in', [$from, $to])
                ->orWhereBetween('check_out', [$from, $to]);
        });
    }

    /**
     * Scope to search bookings.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('booking_reference', 'like', "%{$search}%")
                ->orWhere('guest_name', 'like', "%{$search}%")
                ->orWhere('guest_email', 'like', "%{$search}%")
                ->orWhere('guest_phone', 'like', "%{$search}%")
                ->orWhere('external_reference', 'like', "%{$search}%");
        });
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_CONFIRMED => 'info',
            self::STATUS_CHECKED_IN => 'success',
            self::STATUS_CHECKED_OUT => 'secondary',
            self::STATUS_CANCELLED => 'danger',
            self::STATUS_NO_SHOW => 'dark',
            default => 'secondary',
        };
    }

    /**
     * Get status label for UI.
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_CHECKED_IN => 'In House',
            self::STATUS_CHECKED_OUT => 'Departed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_NO_SHOW => 'No Show',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get source label for UI.
     */
    public function getSourceLabel(): string
    {
        return match($this->source) {
            self::SOURCE_WEBSITE => 'Website',
            self::SOURCE_WALK_IN => 'Walk-in',
            self::SOURCE_PHONE => 'Phone',
            self::SOURCE_BOOKING_COM => 'Booking.com',
            self::SOURCE_EXPEDIA => 'Expedia',
            self::SOURCE_AGODA => 'Agoda',
            self::SOURCE_AIRBNB => 'Airbnb',
            self::SOURCE_OTHER_OTA => 'Other OTA',
            self::SOURCE_CORPORATE => 'Corporate',
            self::SOURCE_TRAVEL_AGENT => 'Travel Agent',
            default => ucfirst($this->source),
        };
    }

    /**
     * Get payment status color.
     */
    public function getPaymentStatusColor(): string
    {
        return match($this->payment_status) {
            self::PAYMENT_PAID => 'success',
            self::PAYMENT_PARTIAL => 'warning',
            self::PAYMENT_PENDING => 'danger',
            self::PAYMENT_REFUNDED => 'info',
            default => 'secondary',
        };
    }

    /**
     * Check room availability for given dates.
     */
    public static function checkAvailability(int $roomTypeId, $checkIn, $checkOut, ?int $excludeBookingId = null): array
    {
        $roomType = RoomType::find($roomTypeId);

        if (!$roomType) {
            return ['available' => false, 'rooms_available' => 0, 'message' => 'Room type not found'];
        }

        $query = static::where('room_type_id', $roomTypeId)
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_CHECKED_IN])
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function ($qq) use ($checkIn, $checkOut) {
                        $qq->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                    });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        $bookedRooms = $query->sum('number_of_rooms');
        $availableRooms = $roomType->total_rooms - $bookedRooms;

        return [
            'available' => $availableRooms > 0,
            'rooms_available' => max(0, $availableRooms),
            'total_rooms' => $roomType->total_rooms,
            'booked_rooms' => $bookedRooms,
        ];
    }

    /**
     * Get all booking sources.
     */
    public static function getSources(): array
    {
        return [
            self::SOURCE_WEBSITE => 'Website',
            self::SOURCE_WALK_IN => 'Walk-in',
            self::SOURCE_PHONE => 'Phone',
            self::SOURCE_BOOKING_COM => 'Booking.com',
            self::SOURCE_EXPEDIA => 'Expedia',
            self::SOURCE_AGODA => 'Agoda',
            self::SOURCE_AIRBNB => 'Airbnb',
            self::SOURCE_OTHER_OTA => 'Other OTA',
            self::SOURCE_CORPORATE => 'Corporate',
            self::SOURCE_TRAVEL_AGENT => 'Travel Agent',
        ];
    }
}
