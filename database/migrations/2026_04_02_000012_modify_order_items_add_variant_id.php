<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('variant_id')->nullable()->after('product_id')->constrained('variants')->onDelete('set null');
            $table->string('product_name', 255)->nullable()->after('variant_id');
            $table->string('variant_name', 255)->nullable()->after('product_name');
        });

        // Mapper les order_items existants vers les variantes LEGACY
        $items = DB::table('order_items')->get();
        foreach ($items as $item) {
            $variant = DB::table('variants')
                ->where('produit_id', $item->product_id)
                ->where('sku', 'like', 'LEGACY-%')
                ->first();

            if ($variant) {
                $product = DB::table('products')->find($item->product_id);
                DB::table('order_items')->where('id', $item->id)->update([
                    'variant_id' => $variant->id,
                    'product_name' => $product->name ?? null,
                    'variant_name' => 'Standard',
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['variant_id']);
            $table->dropColumn(['variant_id', 'product_name', 'variant_name']);
        });
    }
};
