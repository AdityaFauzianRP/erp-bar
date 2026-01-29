<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    // Mass assignment protection
    protected $fillable = [
        'code',
        'name',
        'location',
        'is_active',
        'description',
    ];

    /**
     * Relasi ke tabel Inventory.
     * Satu gudang memiliki banyak catatan stok barang.
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    /**
     * Opsi Tambahan: Jika ingin melihat histori transaksi khusus di gudang ini.
     */
    // public function inventoryHistories(): HasMany
    // {
    //     return $this->hasMany(InventoryHistory::class);
    // }
}