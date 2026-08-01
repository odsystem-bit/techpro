<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    protected $fillable = [
        'title', 'subtitle', 'btn_label', 'btn_url',
        'image', 'overlay_color', 'is_active', 'sort_order',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public static function active()
    {
        return static::where('is_active', true)->orderBy('sort_order')->get();
    }
}
