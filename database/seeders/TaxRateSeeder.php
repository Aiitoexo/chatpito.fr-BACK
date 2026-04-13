<?php

namespace Database\Seeders;

use App\Models\TaxRate;
use Illuminate\Database\Seeder;

class TaxRateSeeder extends Seeder
{
    public function run(): void
    {
        TaxRate::create([
            'name' => 'TVA Alimentaire réduite',
            'rate' => 5.50,
            'country_code' => 'FR',
            'description' => 'Taux réduit pour la majorité des denrées alimentaires',
            'is_default' => true,
        ]);

        TaxRate::create([
            'name' => 'TVA Normale',
            'rate' => 20.00,
            'country_code' => 'FR',
            'description' => 'Taux normal (bonbons, chocolats, confiseries)',
            'is_default' => false,
        ]);
    }
}
