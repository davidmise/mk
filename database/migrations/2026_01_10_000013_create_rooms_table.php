<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained()->onDelete('cascade');
            $table->string('room_number', 20)->unique();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->integer('floor')->default(1);
            $table->string('view_type')->nullable(); // ocean, garden, pool, city, mountain, courtyard
            $table->integer('max_occupancy')->default(2);
            $table->decimal('size', 8, 2)->nullable(); // in sqm
            $table->string('bathroom_type')->default('private'); // private, shared
            $table->json('beds')->nullable(); // [{type: 'king', count: 1}, {type: 'single', count: 2}]
            $table->decimal('price_adjustment', 10, 2)->nullable(); // additional charge for this room
            $table->decimal('custom_rate', 10, 2)->nullable(); // override base price
            $table->decimal('extra_guest_fee', 10, 2)->nullable();
            $table->enum('status', ['available', 'occupied', 'reserved', 'maintenance', 'cleaning', 'out_of_service'])->default('available');
            $table->enum('cleanliness', ['clean', 'dirty', 'inspected'])->default('clean');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_smoking')->default(false);
            $table->boolean('is_accessible')->default(false);
            $table->boolean('pets_allowed')->default(false);
            $table->boolean('has_balcony')->default(false);
            $table->text('maintenance_notes')->nullable();
            $table->timestamp('last_cleaned_at')->nullable();
            $table->timestamp('last_inspected_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('cleanliness');
            $table->index('floor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
