<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Gunakan Pivot atau Model biasa, tapi Model lebih fleksibel untuk Repeater
class ProductSupplier extends Model
{
    // Tentukan nama tabel pivot Anda (cek di database, biasanya snake_case jamak)
    protected $table = 'product_supplier'; 

    protected $fillable = [
        'supplier_id',
        'product_id',
        'sku_supplier',
        'harga_beli_khusus',
        'branch_id',
    ];

    // Relasi balik ke Product (Opsional, tapi membantu)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi balik ke Supplier (Opsional)
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}