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
        Schema::create('t_transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi')->unique();
            $table->date('tanggal_transaksi');
            $table->string('kategori'); // PO, PI, OPX

            // --- PEMBAYARAN & REKENING ---
            $table->string('metode_pembayaran'); // Cash, Transfer, Cicilan
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts');
            $table->date('tenggat_waktu')->nullable(); // Untuk metode Transfer/Tempo

            // --- LOGIKA CICILAN ---
            $table->integer('tenor')->default(1); // Berapa kali cicil (misal: 12)
            $table->string('interval_cicilan')->nullable(); // Bulan, Minggu, Hari

            // --- FINANCIALS ---
            $table->decimal('total_akhir', 15, 2)->default(0);
            $table->decimal('jumlah_terbayar', 15, 2)->default(0); // Uang yang sudah masuk/keluar
            $table->string('status_bayar')->default('Belum Dibayar'); // Belum Dibayar, Dicicil, Lunas

            // ... field lainnya (purchase_id, user_id, dll) tetap sama
            $table->foreignId('purchase_id')->nullable()->constrained('purchases');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
