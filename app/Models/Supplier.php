<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'pic',
        'phone',
        'address',
        'bank_name',
        'bank_account_number',
        'is_active'
    ];

    protected static function booted()
    {
        static::creating(function ($supplier) {
            if (empty($supplier->code)) {
                $lastRecord = self::orderBy('id', 'desc')->first();
                $nextId = $lastRecord ? $lastRecord->id + 1 : 1;
                $supplier->code = 'SUP-' . date('Y') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function branches(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'branch_supplier');
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_supplier')
            ->withPivot([
                'branch_id',
                'harga_beli_khusus',
                'sku_supplier'
            ])
            ->withTimestamps();
    }

    public function product_suppliers()
    {
        return $this->hasMany(\App\Models\ProductSupplier::class);
    }


}
