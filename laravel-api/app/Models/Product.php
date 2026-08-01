<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'discount_price',
        'currency',
        'product_type',
        'stock',
        'is_active',
        'is_featured',
        'category_id',
        'image',
        'file_path',
        'preview_url',
        'features',
        'sales_count',
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_active'      => 'boolean',
        'is_featured'    => 'boolean',
        'features'       => 'array',
        'sales_count'    => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $product) {
            if (empty($product->slug)) {
                $baseSlug = Str::slug($product->name);
                $slug = $baseSlug;
                $counter = 1;

                while (self::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }

                $product->slug = $slug;
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orders()
    {
        return $this->morphMany(Order::class, 'orderable');
    }

    public function modules()
    {
        return $this->hasMany(FormationModule::class)->orderBy('sort_order');
    }

    public function galleryImages()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function isFormation(): bool
    {
        return $this->product_type === 'formation';
    }

    public function getEffectivePriceAttribute(): string
    {
        return $this->discount_price ?? $this->price;
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_price !== null && $this->discount_price < $this->price;
    }

    public function getDiscountPercentAttribute(): int
    {
        if (! $this->has_discount) {
            return 0;
        }
        return (int) round((1 - $this->discount_price / $this->price) * 100);
    }

    public function isInStock(): bool
    {
        return $this->stock === -1 || $this->stock > 0;
    }
}
