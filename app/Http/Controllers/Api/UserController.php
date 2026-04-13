<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        $ordersCount = $user->orders()->count();
        $totalSpent = $user->orders()->where('status', 'paid')->sum('total');

        $favoriteProduct = $user->orders()
            ->where('status', 'paid')
            ->with('items')
            ->get()
            ->flatMap->items
            ->groupBy('product_name')
            ->map->sum('quantity')
            ->sortDesc()
            ->keys()
            ->first();

        $recentOrders = $user->orders()
            ->with('items')
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn ($order) => [
                'id' => $order->id,
                'status' => $order->status,
                'total' => $order->total,
                'items_count' => $order->items->sum('quantity'),
                'created_at' => $order->created_at,
            ]);

        return response()->json([
            'orders_count' => $ordersCount,
            'total_spent' => round($totalSpent, 2),
            'favorite_product' => $favoriteProduct,
            'recent_orders' => $recentOrders,
        ]);
    }
}
