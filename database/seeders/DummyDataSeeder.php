<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\CustomerInduk;
use App\Models\CustomerGroup;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::first();

        // Unit
        $unitPcs = Unit::firstOrCreate(['name' => 'PCS', 'code' => 'PCS']);
        $unitKg = Unit::firstOrCreate(['name' => 'KG', 'code' => 'KG']);

        // Supplier
        $supplier = Supplier::firstOrCreate(['name' => 'PT Suplai Makmur'], [
            'address' => 'Jl. Industri No 1',
            'phone' => '08111222333',
        ]);

        // Product
        for ($i = 1; $i <= 10; $i++) {
            Product::firstOrCreate(['name' => "Produk Dummy $i"], [
                'code' => "SKU00$i",
                'hpp' => rand(10000, 50000),
                'unit_id' => $unitPcs->id,
            ]);
        }

        // Transactions (Pengeluaran)
        for ($i = 1; $i <= 10; $i++) {
            Transaction::create([
                'nomor_transaksi' => 'TRX-' . now()->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'tanggal_transaksi' => now()->subDays(rand(1, 30)),
                'kategori' => 'OPX',
                'metode_pembayaran' => 'Transfer',
                'status_bayar' => ['Lunas', 'Belum Dibayar', 'Menunggu Konfirmasi Pembayaran'][rand(0, 2)],
                'total_akhir' => rand(100000, 5000000),
                'supplier_id' => $supplier->id,
                'user_id' => \App\Models\User::first()->id ?? 1,
            ]);
        }

        // Purchases
        for ($i = 1; $i <= 5; $i++) {
            Purchase::create([
                'po_number' => 'PO' . now()->format('ymd') . str_pad($i, 4, '0', STR_PAD_LEFT),
                'date' => now()->subDays(rand(1, 10)),
                'supplier_id' => $supplier->id,
                'branch_id' => $branch->id ?? null,
            ]);
        }
    }
}
