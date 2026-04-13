<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockAlert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockAlertController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'variant_id' => 'required|exists:variants,id',
        ]);

        StockAlert::updateOrCreate(
            [
                'email' => $validated['email'],
                'variant_id' => $validated['variant_id'],
            ],
            ['notified' => false, 'notified_at' => null]
        );

        return response()->json(['message' => __('stock_alert.subscribed')]);
    }
}
