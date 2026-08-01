<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'orderable_id',
        'orderable_type',
        'customer_email',
        'customer_name',
        'quantity',
        'unit_price',
        'total_amount',
        'currency',
        'payment_gateway',
        'payment_status',
        'moneroo_transaction_id',
        'download_token',
        'download_count',
        'download_limit',
        'download_expires_at',
        'downloaded_at',
        'pixel_purchase_sent',
        'metadata',
    ];

    protected $casts = [
        'metadata'            => 'array',
        'unit_price'          => 'decimal:2',
        'total_amount'        => 'decimal:2',
        'download_expires_at' => 'datetime',
        'downloaded_at'       => 'datetime',
        'pixel_purchase_sent' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $order) {
            if (empty($order->order_number)) {
                $order->order_number = 'TPF-' . strtoupper(Str::random(8));
            }
        });
    }

    public function orderable()
    {
        return $this->morphTo();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'orderable_id')->where('orderable_type', Product::class);
    }

    public function pack()
    {
        return $this->belongsTo(Pack::class, 'orderable_id')->where('orderable_type', Pack::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function canDownload(): bool
    {
        return $this->isPaid()
            && $this->download_count < $this->download_limit
            && ($this->download_expires_at === null || $this->download_expires_at->isFuture());
    }

    public function getDownloadUrlAttribute(): ?string
    {
        if (! $this->download_token) {
            return null;
        }
        return route('download', ['token' => $this->download_token]);
    }

    public function getOdibotUrlAttribute(): ?string
    {
        if (! $this->download_token) {
            return null;
        }
        return route('odibot.download', ['token' => $this->download_token]);
    }

    public function isEbookOrder(): bool
    {
        return $this->product && $this->product->product_type === 'ebook';
    }
}
