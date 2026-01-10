<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop old columns first
            $table->dropColumn(['name', 'email', 'phone']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            // Add new columns
            $table->string('booking_reference')->unique()->after('id');
            $table->foreignId('guest_id')->nullable()->after('booking_reference')->constrained()->nullOnDelete();

            // Guest info for quick reference (denormalized)
            $table->string('guest_name')->after('guest_id');
            $table->string('guest_email')->nullable()->after('guest_name');
            $table->string('guest_phone')->nullable()->after('guest_email');

            $table->foreignId('room_id')->nullable()->after('room_type_id')->constrained()->nullOnDelete();
            $table->integer('adults')->default(1)->after('number_of_rooms');
            $table->integer('children')->default(0)->after('adults');

            // Booking source
            $table->enum('source', ['website', 'walk_in', 'phone', 'booking_com', 'expedia', 'agoda', 'airbnb', 'other_ota', 'corporate', 'travel_agent'])->default('website')->after('children');
            $table->string('external_reference')->nullable()->after('source'); // OTA reference

            // Pricing
            $table->decimal('room_rate', 12, 2)->nullable()->after('total_price');
            $table->decimal('tax_amount', 12, 2)->default(0)->after('room_rate');
            $table->decimal('service_charge', 12, 2)->default(0)->after('tax_amount');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('service_charge');
            $table->string('discount_reason')->nullable()->after('discount_amount');

            // Payment
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'refunded'])->default('pending')->after('discount_reason');
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'mobile_money', 'ota_prepaid', 'corporate_billing'])->nullable()->after('payment_status');
            $table->decimal('amount_paid', 12, 2)->default(0)->after('payment_method');

            // Status enhancement
            $table->dropColumn('status');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('status', ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show'])->default('pending')->after('amount_paid');

            // Timestamps
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // Staff tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('checked_out_by')->nullable()->constrained('users')->nullOnDelete();

            $table->text('cancellation_reason')->nullable();
            $table->text('special_requests')->nullable();

            $table->softDeletes();

            $table->index('booking_reference');
            $table->index('status');
            $table->index('source');
            $table->index(['check_in', 'check_out']);
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['guest_id']);
            $table->dropForeign(['room_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['confirmed_by']);
            $table->dropForeign(['checked_in_by']);
            $table->dropForeign(['checked_out_by']);

            $table->dropColumn([
                'booking_reference', 'guest_id', 'guest_name', 'guest_email', 'guest_phone',
                'room_id', 'adults', 'children', 'source', 'external_reference',
                'room_rate', 'tax_amount', 'service_charge', 'discount_amount', 'discount_reason',
                'payment_status', 'payment_method', 'amount_paid', 'status',
                'confirmed_at', 'checked_in_at', 'checked_out_at', 'cancelled_at',
                'created_by', 'confirmed_by', 'checked_in_by', 'checked_out_by',
                'cancellation_reason', 'special_requests'
            ]);

            $table->dropSoftDeletes();

            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('status')->default('pending');
        });
    }
};
