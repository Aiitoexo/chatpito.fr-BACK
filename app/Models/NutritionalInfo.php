<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NutritionalInfo extends Model
{
    protected $table = 'nutritional_infos';

    protected $fillable = [
        'produit_id', 'ingredients', 'allergenes', 'traces',
        'energie_kcal', 'energie_kj', 'matieres_grasses',
        'dont_acides_gras_satures', 'glucides', 'dont_sucres', 'proteines',
    ];

    protected $casts = [
        'energie_kcal' => 'decimal:2',
        'energie_kj' => 'decimal:2',
        'matieres_grasses' => 'decimal:2',
        'dont_acides_gras_satures' => 'decimal:2',
        'glucides' => 'decimal:2',
        'dont_sucres' => 'decimal:2',
        'proteines' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'produit_id');
    }
}
