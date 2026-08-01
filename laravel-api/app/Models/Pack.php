<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pack extends Model
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
        'is_active',
        'is_featured',
        'category_id',
        'image',
        'sales_count',
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_active'      => 'boolean',
        'is_featured'    => 'boolean',
        'sales_count'    => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $pack) {
            if (empty($pack->slug)) {
                $pack->slug = Str::slug($pack->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'pack_products');
    }

    public function files()
    {
        return $this->hasMany(PackFile::class)->orderBy('sort_order');
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

    public function getTotalProductsPriceAttribute(): float
    {
        return $this->products->sum('price');
    }

    public function getSavingsAttribute(): float
    {
        return $this->getTotalProductsPriceAttribute() - $this->price;
    }
}
