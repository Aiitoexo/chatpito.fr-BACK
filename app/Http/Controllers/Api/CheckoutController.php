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
        ]);

        $validated['user_id'] = $request->user()?->id;

        $order = $this->orderService->createOrder($validated);

        $stripe = new StripeClient(config('services.stripe.secret'));

        $intent = $stripe->paymentIntents->create([
            'amount' => (int) ($order->total * 100),
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
