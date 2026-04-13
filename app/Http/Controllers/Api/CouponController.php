<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function apply(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'order_total' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', strtoupper($validated['code']))
            ->active()
            ->first();

        if (!$coupon) {
            return response()->json(['message' => __('coupon.not_found')], 404);
        }

        if (!$coupon->isValid($validated['order_total'])) {
            $message = __('coupon.invalid');

            if ($coupon->expires_at && $coupon->expires_at->isPast()) {
                $message = __('coupon.expired');
            } elseif ($coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit) {
                $message = __('coupon.used_up');
            } elseif ($validated['order_total'] < $coupon->min_order_amount) {
                $message = __('coupon.min_amount', ['amount' => $coupon->min_order_amount]);
            }

            return response()->json(['message' => $message], 422);
        }

        $discount = $coupon->calculateDiscount($validated['order_total']);

        return response()->json([
            'discount' => $discount,
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
            ],
        ]);
    }
}
