<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->string('pdf_path')->nullable();
            $table->decimal('subtotal_ht', 10, 2);
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('total_ttc', 10, 2);
            $table->string('status')->default('generated');
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
