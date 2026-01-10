<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    protected $fillable = [
        'title',
        'image',
        'thumbnail',
        'alt_text',
        'category',
        'is_slider',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_slider' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSlider($query)
    {
        return $query->where('is_slider', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
