<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Rename name to guest_name if exists
            if (Schema::hasColumn('bookings', 'name')) {
                $table->renameColumn('name', 'guest_name');
            }

            // Rename email to guest_email if exists
            if (Schema::hasColumn('bookings', 'email')) {
                $table->renameColumn('email', 'guest_email');
            }

            // Rename phone to guest_phone if exists
            if (Schema::hasColumn('bookings', 'phone')) {
                $table->renameColumn('phone', 'guest_phone');
            }

            // Add booking_reference if not exists
            if (!Schema::hasColumn('bookings', 'booking_reference')) {
                $table->string('booking_reference')->unique()->nullable()->after('id');
            }

            // Add other fields that might be missing
            if (!Schema::hasColumn('bookings', 'guest_id')) {
                $table->foreignId('guest_id')->nullable()->after('booking_reference');
            }

            if (!Schema::hasColumn('bookings', 'room_id')) {
                $table->foreignId('room_id')->nullable()->after('room_type_id');
            }

            if (!Schema::hasColumn('bookings', 'adults')) {
                $table->integer('adults')->default(1)->after('number_of_rooms');
            }

            if (!Schema::hasColumn('bookings', 'children')) {
                $table->integer('children')->default(0)->after('adults');
            }

            if (!Schema::hasColumn('bookings', 'source')) {
                $table->string('source')->default('website')->after('children');
            }

            if (!Schema::hasColumn('bookings', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('total_price');
            }

            if (!Schema::hasColumn('bookings', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'guest_name')) {
                $table->renameColumn('guest_name', 'name');
            }
            if (Schema::hasColumn('bookings', 'guest_email')) {
                $table->renameColumn('guest_email', 'email');
            }
            if (Schema::hasColumn('bookings', 'guest_phone')) {
                $table->renameColumn('guest_phone', 'phone');
            }
            $table->dropColumn(['booking_reference', 'guest_id', 'room_id', 'adults', 'children', 'source', 'payment_status']);
            $table->dropSoftDeletes();
        });
    }
};
