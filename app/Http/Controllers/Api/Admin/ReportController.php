<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function sales(Request $request): JsonResponse
    {
        $days = (int) $request->get('period', 30);
        $from = now()->subDays($days)->startOfDay();

        $paidOrders = Order::where('status', 'paid')
            ->where('created_at', '>=', $from);

        $totalRevenue = (clone $paidOrders)->sum('total');
        $ordersCount = (clone $paidOrders)->count();
        $avgOrder = $ordersCount > 0 ? round($totalRevenue / $ordersCount, 2) : 0;

        $dailyRevenue = Order::where('status', 'paid')
            ->where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'total_revenue' => round($totalRevenue, 2),
            'orders_count' => $ordersCount,
            'avg_order' => $avgOrder,
            'daily_revenue' => $dailyRevenue,
        ]);
    }

    public function products(Request $request): JsonResponse
    {
        $days = (int) $request->get('period', 30);
        $from = now()->subDays($days)->startOfDay();

        $topProducts = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'paid')
            ->where('orders.created_at', '>=', $from)
            ->selectRaw('order_items.product_name, SUM(order_items.quantity) as total_qty, SUM(order_items.price * order_items.quantity) as total_revenue')
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        return response()->json(['data' => $topProducts]);
    }

    public function orders(Request $request): JsonResponse
    {
        $days = (int) $request->get('period', 30);
        $from = now()->subDays($days)->startOfDay();

        $statusCounts = Order::where('created_at', '>=', $from)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return response()->json(['data' => $statusCounts]);
    }
}
