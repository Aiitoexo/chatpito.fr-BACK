<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    public function __construct(
        private OrderService $orderService,
    ) {}

    public function createPaymentIntent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.variant_id' => 'required|exists:variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
            'shipping' => 'required|array',
            'shipping.name' => 'required|string',
            'shipping.email' => 'required|email',
            'shipping.address' => 'required|string',
            'shipping.city' => 'required|string',
            'shipping.zip' => 'required|string',
            'shipping_method_id' => 'nullable|exists:shipping_methods,id',
            'shipping_cost' => 'nullable|numeric|min:0',
        ]);

        $validated['user_id'] = $request->user()?->id;

        $order = $this->orderService->createOrder($validated);

        $shippingCost = $validated['shipping_cost'] ?? 0;

        $order->update([
            'shipping_cost' => $shippingCost,
            'shipping_method_id' => $validated['shipping_method_id'] ?? null,
        ]);

        $totalWithShipping = $order->total + $shippingCost;

        $stripe = new StripeClient(config('services.stripe.secret'));

        $intent = $stripe->paymentIntents->create([
            'amount' => (int) round($totalWithShipping * 100),
            'currency' => 'eur',
            'metadata' => ['order_id' => $order->id],
        ]);

        $order->update(['stripe_payment_intent_id' => $intent->id]);

        return response()->json([
            'client_secret' => $intent->client_secret,
            'order_id' => $order->id,
        ]);
    }
}
