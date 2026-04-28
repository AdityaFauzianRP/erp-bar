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
        Schema::create('t_transaksi_jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('t_transaksi')->onDelete('cascade');
            $table->integer('cicilan_ke');
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal_jatuh_tempo');
            $table->string('status')->default('Belum Bayar'); // Belum Bayar, Lunas
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_schedules');
    }
};
