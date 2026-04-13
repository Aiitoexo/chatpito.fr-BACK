<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variant_supplier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('variants')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('fournisseur_sku', 100)->nullable();
            $table->string('nom_produit_fournisseur', 255)->nullable();
            $table->decimal('prix_achat_ht', 10, 2);
            $table->decimal('taux_tva_achat', 5, 2)->default(5.50);
            $table->decimal('prix_achat_ttc', 10, 2);
            $table->string('devise', 3)->default('EUR');
            $table->integer('quantite_min_commande')->default(1);
            $table->integer('multiple_commande')->default(1);
            $table->integer('delai_livraison_jours')->nullable();
            $table->decimal('frais_livraison', 10, 2)->nullable();
            $table->string('url_produit_fournisseur', 500)->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();

            $table->unique(['variant_id', 'supplier_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variant_supplier');
    }
};
