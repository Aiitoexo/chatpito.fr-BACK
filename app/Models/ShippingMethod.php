<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    protected $fillable = [
        'name', 'carrier', 'base_price', 'free_above',
        'min_weight_grams', 'max_weight_grams',
        'estimated_days_min', 'estimated_days_max', 'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'free_above' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
