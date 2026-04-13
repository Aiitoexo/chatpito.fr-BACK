<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    public function run(): void
    {
        ShippingMethod::create([
            'name' => 'Colissimo Standard',
            'carrier' => 'colissimo',
            'base_price' => 4.99,
            'free_above' => 30.00,
            'min_weight_grams' => 0,
            'max_weight_grams' => 30000,
            'estimated_days_min' => 2,
            'estimated_days_max' => 4,
        ]);

        ShippingMethod::create([
            'name' => 'Mondial Relay',
            'carrier' => 'mondial_relay',
            'base_price' => 3.99,
            'free_above' => 30.00,
            'min_weight_grams' => 0,
            'max_weight_grams' => 30000,
            'estimated_days_min' => 3,
            'estimated_days_max' => 5,
        ]);

        ShippingMethod::create([
            'name' => 'Chronopost Express',
            'carrier' => 'chronopost',
            'base_price' => 9.99,
            'free_above' => null,
            'min_weight_grams' => 0,
            'max_weight_grams' => 30000,
            'estimated_days_min' => 1,
            'estimated_days_max' => 2,
        ]);
    }
}
