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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->string('department', 100)->nullable()->after('avatar');
            $table->string('position', 100)->nullable()->after('department');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('position');
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            $table->timestamp('password_changed_at')->nullable()->after('last_login_ip');
            $table->json('preferences')->nullable()->after('password_changed_at');
            $table->boolean('is_admin')->default(false)->after('preferences');
            $table->boolean('force_password_change')->default(false)->after('is_admin');
            $table->integer('failed_login_attempts')->default(0)->after('force_password_change');
            $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
            $table->softDeletes();

            $table->index('status');
            $table->index('is_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'phone',
                'avatar',
                'department',
                'position',
                'status',
                'last_login_at',
                'last_login_ip',
                'password_changed_at',
                'preferences',
                'is_admin',
                'force_password_change',
                'failed_login_attempts',
                'locked_until',
            ]);
        });
    }
};
