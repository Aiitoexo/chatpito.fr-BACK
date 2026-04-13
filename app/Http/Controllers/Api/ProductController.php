<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'mainImage', 'variants.stock', 'tags'])
            ->actif();

        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description_courte', 'like', "%{$search}%")
                  ->orWhere('description_longue', 'like', "%{$search}%");
            });
        }

        if ($request->has('featured') && $request->featured === 'true') {
            $query->where('featured', true);
        }

        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($products);
    }

    public function show(string $slug)
    {
        $product = Product::with([
            'category',
            'images' => fn ($q) => $q->orderBy('ordre'),
            'variants' => fn ($q) => $q->actif()->with('stock'),
            'tags',
            'nutritionalInfo',
        ])
            ->actif()
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($product);
    }

    public function featured()
    {
        $products = Product::with(['category', 'mainImage', 'variants.stock', 'tags'])
            ->actif()
            ->where('featured', true)
            ->take(8)
            ->get();

        return response()->json($products);
    }
}
