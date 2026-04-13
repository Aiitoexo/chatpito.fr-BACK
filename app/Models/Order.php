<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'status', 'tracking_number', 'carrier', 'shipped_at',
        'total', 'subtotal_ht', 'tax_amount', 'coupon_id', 'discount_amount',
        'shipping_name', 'shipping_email',
        'shipping_address', 'shipping_city', 'shipping_zip',
        'stripe_session_id', 'stripe_payment_intent_id'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'subtotal_ht' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipped_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}
