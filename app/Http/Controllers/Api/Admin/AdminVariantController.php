<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Variant;
use Illuminate\Http\Request;

class AdminVariantController extends Controller
{
    public function index(string $productId)
    {
        $product = Product::findOrFail($productId);
        return response()->json(
            $product->variants()->with(['stock', 'suppliers'])->get()
        );
    }

    public function store(Request $request, string $productId)
    {
        $product = Product::findOrFail($productId);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:variants,sku',
            'prix_vente_ht' => 'required|numeric|min:0',
            'prix_vente_ttc' => 'required|numeric|min:0',
            'taux_tva' => 'nullable|numeric',
            'poids' => 'nullable|numeric',
            'largeur' => 'nullable|numeric',
            'hauteur' => 'nullable|numeric',
            'profondeur' => 'nullable|numeric',
            'code_barre' => 'nullable|string|max:50',
            'actif' => 'boolean',
            'stock_initial' => 'nullable|integer|min:0',
        ]);

        $variant = $product->variants()->create(
            collect($validated)->except('stock_initial')->toArray()
        );

        $stockInitial = $validated['stock_initial'] ?? 0;
        Stock::create([
            'variant_id' => $variant->id,
            'quantite_disponible' => $stockInitial,
            'quantite_physique' => $stockInitial,
        ]);

        return response()->json($variant->load('stock'), 201);
    }

    public function show(string $productId, string $variantId)
    {
        $variant = Variant::where('produit_id', $productId)
            ->with(['stock', 'suppliers'])
            ->findOrFail($variantId);

        return response()->json($variant);
    }

    public function update(Request $request, string $productId, string $variantId)
    {
        $variant = Variant::where('produit_id', $productId)->findOrFail($variantId);

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'sku' => 'nullable|string|max:100|unique:variants,sku,' . $variantId,
            'prix_vente_ht' => 'sometimes|numeric|min:0',
            'prix_vente_ttc' => 'sometimes|numeric|min:0',
            'taux_tva' => 'nullable|numeric',
            'poids' => 'nullable|numeric',
            'largeur' => 'nullable|numeric',
            'hauteur' => 'nullable|numeric',
            'profondeur' => 'nullable|numeric',
            'code_barre' => 'nullable|string|max:50',
            'actif' => 'sometimes|boolean',
        ]);

        $variant->update($validated);
        return response()->json($variant->load('stock'));
    }

    public function destroy(string $productId, string $variantId)
    {
        $variant = Variant::where('produit_id', $productId)->findOrFail($variantId);

        if ($variant->orderItems()->whereHas('order', fn ($q) => $q->whereIn('status', ['pending', 'paid']))->exists()) {
            return response()->json(['error' => 'Impossible de supprimer une variante avec des commandes en cours'], 422);
        }

        $variant->delete();
        return response()->json(['message' => 'Variante supprimée']);
    }
}
