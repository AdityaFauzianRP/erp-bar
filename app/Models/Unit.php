<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk mengizinkan input data
    protected $fillable = [
        'name', 
        'short_name'
    ];

    /**
     * Relasi balik ke Produk (Opsional tapi disarankan)
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}