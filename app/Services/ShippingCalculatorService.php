<?php

namespace App\Services;

use App\Models\ShippingMethod;
use App\Models\Variant;

class ShippingCalculatorService
{
    public function calculate(array $items): array
    {
        $totalWeight = 0;
        $totalPrice = 0;

        foreach ($items as $item) {
            $variant = Variant::findOrFail($item['variant_id']);
            $totalWeight += ($variant->poids ?? 0) * $item['quantity'];
            $totalPrice += ($item['price'] ?? $variant->prix_vente_ttc) * $item['quantity'];
        }

        // Convertir poids de kg en grammes (le champ poids est en grammes dans la DB)
        $weightGrams = $totalWeight;

        return ShippingMethod::active()
            ->where('min_weight_grams', '<=', $weightGrams)
            ->where(fn ($q) => $q->whereNull('max_weight_grams')->orWhere('max_weight_grams', '>=', $weightGrams))
            ->get()
            ->map(fn ($method) => [
                'id' => $method->id,
                'name' => $method->name,
                'carrier' => $method->carrier,
                'price' => $totalPrice >= ($method->free_above ?? PHP_INT_MAX) ? 0 : $method->base_price,
                'estimated_days' => "{$method->estimated_days_min}-{$method->estimated_days_max} jours",
                'is_free' => $method->free_above && $totalPrice >= $method->free_above,
            ])
            ->toArray();
    }
}
