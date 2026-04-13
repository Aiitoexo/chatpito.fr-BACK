<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $fillable = [
        'variant_id', 'quantite_disponible', 'quantite_reservee',
        'quantite_physique', 'seuil_alerte', 'seuil_reappro', 'emplacement',
    ];

    protected $appends = ['is_low_stock', 'quantite_commandable'];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->quantite_disponible <= $this->seuil_alerte;
    }

    public function getQuantiteCommandableAttribute(): int
    {
        return $this->quantite_disponible - $this->quantite_reservee;
    }
}
