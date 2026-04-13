<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('carrier');
            $table->decimal('base_price', 8, 2);
            $table->decimal('free_above', 8, 2)->nullable();
            $table->integer('min_weight_grams')->default(0);
            $table->integer('max_weight_grams')->nullable();
            $table->integer('estimated_days_min')->default(2);
            $table->integer('estimated_days_max')->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};
