<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationalExpenseItem extends Model
{
    // Kita nonaktifkan timestamps karena biasanya data detail mengikuti header
    public $timestamps = false;

    protected $fillable = [
        'operational_expense_id',
        'operational_product_id',
        'qty',
        'price',
        'subtotal',
        'category_id',
        'description',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relasi balik ke Header
    public function expense(): BelongsTo
    {
        return $this->belongsTo(OperationalExpense::class, 'operational_expense_id');
    }

    // Relasi ke Produk Operasional untuk ambil Nama/Satuan
    public function product(): BelongsTo
    {
        return $this->belongsTo(OperationalProduct::class, 'operational_product_id');
    }
}