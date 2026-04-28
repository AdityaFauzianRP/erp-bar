<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameItem extends Model
{
    protected $fillable = [
        'stock_opname_id',
        'product_id',
        'branch_id',
        'system_stock',
        'physical_stock',
        'difference',
        'waster_qty',
        'waster_price',
        'waster_total_price',
        'note'
    ];

    protected $casts = [
        'physical_stock' => 'decimal:2',
        'waster_qty' => 'decimal:5',
        'difference' => 'decimal:2',
        'waster_price' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    protected static function booted()
    {
        static::creating(function ($item) {
            // 1. Hitung selisih otomatis
            // $item->difference = (int)($item->physical_stock ?? 0) - (int)($item->system_stock ?? 0);

            // 2. Ambil branch_id otomatis dari tabel parent (StockOpname)
            // Jika branch_id tidak dikirim dari form, ambil dari data induknya
            if (!$item->branch_id && $item->stock_opname_id) {
                $item->branch_id = $item->stockOpname?->branch_id ?? 1;
            }
        });
    }

    // Pastikan kamu punya relasi ini di bawahnya
    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class);
    }
}
