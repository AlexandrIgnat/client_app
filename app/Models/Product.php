<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'name', 'brand', 'unit', 'default_price', 'comment'];

    protected $casts = ['default_price' => 'decimal:2'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** История цен = купленные позиции */
    public function priceHistory(): HasMany
    {
        return $this->hasMany(OrderItem::class)
            ->whereNotNull('price')
            ->where('is_purchased', true)
            ->orderByDesc('purchased_at');
    }
}
