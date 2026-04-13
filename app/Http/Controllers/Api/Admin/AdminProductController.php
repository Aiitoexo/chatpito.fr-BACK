<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index()
    {
        return response()->json(
            Product::with(['category', 'variants.stock', 'mainImage', 'tags'])
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description_courte' => 'nullable|string|max:500',
            'description_longue' => 'nullable|string',
            'marque' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'featured' => 'boolean',
            'actif' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            // Variantes
            'variants' => 'required|array|min:1',
            'variants.*.nom' => 'required|string|max:255',
            'variants.*.sku' => 'nullable|string|max:100',
            'variants.*.prix_vente_ht' => 'required|numeric|min:0',
            'variants.*.prix_vente_ttc' => 'required|numeric|min:0',
            'variants.*.taux_tva' => 'nullable|numeric',
            'variants.*.poids' => 'nullable|numeric',
            'variants.*.largeur' => 'nullable|numeric',
            'variants.*.hauteur' => 'nullable|numeric',
            'variants.*.profondeur' => 'nullable|numeric',
            'variants.*.code_barre' => 'nullable|string|max:50',
            'variants.*.actif' => 'boolean',
            'variants.*.stock_initial' => 'nullable|integer|min:0',
            // Images
            'images' => 'nullable|array',
            'images.*.url' => 'required|string',
            'images.*.alt_text' => 'nullable|string|max:255',
            'images.*.ordre' => 'nullable|integer',
            'images.*.est_principale' => 'boolean',
            // Tags
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:100',
            // Infos nutritionnelles
            'nutritional_info' => 'nullable|array',
        ]);

        return DB::transaction(function () use ($validated) {
            // Slug
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }

            // Créer le produit
            $product = Product::create([
                'name' => $validated['name'],
                'slug' => $slug,
                'description_courte' => $validated['description_courte'] ?? null,
                'description_longue' => $validated['description_longue'] ?? null,
                'marque' => $validated['marque'] ?? null,
                'category_id' => $validated['category_id'],
                'featured' => $validated['featured'] ?? false,
                'actif' => $validated['actif'] ?? true,
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
            ]);

            // Créer les variantes avec stock
            foreach ($validated['variants'] as $variantData) {
                $variant = $product->variants()->create([
                    'nom' => $variantData['nom'],
                    'sku' => $variantData['sku'] ?? null,
                    'prix_vente_ht' => $variantData['prix_vente_ht'],
                    'prix_vente_ttc' => $variantData['prix_vente_ttc'],
                    'taux_tva' => $variantData['taux_tva'] ?? 5.50,
                    'poids' => $variantData['poids'] ?? null,
                    'largeur' => $variantData['largeur'] ?? null,
                    'hauteur' => $variantData['hauteur'] ?? null,
                    'profondeur' => $variantData['profondeur'] ?? null,
                    'code_barre' => $variantData['code_barre'] ?? null,
                    'actif' => $variantData['actif'] ?? true,
                ]);

                $stockInitial = $variantData['stock_initial'] ?? 0;
                Stock::create([
                    'variant_id' => $variant->id,
                    'quantite_disponible' => $stockInitial,
                    'quantite_physique' => $stockInitial,
                ]);
            }

            // Créer les images
            if (!empty($validated['images'])) {
                foreach ($validated['images'] as $imageData) {
                    $product->images()->create($imageData);
                }
            }

            // Attacher les tags
            if (!empty($validated['tags'])) {
                $tagIds = [];
                foreach ($validated['tags'] as $tagName) {
                    $tag = Tag::firstOrCreate(
                        ['slug' => Str::slug($tagName)],
                        ['nom' => $tagName]
                    );
                    $tagIds[] = $tag->id;
                }
                $product->tags()->sync($tagIds);
            }

            // Infos nutritionnelles
            if (!empty($validated['nutritional_info'])) {
                $product->nutritionalInfo()->create($validated['nutritional_info']);
            }

            return response()->json(
                $product->load(['category', 'variants.stock', 'images', 'tags', 'nutritionalInfo']),
                201
            );
        });
    }

    public function show(string $id)
    {
        return response()->json(
            Product::with(['category', 'variants.stock', 'images', 'tags', 'nutritionalInfo'])
                ->findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description_courte' => 'nullable|string|max:500',
            'description_longue' => 'nullable|string',
            'marque' => 'nullable|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'featured' => 'sometimes|boolean',
            'actif' => 'sometimes|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            // Variantes
            'variants' => 'sometimes|array|min:1',
            'variants.*.id' => 'nullable|integer',
            'variants.*.nom' => 'required|string|max:255',
            'variants.*.sku' => 'nullable|string|max:100',
            'variants.*.prix_vente_ht' => 'required|numeric|min:0',
            'variants.*.prix_vente_ttc' => 'required|numeric|min:0',
            'variants.*.taux_tva' => 'nullable|numeric',
            'variants.*.poids' => 'nullable|numeric',
            'variants.*.largeur' => 'nullable|numeric',
            'variants.*.hauteur' => 'nullable|numeric',
            'variants.*.profondeur' => 'nullable|numeric',
            'variants.*.code_barre' => 'nullable|string|max:50',
            'variants.*.actif' => 'boolean',
            'variants.*.stock_initial' => 'nullable|integer|min:0',
            // Images
            'images' => 'sometimes|array',
            'images.*.id' => 'nullable|integer',
            'images.*.url' => 'required|string',
            'images.*.alt_text' => 'nullable|string|max:255',
            'images.*.ordre' => 'nullable|integer',
            'images.*.est_principale' => 'boolean',
            // Tags
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:100',
            // Infos nutritionnelles
            'nutritional_info' => 'nullable|array',
        ]);

        return DB::transaction(function () use ($product, $validated, $id) {
            // Mise à jour du slug si le nom change
            $productData = collect($validated)->only([
                'name', 'description_courte', 'description_longue', 'marque',
                'category_id', 'featured', 'actif', 'meta_title', 'meta_description',
            ])->toArray();

            if (isset($validated['name']) && $validated['name'] !== $product->name) {
                $slug = Str::slug($validated['name']);
                $originalSlug = $slug;
                $counter = 1;
                while (Product::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                    $slug = $originalSlug . '-' . $counter++;
                }
                $productData['slug'] = $slug;
            }

            $product->update($productData);

            // Sync variantes
            if (isset($validated['variants'])) {
                $existingIds = [];
                foreach ($validated['variants'] as $variantData) {
                    if (!empty($variantData['id'])) {
                        $variant = $product->variants()->findOrFail($variantData['id']);
                        $variant->update(collect($variantData)->except(['id', 'stock_initial'])->toArray());
                        $existingIds[] = $variant->id;
                    } else {
                        $variant = $product->variants()->create(
                            collect($variantData)->except(['stock_initial'])->toArray()
                        );
                        $stockInitial = $variantData['stock_initial'] ?? 0;
                        Stock::create([
                            'variant_id' => $variant->id,
                            'quantite_disponible' => $stockInitial,
                            'quantite_physique' => $stockInitial,
                        ]);
                        $existingIds[] = $variant->id;
                    }
                }
                // Supprimer les variantes non envoyées (sauf si commandes en cours)
                $product->variants()->whereNotIn('id', $existingIds)
                    ->whereDoesntHave('orderItems', fn ($q) => $q->whereHas('order', fn ($o) => $o->whereIn('status', ['pending', 'paid'])))
                    ->delete();
            }

            // Sync images
            if (isset($validated['images'])) {
                $existingImageIds = [];
                foreach ($validated['images'] as $imageData) {
                    if (!empty($imageData['id'])) {
                        $image = $product->images()->findOrFail($imageData['id']);
                        $image->update($imageData);
                        $existingImageIds[] = $image->id;
                    } else {
                        $image = $product->images()->create($imageData);
                        $existingImageIds[] = $image->id;
                    }
                }
                $product->images()->whereNotIn('id', $existingImageIds)->delete();
            }

            // Sync tags
            if (isset($validated['tags'])) {
                $tagIds = [];
                foreach ($validated['tags'] as $tagName) {
                    $tag = Tag::firstOrCreate(
                        ['slug' => Str::slug($tagName)],
                        ['nom' => $tagName]
                    );
                    $tagIds[] = $tag->id;
                }
                $product->tags()->sync($tagIds);
            }

            // Sync infos nutritionnelles
            if (isset($validated['nutritional_info'])) {
                $product->nutritionalInfo()->updateOrCreate(
                    ['produit_id' => $product->id],
                    $validated['nutritional_info']
                );
            }

            return response()->json(
                $product->load(['category', 'variants.stock', 'images', 'tags', 'nutritionalInfo'])
            );
        });
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['message' => 'Produit supprimé']);
    }
}
