<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    // Mengizinkan pengisian massal untuk kolom-kolom kunci
    protected $fillable = [
        'warehouse_id',
        'product_id',
        'branch_id',
        'stock',
        'min_stock',
        'bin_location',
    ];

    /**
     * Relasi ke Master Produk
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke Master Gudang
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Relasi ke PT (Branch/Cabang) sebagai pemilik barang
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->where('branch_id', auth()->user()->active_branch_id);
    // }
}
