<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'department',
        'position',
        'status',
        'failed_login_attempts',
        'locked_until',
        'last_login_at',
        'last_login_ip',
        'password_changed_at',
        'preferences',
        'is_admin',
        'force_password_change',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'locked_until' => 'datetime',
            'last_login_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'preferences' => 'array',
            'is_admin' => 'boolean',
            'force_password_change' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'two_factor_recovery_codes' => 'array',
        ];
    }

    const STATUS_ACTIVE = 'active';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_LOCKED = 'locked';

    const MAX_LOGIN_ATTEMPTS = 5;
    const LOCKOUT_DURATION = 30; // minutes

    /**
     * Get the user's primary role (first role from pivot table).
     * This is NOT a relationship - use roles() for that.
     */
    public function getPrimaryRole()
    {
        return $this->roles()->first();
    }

    /**
     * Get all roles for this user (via pivot table).
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')->withTimestamps();
    }

    /**
     * Get activity logs for this user.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get bookings created by this user.
     */
    public function bookingsCreated(): HasMany
    {
        return $this->hasMany(Booking::class, 'created_by');
    }

    /**
     * Get news articles authored by this user.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(NewsArticle::class, 'author_id');
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $role = $this->getPrimaryRole();
        return $role ? $role->hasPermission($permission) : false;
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $role = $this->getPrimaryRole();
        return $role ? $role->hasAnyPermission($permissions) : false;
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $role = $this->getPrimaryRole();
        return $role ? $role->hasAllPermissions($permissions) : false;
    }

    /**
     * Check if user is a Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        $role = $this->getPrimaryRole();
        return $role && $role->slug === 'super-admin';
    }

    /**
     * Check if user is an Admin or higher.
     */
    public function isAdmin(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $role = $this->getPrimaryRole();
        return $role && $role->slug === 'admin';
    }

    /**
     * Check if user account is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if user account is locked.
     */
    public function isLocked(): bool
    {
        if ($this->status === self::STATUS_LOCKED && $this->locked_until) {
            if ($this->locked_until->isPast()) {
                $this->unlock();
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Check if user account is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    /**
     * Lock the user account.
     */
    public function lock(int $minutes = null): void
    {
        $minutes = $minutes ?? self::LOCKOUT_DURATION;

        $this->update([
            'status' => self::STATUS_LOCKED,
            'locked_until' => now()->addMinutes($minutes),
        ]);
    }

    /**
     * Unlock the user account.
     */
    public function unlock(): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'locked_until' => null,
            'failed_login_attempts' => 0,
        ]);
    }

    /**
     * Suspend the user account.
     */
    public function suspend(): void
    {
        $this->update(['status' => self::STATUS_SUSPENDED]);
    }

    /**
     * Activate the user account.
     */
    public function activate(): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'locked_until' => null,
            'failed_login_attempts' => 0,
        ]);
    }

    /**
     * Record a failed login attempt.
     */
    public function recordFailedLogin(): void
    {
        $this->increment('failed_login_attempts');

        if ($this->failed_login_attempts >= self::MAX_LOGIN_ATTEMPTS) {
            $this->lock();
        }
    }

    /**
     * Record a successful login.
     */
    public function recordSuccessfulLogin(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);

        ActivityLog::logLogin($this);
    }

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random';
    }

    /**
     * Get status color.
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'success',
            self::STATUS_SUSPENDED => 'warning',
            self::STATUS_LOCKED => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Scope to get active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope to filter by role.
     */
    public function scopeWithRole($query, string $roleSlug)
    {
        return $query->whereHas('role', fn($q) => $q->where('slug', $roleSlug));
    }

    /**
     * Scope to search users.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }
}
