<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormationModule extends Model
{
    protected $fillable = [
        'product_id',
        'title',
        'description',
        'file_path',
        'external_url',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getFileNameAttribute(): ?string
    {
        return $this->file_path ? basename($this->file_path) : null;
    }

    public function getHasFileAttribute(): bool
    {
        return $this->file_path !== null || $this->files()->exists();
    }

    public function files(): HasMany
    {
        return $this->hasMany(FormationModuleFile::class)->orderBy('sort_order');
    }

    public function getHasExternalUrlAttribute(): bool
    {
        return $this->external_url !== null;
    }
}
