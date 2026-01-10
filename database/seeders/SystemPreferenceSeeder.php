<?php

namespace Database\Seeders;

use App\Models\SystemPreference;
use Illuminate\Database\Seeder;

class SystemPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $preferences = [
            // General Settings
            ['key' => 'currency', 'value' => 'USD', 'type' => 'string', 'group' => 'general'],
            ['key' => 'currency_symbol', 'value' => '$', 'type' => 'string', 'group' => 'general'],
            ['key' => 'timezone', 'value' => 'UTC', 'type' => 'string', 'group' => 'general'],
            ['key' => 'date_format', 'value' => 'Y-m-d', 'type' => 'string', 'group' => 'general'],
            ['key' => 'time_format', 'value' => 'H:i', 'type' => 'string', 'group' => 'general'],
            ['key' => 'language', 'value' => 'en', 'type' => 'string', 'group' => 'general'],

            // Booking Settings
            ['key' => 'check_in_time', 'value' => '14:00', 'type' => 'string', 'group' => 'booking'],
            ['key' => 'check_out_time', 'value' => '11:00', 'type' => 'string', 'group' => 'booking'],
            ['key' => 'min_stay_nights', 'value' => '1', 'type' => 'integer', 'group' => 'booking'],
            ['key' => 'max_stay_nights', 'value' => '30', 'type' => 'integer', 'group' => 'booking'],
            ['key' => 'max_guests_per_room', 'value' => '4', 'type' => 'integer', 'group' => 'booking'],
            ['key' => 'allow_same_day_booking', 'value' => '1', 'type' => 'boolean', 'group' => 'booking'],
            ['key' => 'allow_overbooking', 'value' => '0', 'type' => 'boolean', 'group' => 'booking'],
            ['key' => 'cancellation_window_hours', 'value' => '24', 'type' => 'integer', 'group' => 'booking'],
            ['key' => 'advance_booking_days', 'value' => '365', 'type' => 'integer', 'group' => 'booking'],

            // Payment Settings
            ['key' => 'tax_rate', 'value' => '10', 'type' => 'decimal', 'group' => 'payment'],
            ['key' => 'service_charge_rate', 'value' => '5', 'type' => 'decimal', 'group' => 'payment'],
            ['key' => 'deposit_percentage', 'value' => '20', 'type' => 'decimal', 'group' => 'payment'],
            ['key' => 'require_deposit', 'value' => '1', 'type' => 'boolean', 'group' => 'payment'],
            ['key' => 'accept_cash', 'value' => '1', 'type' => 'boolean', 'group' => 'payment'],
            ['key' => 'accept_card', 'value' => '1', 'type' => 'boolean', 'group' => 'payment'],
            ['key' => 'accept_bank_transfer', 'value' => '1', 'type' => 'boolean', 'group' => 'payment'],

            // Email Settings
            ['key' => 'send_booking_confirmation', 'value' => '1', 'type' => 'boolean', 'group' => 'email'],
            ['key' => 'send_check_in_reminder', 'value' => '1', 'type' => 'boolean', 'group' => 'email'],
            ['key' => 'check_in_reminder_hours', 'value' => '24', 'type' => 'integer', 'group' => 'email'],
            ['key' => 'send_check_out_reminder', 'value' => '1', 'type' => 'boolean', 'group' => 'email'],
            ['key' => 'send_invoice_email', 'value' => '1', 'type' => 'boolean', 'group' => 'email'],

            // Notification Settings
            ['key' => 'notify_new_booking', 'value' => '1', 'type' => 'boolean', 'group' => 'notifications'],
            ['key' => 'notify_cancellation', 'value' => '1', 'type' => 'boolean', 'group' => 'notifications'],
            ['key' => 'notify_payment', 'value' => '1', 'type' => 'boolean', 'group' => 'notifications'],
            ['key' => 'notification_email', 'value' => '', 'type' => 'string', 'group' => 'notifications'],
        ];

        foreach ($preferences as $pref) {
            SystemPreference::updateOrCreate(
                ['key' => $pref['key']],
                $pref
            );
        }

        $this->command->info('System preferences seeded successfully!');
    }
}
