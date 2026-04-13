<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index()
    {
        return response()->json(
            Category::roots()
                ->with(['children' => fn ($q) => $q->orderBy('ordre')->withCount('products')])
                ->withCount('products')
                ->orderBy('ordre')
                ->orderBy('name')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'ordre' => 'nullable|integer',
            'actif' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Category::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter++;
        }

        $category = Category::create($validated);
        return response()->json($category, 201);
    }

    public function show(string $id)
    {
        return response()->json(
            Category::with(['children' => fn ($q) => $q->orderBy('ordre')->withCount('products')])
                ->withCount('products')
                ->findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'image' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'ordre' => 'nullable|integer',
            'actif' => 'sometimes|boolean',
        ]);

        if (isset($validated['name']) && $validated['name'] !== $category->name) {
            $validated['slug'] = Str::slug($validated['name']);
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Category::where('slug', $validated['slug'])->where('id', '!=', $id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter++;
            }
        }

        $category->update($validated);
        return response()->json($category);
    }

    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        if ($category->products()->count() > 0) {
            return response()->json([
                'error' => 'Impossible de supprimer une catégorie contenant des produits'
            ], 422);
        }

        $category->delete();
        return response()->json(['message' => 'Catégorie supprimée']);
    }
}
