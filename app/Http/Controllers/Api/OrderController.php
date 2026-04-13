<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Variant;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService)
    {
    }

    public function index(Request $request)
    {
        $userId = $request->user()?->id;

        if (!$userId) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }

        $orders = Order::where('user_id', $userId)
            ->with(['items.variant.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.variant_id' => 'required|exists:variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
            'total' => 'required|numeric',
            'shipping' => 'required|array',
            'shipping.name' => 'required|string',
            'shipping.email' => 'required|email',
            'shipping.address' => 'required|string',
            'shipping.city' => 'required|string',
            'shipping.zip' => 'required|string',
        ]);

        $validated['user_id'] = $request->user()?->id;

        try {
            $order = $this->orderService->createOrder($validated);
            return response()->json($order, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show(string $id)
    {
        $order = Order::with(['items.variant.product', 'invoice'])->findOrFail($id);
        return response()->json($order);
    }

    public function byPaymentIntent(string $paymentIntent)
    {
        $order = Order::with(['items.variant.product'])
            ->where('stripe_payment_intent_id', $paymentIntent)
            ->firstOrFail();

        return response()->json($order);
    }

    public function byEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = \App\Models\User::where('email', $request->email)->first();

        $orders = Order::where(function ($query) use ($request, $user) {
                $query->where('shipping_email', $request->email);
                if ($user) {
                    $query->orWhere('user_id', $user->id);
                }
            })
            ->with(['items.variant.product', 'invoice'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    public function updateStatus(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,shipped,delivered,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $validated['status']]);

        return response()->json($order);
    }

    public function webhook(Request $request)
    {
        $validated = $request->validate([
            'email' => 'nullable|email',
            'status' => 'required|string',
            'total' => 'required|numeric',
            'shipping_name' => 'nullable|string',
            'shipping_email' => 'nullable|email',
            'shipping_address' => 'nullable|string',
            'shipping_city' => 'nullable|string',
            'shipping_zip' => 'nullable|string',
            'stripe_session_id' => 'required|string',
            'items' => 'required|array',
            'items.*.variant_id' => 'required|exists:variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
        ]);

        $userId = null;
        if ($validated['email']) {
            $user = \App\Models\User::where('email', $validated['email'])->first();
            $userId = $user?->id;
        }

        try {
            $order = $this->orderService->createOrder([
                'user_id' => $userId,
                'total' => $validated['total'],
                'stripe_session_id' => $validated['stripe_session_id'],
                'shipping' => [
                    'name' => $validated['shipping_name'],
                    'email' => $validated['shipping_email'] ?? $validated['email'],
                    'address' => $validated['shipping_address'],
                    'city' => $validated['shipping_city'],
                    'zip' => $validated['shipping_zip'],
                ],
                'items' => $validated['items'],
            ]);

            return response()->json(['success' => true, 'order_id' => $order->id], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
