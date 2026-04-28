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
        Schema::create('receiving_reports', function (Blueprint $table) {
            $table->id();
            // Relasi ke Purchase Order [cite: 3]
            $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();

            $table->string('receive_number')->unique(); // Contoh: GR/2026/01/001
            $table->date('received_date');
            $table->string('delivery_note_number')->nullable(); // No Surat Jalan Vendor

            // Siapa yang menerima di gudang
            $table->foreignId('received_by')->constrained('users');

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receiving_reports');
    }
};
