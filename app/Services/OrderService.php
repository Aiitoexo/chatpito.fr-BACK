<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Variant;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(private StockService $stockService)
    {
    }

    public function createOrder(array $validated): Order
    {
        return DB::transaction(function () use ($validated) {
            // Vérifier la disponibilité de chaque variante
            foreach ($validated['items'] as $item) {
                $variant = Variant::where('id', $item['variant_id'])->actif()->firstOrFail();

                if (!$this->stockService->checkAvailability($variant->id, $item['quantity'])) {
                    throw new \Exception("Stock insuffisant pour la variante : {$variant->nom}");
                }
            }

            // Créer la commande
            $order = Order::create([
                'user_id' => $validated['user_id'] ?? null,
                'status' => 'pending',
                'total' => $validated['total'],
                'shipping_name' => $validated['shipping']['name'] ?? null,
                'shipping_email' => $validated['shipping']['email'] ?? null,
                'shipping_address' => $validated['shipping']['address'] ?? null,
                'shipping_city' => $validated['shipping']['city'] ?? null,
                'shipping_zip' => $validated['shipping']['zip'] ?? null,
                'stripe_session_id' => $validated['stripe_session_id'] ?? null,
            ]);

            // Créer les items et décrémenter le stock
            foreach ($validated['items'] as $item) {
                $variant = Variant::with('product')->findOrFail($item['variant_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $variant->produit_id,
                    'variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'variant_name' => $variant->nom,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'] ?? $variant->prix_vente_ttc,
                ]);

                $this->stockService->confirm($variant->id, $item['quantity'], 'order', $order->id);
            }

            return $order->load('items.variant.product');
        });
    }
}
