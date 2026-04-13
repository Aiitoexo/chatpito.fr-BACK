<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nutritional_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->unique()->constrained('products')->onDelete('cascade');
            $table->text('ingredients')->nullable();
            $table->text('allergenes')->nullable();
            $table->text('traces')->nullable();
            $table->decimal('energie_kcal', 8, 2)->nullable();
            $table->decimal('energie_kj', 8, 2)->nullable();
            $table->decimal('matieres_grasses', 8, 2)->nullable();
            $table->decimal('dont_acides_gras_satures', 8, 2)->nullable();
            $table->decimal('glucides', 8, 2)->nullable();
            $table->decimal('dont_sucres', 8, 2)->nullable();
            $table->decimal('proteines', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nutritional_infos');
    }
};
