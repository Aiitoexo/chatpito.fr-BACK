<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 255);
            $table->string('raison_sociale', 255)->nullable();
            $table->string('siret', 14)->nullable();
            $table->string('tva_intracommunautaire', 20)->nullable();
            $table->string('contact_nom', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('telephone', 20)->nullable();
            $table->text('adresse')->nullable();
            $table->string('code_postal', 10)->nullable();
            $table->string('ville', 100)->nullable();
            $table->string('pays', 100)->default('France');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
