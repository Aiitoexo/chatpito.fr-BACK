<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ShippingCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function __construct(private ShippingCalculatorService $calculator)
    {
    }

    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.variant_id' => 'required|exists:variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'sometimes|numeric',
        ]);

        $methods = $this->calculator->calculate($validated['items']);

        return response()->json(['data' => $methods]);
    }
}
