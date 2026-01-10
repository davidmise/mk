<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemPreference extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    /**
     * Get a preference value.
     */
    public static function get(string $key, $default = null)
    {
        $preference = static::where('key', $key)->first();

        if (!$preference) {
            return $default;
        }

        return static::castValue($preference->value, $preference->type);
    }

    /**
     * Set a preference value.
     */
    public static function set(string $key, $value, string $type = 'string', string $group = 'general'): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
                'group' => $group,
            ]
        );
    }

    /**
     * Get all preferences for a group.
     */
    public static function getGroup(string $group): array
    {
        $preferences = static::where('group', $group)->get();

        return $preferences->mapWithKeys(function ($pref) {
            return [$pref->key => static::castValue($pref->value, $pref->type)];
        })->toArray();
    }

    /**
     * Cast value based on type.
     */
    protected static function castValue($value, string $type)
    {
        return match($type) {
            'integer', 'int' => (int) $value,
            'float', 'decimal' => (float) $value,
            'boolean', 'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'array', 'json' => json_decode($value, true) ?? [],
            default => $value,
        };
    }

    /**
     * Get all preferences as key-value array.
     */
    public static function allAsArray(): array
    {
        return static::query()->get()->mapWithKeys(function ($pref) {
            return [$pref->key => static::castValue($pref->value, $pref->type)];
        })->toArray();
    }

    /**
     * Default system preferences.
     */
    public static function defaults(): array
    {
        return [
            // General
            'currency' => ['value' => 'TZS', 'type' => 'string', 'group' => 'general'],
            'currency_symbol' => ['value' => 'TZS', 'type' => 'string', 'group' => 'general'],
            'timezone' => ['value' => 'Africa/Dar_es_Salaam', 'type' => 'string', 'group' => 'general'],
            'date_format' => ['value' => 'd M Y', 'type' => 'string', 'group' => 'general'],
            'time_format' => ['value' => 'H:i', 'type' => 'string', 'group' => 'general'],
            'language' => ['value' => 'en', 'type' => 'string', 'group' => 'general'],

            // Booking rules
            'min_stay_nights' => ['value' => '1', 'type' => 'integer', 'group' => 'booking'],
            'max_stay_nights' => ['value' => '30', 'type' => 'integer', 'group' => 'booking'],
            'check_in_time' => ['value' => '14:00', 'type' => 'string', 'group' => 'booking'],
            'check_out_time' => ['value' => '11:00', 'type' => 'string', 'group' => 'booking'],
            'advance_booking_days' => ['value' => '365', 'type' => 'integer', 'group' => 'booking'],
            'cancellation_window_hours' => ['value' => '24', 'type' => 'integer', 'group' => 'booking'],
            'allow_same_day_booking' => ['value' => 'true', 'type' => 'boolean', 'group' => 'booking'],
            'allow_overbooking' => ['value' => 'false', 'type' => 'boolean', 'group' => 'booking'],
            'overbooking_percentage' => ['value' => '0', 'type' => 'integer', 'group' => 'booking'],

            // Payment & Tax
            'tax_rate' => ['value' => '18', 'type' => 'decimal', 'group' => 'payment'],
            'service_charge_rate' => ['value' => '5', 'type' => 'decimal', 'group' => 'payment'],
            'deposit_percentage' => ['value' => '30', 'type' => 'decimal', 'group' => 'payment'],
            'require_deposit' => ['value' => 'false', 'type' => 'boolean', 'group' => 'payment'],
            'accepted_payment_methods' => ['value' => '["cash","card","mobile_money","bank_transfer"]', 'type' => 'array', 'group' => 'payment'],

            // Email
            'send_booking_confirmation' => ['value' => 'true', 'type' => 'boolean', 'group' => 'email'],
            'send_check_in_reminder' => ['value' => 'true', 'type' => 'boolean', 'group' => 'email'],
            'check_in_reminder_hours' => ['value' => '24', 'type' => 'integer', 'group' => 'email'],
            'send_checkout_receipt' => ['value' => 'true', 'type' => 'boolean', 'group' => 'email'],
        ];
    }

    /**
     * Seed default preferences.
     */
    public static function seedDefaults(): void
    {
        foreach (static::defaults() as $key => $config) {
            static::firstOrCreate(
                ['key' => $key],
                [
                    'value' => $config['value'],
                    'type' => $config['type'],
                    'group' => $config['group'],
                ]
            );
        }
    }
}
