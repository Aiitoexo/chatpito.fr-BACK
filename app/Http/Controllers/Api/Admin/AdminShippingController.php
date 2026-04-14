<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminShippingController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => ShippingMethod::all()]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'carrier' => 'required|string|max:100',
            'base_price' => 'required|numeric|min:0',
            'free_above' => 'nullable|numeric|min:0',
            'min_weight_grams' => 'nullable|integer|min:0',
            'max_weight_grams' => 'nullable|integer|min:0',
            'estimated_days_min' => 'required|integer|min:0',
            'estimated_days_max' => 'required|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $method = ShippingMethod::create($validated);
        return response()->json(['data' => $method], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $method = ShippingMethod::findOrFail($id);
        $method->update($request->validate([
            'name' => 'sometimes|string|max:255',
            'carrier' => 'sometimes|string|max:100',
            'base_price' => 'sometimes|numeric|min:0',
            'free_above' => 'nullable|numeric|min:0',
            'min_weight_grams' => 'nullable|integer|min:0',
            'max_weight_grams' => 'nullable|integer|min:0',
            'estimated_days_min' => 'sometimes|integer|min:0',
            'estimated_days_max' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]));
        return response()->json(['data' => $method]);
    }

    public function destroy(string $id): JsonResponse
    {
        ShippingMethod::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    public function toggle(string $id): JsonResponse
    {
        $method = ShippingMethod::findOrFail($id);
        $method->update(['is_active' => !$method->is_active]);
        return response()->json(['data' => $method]);
    }
}
