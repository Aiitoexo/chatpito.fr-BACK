<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('description_courte', 500)->nullable()->after('description');
            $table->text('description_longue')->nullable()->after('description_courte');
            $table->string('marque', 255)->nullable()->after('description_longue');
            $table->boolean('actif')->default(true)->after('featured');
            $table->string('meta_title', 255)->nullable()->after('actif');
            $table->text('meta_description')->nullable()->after('meta_title');
        });

        // Migrer les données existantes
        DB::statement('UPDATE products SET description_courte = LEFT(description, 500), description_longue = description');
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['description_courte', 'description_longue', 'marque', 'actif', 'meta_title', 'meta_description']);
        });
    }
};
