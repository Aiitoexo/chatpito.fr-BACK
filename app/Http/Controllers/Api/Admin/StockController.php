<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct(private StockService $stockService)
    {
    }

    public function index(Request $request)
    {
        $query = Stock::with(['variant.product']);

        if ($request->has('low_stock') && $request->low_stock === 'true') {
            $query->whereColumn('quantite_disponible', '<=', 'seuil_alerte');
        }

        if ($request->has('product_id')) {
            $query->whereHas('variant', fn ($q) => $q->where('produit_id', $request->product_id));
        }

        return response()->json($query->get());
    }

    public function show(string $variantId)
    {
        $stock = Stock::where('variant_id', $variantId)
            ->with(['variant.product'])
            ->firstOrFail();

        $recentMovements = StockMovement::where('variant_id', $variantId)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return response()->json([
            'stock' => $stock,
            'movements' => $recentMovements,
        ]);
    }

    public function adjust(Request $request, string $variantId)
    {
        $validated = $request->validate([
            'quantite' => 'required|integer|min:0',
            'commentaire' => 'required|string|max:500',
        ]);

        $this->stockService->adjust($variantId, $validated['quantite'], $validated['commentaire']);

        $stock = Stock::where('variant_id', $variantId)->with(['variant.product'])->firstOrFail();
        return response()->json($stock);
    }

    public function movements(string $variantId)
    {
        $movements = StockMovement::where('variant_id', $variantId)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json($movements);
    }

    public function allMovements(Request $request)
    {
        $query = StockMovement::with(['variant.product'])
            ->orderBy('created_at', 'desc');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('from')) {
            $query->where('created_at', '>=', $request->from);
        }

        if ($request->has('to')) {
            $query->where('created_at', '<=', $request->to);
        }

        return response()->json($query->paginate(50));
    }
}
