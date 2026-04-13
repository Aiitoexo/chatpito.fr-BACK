<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Variant extends Model
{
    protected $fillable = [
        'produit_id', 'sku', 'nom', 'prix_vente_ht', 'prix_vente_ttc',
        'taux_tva', 'poids', 'largeur', 'hauteur', 'profondeur',
        'code_barre', 'actif',
    ];

    protected $casts = [
        'prix_vente_ht' => 'decimal:2',
        'prix_vente_ttc' => 'decimal:2',
        'taux_tva' => 'decimal:2',
        'poids' => 'decimal:2',
        'largeur' => 'decimal:2',
        'hauteur' => 'decimal:2',
        'profondeur' => 'decimal:2',
        'actif' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'produit_id');
    }

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class);
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'variant_supplier')
            ->withPivot([
                'fournisseur_sku', 'nom_produit_fournisseur',
                'prix_achat_ht', 'taux_tva_achat', 'prix_achat_ttc',
                'devise', 'quantite_min_commande', 'multiple_commande',
                'delai_livraison_jours', 'frais_livraison',
                'url_produit_fournisseur', 'actif',
            ])
            ->withTimestamps();
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }
}
