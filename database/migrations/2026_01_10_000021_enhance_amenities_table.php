<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('amenities', function (Blueprint $table) {
            $table->enum('category', ['room', 'property', 'service'])->default('property')->after('description');
            $table->string('image')->nullable()->after('icon');
        });

        // Create amenity-room_type pivot table
        Schema::create('amenity_room_type', function (Blueprint $table) {
            $table->foreignId('amenity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();
            $table->primary(['amenity_id', 'room_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amenity_room_type');

        Schema::table('amenities', function (Blueprint $table) {
            $table->dropColumn(['category', 'image']);
        });
    }
};
