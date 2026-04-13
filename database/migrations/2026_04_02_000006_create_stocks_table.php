<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->unique()->constrained('variants')->onDelete('cascade');
            $table->integer('quantite_disponible')->default(0);
            $table->integer('quantite_reservee')->default(0);
            $table->integer('quantite_physique')->default(0);
            $table->integer('seuil_alerte')->default(10);
            $table->integer('seuil_reappro')->default(20);
            $table->string('emplacement', 100)->nullable();
            $table->timestamps();
        });

        // Migrer les stocks existants depuis les produits
        $variants = DB::table('variants')->get();
        foreach ($variants as $variant) {
            $product = DB::table('products')->find($variant->produit_id);
            $stock = $product->stock ?? 0;
            DB::table('stocks')->insert([
                'variant_id' => $variant->id,
                'quantite_disponible' => $stock,
                'quantite_reservee' => 0,
                'quantite_physique' => $stock,
                'seuil_alerte' => 10,
                'seuil_reappro' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
