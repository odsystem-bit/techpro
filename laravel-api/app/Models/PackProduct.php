<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'pack_id',
        'product_id',
    ];

    public function pack()
    {
        return $this->belongsTo(Pack::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
