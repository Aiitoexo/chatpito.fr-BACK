<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Variant;
use Illuminate\Http\Request;

class VariantSupplierController extends Controller
{
    public function index(string $variantId)
    {
        $variant = Variant::with(['suppliers'])->findOrFail($variantId);

        return response()->json($variant->suppliers);
    }

    public function store(Request $request, string $variantId)
    {
        $variant = Variant::findOrFail($variantId);

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'fournisseur_sku' => 'nullable|string|max:100',
            'nom_produit_fournisseur' => 'nullable|string|max:255',
            'prix_achat_ht' => 'required|numeric|min:0',
            'taux_tva_achat' => 'nullable|numeric',
            'prix_achat_ttc' => 'required|numeric|min:0',
            'devise' => 'nullable|string|max:3',
            'quantite_min_commande' => 'nullable|integer|min:1',
            'multiple_commande' => 'nullable|integer|min:1',
            'delai_livraison_jours' => 'nullable|integer',
            'frais_livraison' => 'nullable|numeric|min:0',
            'url_produit_fournisseur' => 'nullable|string|max:500',
            'actif' => 'boolean',
        ]);

        $supplierId = $validated['supplier_id'];
        unset($validated['supplier_id']);

        $variant->suppliers()->syncWithoutDetaching([
            $supplierId => $validated,
        ]);

        return response()->json($variant->load('suppliers'), 201);
    }

    public function destroy(string $variantId, string $supplierId)
    {
        $variant = Variant::findOrFail($variantId);
        $variant->suppliers()->detach($supplierId);

        return response()->json(['message' => 'Lien fournisseur supprimé']);
    }
}
