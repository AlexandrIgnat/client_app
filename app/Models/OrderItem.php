<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit',
        'price',
        'is_purchased',
        'purchased_at',
        'comment',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'price' => 'decimal:2',
        'is_purchased' => 'boolean',
        'purchased_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** Цена: фактическая → default_price → 0 */
    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->price ?? $this->product?->default_price ?? 0);
    }

    public function getEffectiveTotalAttribute(): float
    {
        return $this->effective_price * (float) $this->quantity;
    }
}
