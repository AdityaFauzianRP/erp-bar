<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Branch extends Model
{
    /** @use HasFactory<\Database\Factories\BranchFactory> */
    use HasFactory;

    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'name',
        'code',
        'address',
        'is_active',
    ];

    /**
     * Relasi ke User (Satu cabang bisa punya banyak staff/admin)
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relasi ke Product melalui tabel pivot product_supplier.
     * Ini berguna jika kita ingin melihat produk apa saja yang tersedia 
     * di cabang ini beserta informasi harganya.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_supplier')
            ->withPivot(['supplier_id', 'harga_beli_khusus', 'sku_supplier'])
            ->withTimestamps();
    }

    public function suppliers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'branch_supplier');
    }
}
