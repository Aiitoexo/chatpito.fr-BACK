<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderShippedMail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items.variant.product', 'user']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json(
            $query->orderBy('created_at', 'desc')->get()
        );
    }

    public function show(string $id)
    {
        return response()->json(
            Order::with(['items.variant.product', 'user'])->findOrFail($id)
        );
    }

    public function updateStatus(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,preparing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string|max:100',
            'carrier' => 'nullable|string|max:50',
        ]);

        $order = Order::findOrFail($id);

        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'shipped') {
            $updateData['tracking_number'] = $validated['tracking_number'] ?? null;
            $updateData['carrier'] = $validated['carrier'] ?? null;
            $updateData['shipped_at'] = now();
        }

        $order->update($updateData);

        if ($validated['status'] === 'shipped') {
            Mail::to($order->shipping_email)->queue(new OrderShippedMail($order));
        }

        return response()->json($order->load(['items.variant.product', 'user']));
    }
}
