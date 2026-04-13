<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Stock;
use App\Models\Variant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats()
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total');
        $totalProducts = Product::count();
        $totalVariants = Variant::count();
        $totalCategories = Category::count();
        $totalUsers = User::where('role', 'user')->count();

        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $recentOrders = Order::with(['items.variant.product'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $lowStockProducts = Stock::where('quantite_disponible', '<=', DB::raw('seuil_alerte'))
            ->with(['variant.product'])
            ->orderBy('quantite_disponible', 'asc')
            ->take(10)
            ->get();

        $revenueByDay = Order::where('status', '!=', 'cancelled')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as revenue'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'totalOrders' => $totalOrders,
            'totalRevenue' => (float) $totalRevenue,
            'totalProducts' => $totalProducts,
            'totalVariants' => $totalVariants,
            'totalCategories' => $totalCategories,
            'totalUsers' => $totalUsers,
            'ordersByStatus' => $ordersByStatus,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
            'revenueByDay' => $revenueByDay,
        ]);
    }
}
