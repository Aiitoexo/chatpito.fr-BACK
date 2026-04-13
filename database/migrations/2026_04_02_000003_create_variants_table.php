<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('products')->onDelete('cascade');
            $table->string('sku', 100)->unique()->nullable();
            $table->string('nom', 255);
            $table->decimal('prix_vente_ht', 10, 2);
            $table->decimal('prix_vente_ttc', 10, 2);
            $table->decimal('taux_tva', 5, 2)->default(5.50);
            $table->decimal('poids', 8, 2)->nullable();
            $table->decimal('largeur', 8, 2)->nullable();
            $table->decimal('hauteur', 8, 2)->nullable();
            $table->decimal('profondeur', 8, 2)->nullable();
            $table->string('code_barre', 50)->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        // Créer une variante "Standard" pour chaque produit existant
        $products = DB::table('products')->get();
        foreach ($products as $product) {
            $prixHt = round($product->price / 1.055, 2);
            DB::table('variants')->insert([
                'produit_id' => $product->id,
                'sku' => 'LEGACY-' . $product->id,
                'nom' => 'Standard',
                'prix_vente_ht' => $prixHt,
                'prix_vente_ttc' => $product->price,
                'taux_tva' => 5.50,
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('variants');
    }
};
