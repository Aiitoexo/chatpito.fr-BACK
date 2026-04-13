<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('products')->onDelete('cascade');
            $table->string('url', 500);
            $table->string('alt_text', 255)->nullable();
            $table->integer('ordre')->default(0);
            $table->boolean('est_principale')->default(false);
            $table->timestamps();
        });

        // Migrer les images existantes depuis le champ image des produits
        $products = DB::table('products')->whereNotNull('image')->where('image', '!=', '')->get();
        foreach ($products as $product) {
            DB::table('images')->insert([
                'produit_id' => $product->id,
                'url' => $product->image,
                'alt_text' => $product->name,
                'ordre' => 0,
                'est_principale' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
