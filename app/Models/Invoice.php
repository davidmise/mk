<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'booking_id',
        'guest_id',
        'billing_name',
        'billing_email',
        'billing_address',
        'billing_phone',
        'billing_company',
        'billing_tax_id',
        'subtotal',
        'tax_amount',
        'service_charge',
        'discount_amount',
        'total_amount',
        'amount_paid',
        'balance_due',
        'currency',
        'status',
        'issue_date',
        'due_date',
        'notes',
        'terms',
        'created_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'issue_date' => 'date',
        'due_date' => 'date',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_SENT = 'sent';
    const STATUS_PAID = 'paid';
    const STATUS_PARTIAL = 'partial';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateInvoiceNumber();
            }
            $invoice->calculateTotals();
        });

        static::updating(function ($invoice) {
            $invoice->calculateTotals();
        });
    }

    /**
     * Generate unique invoice number.
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $year = now()->format('Y');
        $month = now()->format('m');

        $lastInvoice = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderByDesc('id')
            ->first();

        $sequence = $lastInvoice
            ? (int)substr($lastInvoice->invoice_number, -4) + 1
            : 1;

        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $sequence);
    }

    /**
     * Calculate totals.
     */
    public function calculateTotals(): void
    {
        $this->balance_due = $this->total_amount - $this->amount_paid;
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
     * Get the creator.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all line items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Add a line item.
     */
    public function addItem(string $description, int $quantity, float $unitPrice): InvoiceItem
    {
        $item = $this->items()->create([
            'description' => $description,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total' => $quantity * $unitPrice,
        ]);

        $this->recalculateSubtotal();

        return $item;
    }

    /**
     * Recalculate subtotal from items.
     */
    public function recalculateSubtotal(): void
    {
        $subtotal = $this->items()->sum('total');
        $this->update(['subtotal' => $subtotal]);
    }

    /**
     * Record a payment.
     */
    public function recordPayment(float $amount): void
    {
        $this->increment('amount_paid', $amount);
        $this->calculateTotals();
        $this->save();
        $this->updateStatus();
    }

    /**
     * Update status based on payment.
     */
    public function updateStatus(): void
    {
        if ($this->balance_due <= 0) {
            $this->update(['status' => self::STATUS_PAID]);
        } elseif ($this->amount_paid > 0) {
            $this->update(['status' => self::STATUS_PARTIAL]);
        } elseif ($this->due_date < now()) {
            $this->update(['status' => self::STATUS_OVERDUE]);
        }
    }

    /**
     * Mark as sent.
     */
    public function markAsSent(): void
    {
        $this->update(['status' => self::STATUS_SENT]);
    }

    /**
     * Cancel invoice.
     */
    public function cancel(): void
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }

    /**
     * Scope by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for overdue invoices.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereNotIn('status', [self::STATUS_PAID, self::STATUS_CANCELLED]);
    }

    /**
     * Get status color.
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            self::STATUS_PAID => 'success',
            self::STATUS_PARTIAL => 'info',
            self::STATUS_SENT => 'primary',
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_OVERDUE => 'danger',
            self::STATUS_CANCELLED => 'dark',
            default => 'secondary',
        };
    }

    /**
     * Create invoice from booking.
     */
    public static function createFromBooking(Booking $booking): self
    {
        $invoice = static::create([
            'booking_id' => $booking->id,
            'guest_id' => $booking->guest_id,
            'billing_name' => $booking->guest_name,
            'billing_email' => $booking->guest_email,
            'billing_phone' => $booking->guest_phone,
            'subtotal' => $booking->total_price,
            'tax_amount' => $booking->tax_amount,
            'service_charge' => $booking->service_charge,
            'discount_amount' => $booking->discount_amount,
            'total_amount' => $booking->total_price + $booking->tax_amount + $booking->service_charge - $booking->discount_amount,
            'amount_paid' => $booking->amount_paid,
            'issue_date' => now(),
            'due_date' => $booking->check_out,
            'created_by' => auth()->id(),
        ]);

        // Add room charge as line item
        $nights = $booking->check_in->diffInDays($booking->check_out);
        $invoice->addItem(
            "{$booking->roomType->name} - {$nights} night(s)",
            $booking->number_of_rooms,
            $booking->room_rate * $nights
        );

        return $invoice;
    }
}
