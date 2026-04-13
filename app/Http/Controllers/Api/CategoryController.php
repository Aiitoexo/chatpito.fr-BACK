<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::actif()
            ->roots()
            ->with(['children' => fn ($q) => $q->actif()->orderBy('ordre')->withCount('products')])
            ->withCount('products')
            ->orderBy('ordre')
            ->get();

        return response()->json($categories);
    }

    public function show(string $slug)
    {
        $category = Category::where('slug', $slug)
            ->actif()
            ->with(['children' => fn ($q) => $q->actif()->orderBy('ordre')->withCount('products')])
            ->withCount('products')
            ->firstOrFail();

        return response()->json($category);
    }
}
