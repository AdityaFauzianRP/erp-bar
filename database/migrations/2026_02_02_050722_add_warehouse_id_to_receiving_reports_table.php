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
        Schema::table('receiving_reports', function (Blueprint $table) {
            // Tambahkan foreign key ke tabel warehouses
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('receiving_reports', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn('warehouse_id');
        });
    }
};
