<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supplier extends Model
{
    protected $fillable = [
        'nom', 'raison_sociale', 'siret', 'tva_intracommunautaire',
        'contact_nom', 'email', 'telephone', 'adresse',
        'code_postal', 'ville', 'pays', 'notes',
    ];

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(Variant::class, 'variant_supplier')
            ->withPivot([
                'fournisseur_sku', 'nom_produit_fournisseur',
                'prix_achat_ht', 'taux_tva_achat', 'prix_achat_ttc',
                'devise', 'quantite_min_commande', 'multiple_commande',
                'delai_livraison_jours', 'frais_livraison',
                'url_produit_fournisseur', 'actif',
            ])
            ->withTimestamps();
    }
}
