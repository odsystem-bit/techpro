<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackFile extends Model
{
    protected $fillable = [
        'pack_id',
        'name',
        'file_path',
        'file_type',
        'file_size',
        'sort_order',
    ];

    public function pack(): BelongsTo
    {
        return $this->belongsTo(Pack::class);
    }
}
