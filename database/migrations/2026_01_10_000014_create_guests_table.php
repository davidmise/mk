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
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('nationality')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            $table->string('id_type')->nullable(); // passport, driver_license, national_id
            $table->string('id_number')->nullable();
            $table->date('id_expiry')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('is_vip')->default(false);
            $table->string('loyalty_tier')->nullable(); // bronze, silver, gold, platinum
            $table->integer('loyalty_points')->default(0);
            $table->text('preferences')->nullable();
            $table->text('notes')->nullable();
            $table->text('dietary_requirements')->nullable();
            $table->boolean('email_marketing')->default(false);
            $table->boolean('sms_marketing')->default(false);
            $table->timestamp('last_stay_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['first_name', 'last_name']);
            $table->index('is_vip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
