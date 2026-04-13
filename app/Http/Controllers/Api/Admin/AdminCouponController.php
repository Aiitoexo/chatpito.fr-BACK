<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminCouponController extends Controller
{
    public function index(): JsonResponse
    {
        $coupons = Coupon::latest()->get();
        return response()->json(['data' => $coupons]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);

        $coupon = Coupon::create($validated);

        return response()->json(['data' => $coupon], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $coupon = Coupon::findOrFail($id);

        $validated = $request->validate([
            'code' => "sometimes|string|unique:coupons,code,{$id}",
            'type' => 'sometimes|in:percent,fixed',
            'value' => 'sometimes|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
            'is_active' => 'sometimes|boolean',
        ]);

        if (isset($validated['code'])) {
            $validated['code'] = strtoupper($validated['code']);
        }

        $coupon->update($validated);

        return response()->json(['data' => $coupon]);
    }

    public function destroy(string $id): JsonResponse
    {
        Coupon::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    public function toggle(string $id): JsonResponse
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->update(['is_active' => !$coupon->is_active]);

        return response()->json(['data' => $coupon]);
    }
}
