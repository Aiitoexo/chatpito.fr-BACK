<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class AdminReturnController extends Controller
{
    public function index(): JsonResponse
    {
        $returns = ReturnRequest::with('order')
            ->latest()
            ->get();

        return response()->json(['data' => $returns]);
    }

    public function show(string $id): JsonResponse
    {
        $return = ReturnRequest::with('order.items')->findOrFail($id);
        return response()->json(['data' => $return]);
    }

    public function approve(Request $request, string $id): JsonResponse
    {
        $return = ReturnRequest::findOrFail($id);
        $validated = $request->validate([
            'refund_amount' => 'required|numeric|min:0',
            'admin_notes' => 'nullable|string',
        ]);

        $return->update([
            'status' => 'approved',
            'refund_amount' => $validated['refund_amount'],
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        ActivityLogger::log('return_approved', $return, [], ['refund_amount' => $validated['refund_amount']]);

        return response()->json(['data' => $return]);
    }

    public function reject(Request $request, string $id): JsonResponse
    {
        $return = ReturnRequest::findOrFail($id);
        $return->update([
            'status' => 'rejected',
            'admin_notes' => $request->input('admin_notes'),
        ]);

        ActivityLogger::log('return_rejected', $return);

        return response()->json(['data' => $return]);
    }

    public function refund(string $id): JsonResponse
    {
        $return = ReturnRequest::with('order')->findOrFail($id);

        if (!$return->order->stripe_payment_intent_id) {
            return response()->json(['message' => 'Pas de payment intent Stripe'], 422);
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        $refund = $stripe->refunds->create([
            'payment_intent' => $return->order->stripe_payment_intent_id,
            'amount' => (int) round($return->refund_amount * 100),
            'reason' => 'requested_by_customer',
        ]);

        $return->update([
            'status' => 'refunded',
            'stripe_refund_id' => $refund->id,
        ]);

        ActivityLogger::log('return_refunded', $return, [], ['stripe_refund_id' => $refund->id]);

        return response()->json(['data' => $return]);
    }
}
