<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = $request->user()
            ->wishlist()
            ->with(['category', 'variants.stock', 'mainImage'])
            ->get();

        return response()->json(['data' => $products]);
    }

    public function toggle(Request $request, int $productId): JsonResponse
    {
        $exists = $request->user()->wishlist()->where('product_id', $productId)->exists();

        if ($exists) {
            $request->user()->wishlist()->detach($productId);
            return response()->json(['action' => 'removed']);
        }

        $request->user()->wishlist()->attach($productId);
        return response()->json(['action' => 'added']);
    }

    public function destroy(Request $request, int $productId): JsonResponse
    {
        $request->user()->wishlist()->detach($productId);
        return response()->json(null, 204);
    }
}
