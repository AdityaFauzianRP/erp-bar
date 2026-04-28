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
        Schema::table('t_transaksi', function (Blueprint $table) {
            // Tambahkan jika belum ada
            if (!Schema::hasColumn('t_transaksi', 'subtotal')) {
                $table->decimal('subtotal', 15, 2)->default(0)->after('metode_pembayaran');
            }
            if (!Schema::hasColumn('t_transaksi', 'pajak')) {
                $table->decimal('pajak', 15, 2)->default(0)->after('subtotal');
            }
            if (!Schema::hasColumn('t_transaksi', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('status_bayar');
            }

            // Tambahkan juga kolom untuk kebutuhan Transfer/Cicilan yang tadi dibahas
            if (!Schema::hasColumn('t_transaksi', 'tenggat_waktu')) {
                $table->date('tenggat_waktu')->nullable()->after('bank_account_id');
            }
            if (!Schema::hasColumn('t_transaksi', 'tenor')) {
                $table->integer('tenor')->default(1)->after('tenggat_waktu');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_transaksi', function (Blueprint $table) {
            //
        });
    }
};
