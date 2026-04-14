<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminClientController extends Controller
{
    public function index(): JsonResponse
    {
        $clients = User::where('role', 'user')
            ->withCount('orders')
            ->addSelect([
                'total_spent' => Order::selectRaw('COALESCE(SUM(total), 0)')
                    ->whereColumn('user_id', 'users.id')
                    ->where('status', 'paid'),
            ])
            ->latest()
            ->get();

        return response()->json(['data' => $clients]);
    }

    public function show(string $id): JsonResponse
    {
        $user = User::with(['orders' => fn ($q) => $q->latest()->limit(10)->with('items')])
            ->withCount('orders')
            ->findOrFail($id);

        return response()->json(['data' => $user]);
    }
}
