<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Variant;

class VariantController extends Controller
{
    public function index(int $productId)
    {
        $variants = Variant::where('produit_id', $productId)
            ->actif()
            ->with('stock')
            ->get();

        return response()->json($variants);
    }
}
