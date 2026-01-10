<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seasonal_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "Peak Season", "Holiday Special"
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('price', 12, 2);
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->integer('priority')->default(0); // Higher priority overrides
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seasonal_prices');
    }
};
