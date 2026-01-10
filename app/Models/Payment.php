<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'payment_reference',
        'booking_id',
        'guest_id',
        'amount',
        'method',
        'type',
        'status',
        'transaction_id',
        'notes',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    const METHOD_CASH = 'cash';
    const METHOD_CARD = 'card';
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_MOBILE_MONEY = 'mobile_money';
    const METHOD_OTA_PREPAID = 'ota_prepaid';
    const METHOD_CORPORATE_BILLING = 'corporate_billing';

    const TYPE_DEPOSIT = 'deposit';
    const TYPE_FULL_PAYMENT = 'full_payment';
    const TYPE_PARTIAL = 'partial';
    const TYPE_REFUND = 'refund';
    const TYPE_ADDITIONAL_CHARGE = 'additional_charge';

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_reference)) {
                $payment->payment_reference = static::generateReference();
            }
        });
    }

    /**
     * Generate unique payment reference.
     */
    public static function generateReference(): string
    {
        $prefix = 'PAY';
        $date = now()->format('Ymd');
        $random = strtoupper(\Str::random(6));

        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Get the booking.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the guest.
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    /**
     * Get the staff who processed this payment.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Mark payment as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'processed_at' => now(),
            'processed_by' => auth()->id(),
        ]);

        // Update booking payment
        $this->booking?->recordPayment($this->amount);
    }

    /**
     * Mark payment as failed.
     */
    public function markAsFailed(): void
    {
        $this->update(['status' => self::STATUS_FAILED]);
    }

    /**
     * Process a refund.
     */
    public function processRefund(?string $notes = null): self
    {
        $this->update(['status' => self::STATUS_REFUNDED]);

        return static::create([
            'booking_id' => $this->booking_id,
            'guest_id' => $this->guest_id,
            'amount' => -$this->amount,
            'method' => $this->method,
            'type' => self::TYPE_REFUND,
            'status' => self::STATUS_COMPLETED,
            'notes' => $notes ?? "Refund for payment {$this->payment_reference}",
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get completed payments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope to filter by method.
     */
    public function scopeMethod($query, string $method)
    {
        return $query->where('method', $method);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Get method label.
     */
    public function getMethodLabel(): string
    {
        return match($this->method) {
            self::METHOD_CASH => 'Cash',
            self::METHOD_CARD => 'Credit/Debit Card',
            self::METHOD_BANK_TRANSFER => 'Bank Transfer',
            self::METHOD_MOBILE_MONEY => 'Mobile Money',
            self::METHOD_OTA_PREPAID => 'OTA Prepaid',
            self::METHOD_CORPORATE_BILLING => 'Corporate Billing',
            default => ucfirst($this->method),
        };
    }

    /**
     * Get status color.
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            self::STATUS_COMPLETED => 'success',
            self::STATUS_PENDING => 'warning',
            self::STATUS_FAILED => 'danger',
            self::STATUS_REFUNDED => 'info',
            default => 'secondary',
        };
    }
}
