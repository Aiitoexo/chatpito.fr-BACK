<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        $userOrders = Order::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhere('shipping_email', $user->email);
        });

        $ordersCount = (clone $userOrders)->count();
        $totalSpent = (clone $userOrders)->where('status', 'paid')->sum('total');

        $favoriteProduct = (clone $userOrders)
            ->where('status', 'paid')
            ->with('items')
            ->get()
            ->flatMap->items
            ->groupBy('product_name')
            ->map->sum('quantity')
            ->sortDesc()
            ->keys()
            ->first();

        $recentOrders = (clone $userOrders)
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

    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $request->user()->id,
            'phone' => 'sometimes|string|max:20|nullable',
            'password' => 'sometimes|min:8|confirmed',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $request->user()->update($validated);

        return response()->json([
            'data' => $request->user()->fresh(),
            'message' => __('user.profile_updated'),
        ]);
    }
}
