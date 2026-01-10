<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get the user who performed this action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model that was acted upon.
     */
    public function subject()
    {
        if ($this->subject_type && $this->subject_id) {
            return $this->subject_type::find($this->subject_id);
        }
        return null;
    }

    /**
     * Log an activity.
     */
    public static function log(
        string $type,
        string $action,
        ?string $description = null,
        ?Model $model = null,
        ?array $properties = null
    ): self {
        return static::create([
            'user_id' => auth()->id(),
            'type' => $type,
            'action' => $action,
            'subject_type' => $model ? get_class($model) : null,
            'subject_id' => $model?->id,
            'description' => $description ?? ucfirst($action) . ' ' . ($model ? class_basename($model) : ''),
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log a login activity.
     */
    public static function logLogin(User $user): self
    {
        return static::create([
            'user_id' => $user->id,
            'type' => 'auth',
            'action' => 'login',
            'description' => 'User logged in',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log a logout activity.
     */
    public static function logLogout(User $user): self
    {
        return static::create([
            'user_id' => $user->id,
            'type' => 'auth',
            'action' => 'logout',
            'description' => 'User logged out',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log a failed login attempt.
     */
    public static function logFailedLogin(string $email): self
    {
        return static::create([
            'user_id' => null,
            'type' => 'auth',
            'action' => 'failed_login',
            'description' => "Failed login attempt for: {$email}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Scope to filter by action.
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by user.
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by model.
     */
    public function scopeForModel($query, string $modelType, ?int $modelId = null)
    {
        $query->where('model_type', $modelType);

        if ($modelId) {
            $query->where('model_id', $modelId);
        }

        return $query;
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Get a human-readable description of the action.
     */
    public function getActionLabel(): string
    {
        return match($this->action) {
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'restored' => 'Restored',
            'login' => 'Logged In',
            'logout' => 'Logged Out',
            'failed_login' => 'Failed Login',
            'password_reset' => 'Password Reset',
            'status_changed' => 'Status Changed',
            'checked_in' => 'Checked In Guest',
            'checked_out' => 'Checked Out Guest',
            'payment_received' => 'Payment Received',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }
}
