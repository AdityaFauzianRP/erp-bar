<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'hpp',
        'harga_jual_default',
        'is_active'
    ];

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->code)) {
                // Contoh Format: PRD-202401-0001
                $prefix = 'PRD-' . date('Ym') . '-';

                // Cari nomor urut terakhir pada bulan yang sama
                $lastProduct = self::where('code', 'LIKE', $prefix . '%')
                    ->orderBy('code', 'desc')
                    ->first();

                if ($lastProduct) {
                    $lastNumber = intval(substr($lastProduct->code, -4));
                    $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $newNumber = '0001';
                }

                $product->code = $prefix . $newNumber;
            }
        });
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier')
            ->withPivot('harga_beli_khusus', 'sku_supplier')
            ->withTimestamps();
    }

    public function customerPrices()
    {
        return $this->hasMany(CustomerPrice::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function inventories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Inventory::class);
    }
}
