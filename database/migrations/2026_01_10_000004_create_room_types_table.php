<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 12, 2);
            $table->string('price_label')->default('/night');
            $table->text('description')->nullable();
            $table->text('secondary_description')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->json('amenities')->nullable();
            $table->integer('total_rooms')->default(1);
            $table->integer('order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->json('extra_data')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
