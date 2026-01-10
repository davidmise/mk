<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Section extends Model
{
    protected $fillable = [
        'page_id',
        'key',
        'title',
        'content',
        'image',
        'image_alt',
        'extra_data',
        'order',
        'is_active',
    ];

    protected $casts = [
        'extra_data' => 'array',
        'is_active' => 'boolean',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getExtraAttribute(string $key, $default = null)
    {
        return data_get($this->extra_data, $key, $default);
    }
}
