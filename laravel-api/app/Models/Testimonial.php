<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'author_name', 'author_title', 'author_avatar',
        'screenshot', 'content', 'rating', 'is_active', 'sort_order',
    ];

    protected $casts = ['is_active' => 'boolean', 'rating' => 'integer'];

    public static function active()
    {
        return static::where('is_active', true)->orderBy('sort_order')->get();
    }
}
