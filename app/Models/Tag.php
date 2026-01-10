<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    /**
     * Get all news articles with this tag.
     */
    public function news(): MorphToMany
    {
        return $this->morphedByMany(News::class, 'taggable');
    }

    /**
     * Get all pages with this tag.
     */
    public function pages(): MorphToMany
    {
        return $this->morphedByMany(Page::class, 'taggable');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get usage count across all taggables.
     */
    public function getUsageCountAttribute(): int
    {
        return $this->news()->count() + $this->pages()->count();
    }

    /**
     * Find or create a tag by name.
     */
    public static function findOrCreateByName(string $name): self
    {
        $slug = Str::slug($name);

        return static::firstOrCreate(
            ['slug' => $slug],
            ['name' => trim($name)]
        );
    }

    /**
     * Sync tags from a comma-separated string.
     */
    public static function syncFromString(Model $model, ?string $tagString): void
    {
        if (empty($tagString)) {
            $model->tags()->detach();
            return;
        }

        $tagNames = array_map('trim', explode(',', $tagString));
        $tagIds = [];

        foreach ($tagNames as $name) {
            if (!empty($name)) {
                $tag = static::findOrCreateByName($name);
                $tagIds[] = $tag->id;
            }
        }

        $model->tags()->sync($tagIds);
    }

    /**
     * Scope for popular tags.
     */
    public function scopePopular($query, $limit = 20)
    {
        return $query->withCount('news')
            ->orderByDesc('news_count')
            ->limit($limit);
    }

    /**
     * Scope for searching.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }
}
