<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('receiving_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receiving_report_id')->constrained('receiving_reports')->cascadeOnDelete();

            // Relasi ke item PO spesifik 
            $table->foreignId('purchase_item_id')->constrained('purchase_items')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');

            // Qty Realisasi (Yang Bagus)
            $table->decimal('qty_received', 12, 2)->default(0);

            // Qty Reject (Yang Rusak/Hilang - Tidak masuk stok)
            $table->decimal('qty_rejected', 12, 2)->default(0);

            $table->string('reject_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receiving_items');
    }
};
