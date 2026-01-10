<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['booking', 'guest', 'processedBy']);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('reference', 'like', "%{$request->search}%")
                  ->orWhereHas('booking', function($bq) use ($request) {
                      $bq->where('booking_reference', 'like', "%{$request->search}%");
                  });
            });
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->latest()->paginate(20)->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|string|in:cash,credit_card,debit_card,bank_transfer,mobile_money,paypal',
            'reference' => 'nullable|string|max:255',
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'guest_id' => $booking->guest_id,
            'amount' => $request->amount,
            'method' => $request->method,
            'payment_reference' => $request->reference ?? 'PAY-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'type' => $request->amount >= ($booking->total_amount ?? $booking->total_price ?? 0) - ($booking->paid_amount ?? $booking->amount_paid ?? 0) ? Payment::TYPE_FULL_PAYMENT : Payment::TYPE_PARTIAL,
            'status' => Payment::STATUS_COMPLETED,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        // Update booking paid amount
        $currentPaid = $booking->paid_amount ?? $booking->amount_paid ?? 0;
        $booking->update([
            'paid_amount' => $currentPaid + $request->amount,
            'amount_paid' => $currentPaid + $request->amount,
        ]);

        ActivityLog::log('created', "Recorded payment of TZS " . number_format($request->amount, 0) . " for booking {$booking->booking_reference}", $payment);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment of TZS ' . number_format($request->amount, 0) . ' recorded successfully!',
                'payment' => $payment
            ]);
        }

        return back()->with('success', 'Payment recorded successfully.');
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        $payment->load(['booking', 'guest', 'processedBy']);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Update the specified payment status.
     */
    public function updateStatus(Request $request, Payment $payment)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,failed,refunded',
        ]);

        $oldStatus = $payment->status;
        $payment->update(['status' => $request->status]);

        ActivityLog::log('updated', "Changed payment status from {$oldStatus} to {$request->status}", $payment);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment status updated successfully!'
            ]);
        }

        return back()->with('success', 'Payment status updated.');
    }

    /**
     * Process a refund.
     */
    public function refund(Request $request, Payment $payment)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $payment->amount,
            'reason' => 'required|string|max:500',
        ]);

        $payment->update([
            'status' => 'refunded',
            'refund_amount' => $request->amount,
            'refund_reason' => $request->reason,
            'refunded_at' => now(),
            'refunded_by' => auth()->id(),
        ]);

        // Update booking paid amount
        if ($payment->booking) {
            $currentPaid = $payment->booking->paid_amount ?? $payment->booking->amount_paid ?? 0;
            $payment->booking->update([
                'paid_amount' => max(0, $currentPaid - $request->amount),
                'amount_paid' => max(0, $currentPaid - $request->amount),
            ]);
        }

        ActivityLog::log('refunded', "Refunded TZS " . number_format($request->amount, 0) . " - Reason: {$request->reason}", $payment);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully!'
            ]);
        }

        return back()->with('success', 'Refund processed successfully.');
    }
}
