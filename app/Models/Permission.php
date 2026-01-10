<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'group',
        'description',
    ];

    /**
     * Get all roles that have this permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    /**
     * Alias for module for backwards compatibility.
     */
    public function getModuleAttribute(): ?string
    {
        return $this->group;
    }

    /**
     * Scope to filter by group/module.
     */
    public function scopeModule($query, string $module)
    {
        return $query->where('group', $module);
    }

    /**
     * Scope to filter by group.
     */
    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Get permissions grouped by group.
     */
    public static function grouped(): array
    {
        return static::all()->groupBy('group')->toArray();
    }

    /**
     * Create a permission if it doesn't exist.
     */
    public static function createIfNotExists(string $slug, string $name, string $group, ?string $description = null): self
    {
        return static::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'group' => $group,
                'description' => $description,
            ]
        );
    }
}
