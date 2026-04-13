<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        $reviews = Review::where('product_id', $product->id)
            ->approved()
            ->with('user:id,name')
            ->latest()
            ->get();

        $averageRating = $reviews->avg('rating');
        $ratingsCount = $reviews->count();

        return response()->json([
            'data' => $reviews,
            'average_rating' => $averageRating ? round($averageRating, 1) : null,
            'ratings_count' => $ratingsCount,
        ]);
    }

    public function store(Request $request, string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        $hasPurchased = $request->user()->orders()
            ->whereIn('status', ['paid', 'shipped', 'delivered'])
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->exists();

        if (!$hasPurchased) {
            return response()->json([
                'message' => __('review.must_purchase'),
            ], 403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:100',
            'body' => 'nullable|string|max:1000',
            'order_id' => 'required|exists:orders,id',
        ]);

        $review = Review::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
            ],
            array_merge($validated, ['user_id' => $request->user()->id, 'product_id' => $product->id])
        );

        return response()->json(['data' => $review->load('user:id,name')], 201);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $review = Review::where('user_id', $request->user()->id)->findOrFail($id);
        $review->delete();

        return response()->json(null, 204);
    }

    public function adminIndex(): JsonResponse
    {
        $reviews = Review::with(['user:id,name', 'product:id,name,slug'])
            ->latest()
            ->get();

        return response()->json(['data' => $reviews]);
    }

    public function approve(string $id): JsonResponse
    {
        $review = Review::findOrFail($id);
        $review->update(['is_approved' => !$review->is_approved]);

        return response()->json(['data' => $review]);
    }

    public function adminDestroy(string $id): JsonResponse
    {
        Review::findOrFail($id)->delete();

        return response()->json(null, 204);
    }
}
