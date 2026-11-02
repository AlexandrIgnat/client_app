<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'name', 'notes'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Сортировка: сначала «купить», затем «куплено» */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class)
            ->orderBy('is_purchased', 'asc')
            ->orderBy('created_at', 'asc');
    }

    public function upcomingItems(): HasMany
    {
        return $this->items()->where('is_purchased', false);
    }

    public function purchasedItems(): HasMany
    {
        return $this->items()->where('is_purchased', true);
    }

    /** Итого предстоящих покупок (учитывая fallback на default_price) */
    public function getUpcomingTotalAttribute(): float
    {
        return $this->items
            ->where('is_purchased', false)
            ->sum(fn(OrderItem $i) => $i->effective_total);
    }

    /** Итого купленного */
    public function getPurchasedTotalAttribute(): float
    {
        return $this->items
            ->where('is_purchased', true)
            ->sum(fn(OrderItem $i) => $i->effective_total);
    }
}
