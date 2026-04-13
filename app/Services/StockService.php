<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function checkAvailability(int $variantId, int $quantity): bool
    {
        $stock = Stock::where('variant_id', $variantId)->first();
        if (!$stock) {
            return false;
        }
        return ($stock->quantite_disponible - $stock->quantite_reservee) >= $quantity;
    }

    public function reserve(int $variantId, int $quantity): void
    {
        DB::transaction(function () use ($variantId, $quantity) {
            $stock = Stock::where('variant_id', $variantId)->lockForUpdate()->firstOrFail();
            $stockAvant = $stock->quantite_reservee;
            $stock->quantite_reservee += $quantity;
            $stock->save();

            $this->createMovement($variantId, 'sortie', $quantity, $stock->quantite_disponible, $stock->quantite_disponible, 'reservation', null, 'Réservation stock');
        });
    }

    public function confirm(int $variantId, int $quantity, ?string $referenceType = null, ?int $referenceId = null): void
    {
        DB::transaction(function () use ($variantId, $quantity, $referenceType, $referenceId) {
            $stock = Stock::where('variant_id', $variantId)->lockForUpdate()->firstOrFail();
            $stockAvant = $stock->quantite_disponible;

            $stock->quantite_disponible -= $quantity;
            $stock->quantite_reservee = max(0, $stock->quantite_reservee - $quantity);
            $stock->quantite_physique -= $quantity;
            $stock->save();

            $this->createMovement($variantId, 'vente', -$quantity, $stockAvant, $stock->quantite_disponible, $referenceType, $referenceId, 'Vente confirmée');
        });
    }

    public function release(int $variantId, int $quantity): void
    {
        DB::transaction(function () use ($variantId, $quantity) {
            $stock = Stock::where('variant_id', $variantId)->lockForUpdate()->firstOrFail();
            $stock->quantite_reservee = max(0, $stock->quantite_reservee - $quantity);
            $stock->save();
        });
    }

    public function adjust(int $variantId, int $newQuantity, string $commentaire = ''): void
    {
        DB::transaction(function () use ($variantId, $newQuantity, $commentaire) {
            $stock = Stock::where('variant_id', $variantId)->lockForUpdate()->firstOrFail();
            $stockAvant = $stock->quantite_disponible;
            $diff = $newQuantity - $stockAvant;

            $stock->quantite_disponible = $newQuantity;
            $stock->quantite_physique = $newQuantity;
            $stock->save();

            $this->createMovement($variantId, 'correction', $diff, $stockAvant, $newQuantity, null, null, $commentaire);
        });
    }

    public function addStock(int $variantId, int $quantity, string $commentaire = '', ?string $referenceType = null, ?int $referenceId = null): void
    {
        DB::transaction(function () use ($variantId, $quantity, $commentaire, $referenceType, $referenceId) {
            $stock = Stock::where('variant_id', $variantId)->lockForUpdate()->firstOrFail();
            $stockAvant = $stock->quantite_disponible;

            $stock->quantite_disponible += $quantity;
            $stock->quantite_physique += $quantity;
            $stock->save();

            $this->createMovement($variantId, 'entree', $quantity, $stockAvant, $stock->quantite_disponible, $referenceType, $referenceId, $commentaire);
        });
    }

    private function createMovement(int $variantId, string $type, int $quantite, int $stockAvant, int $stockApres, ?string $referenceType, ?int $referenceId, ?string $commentaire): void
    {
        StockMovement::create([
            'variant_id' => $variantId,
            'type' => $type,
            'quantite' => $quantite,
            'stock_avant' => $stockAvant,
            'stock_apres' => $stockApres,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'commentaire' => $commentaire,
        ]);
    }
}
