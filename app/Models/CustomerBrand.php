<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerBrand extends Model
{
    protected $table = 'customer_brands';

    protected $fillable = [
        'customer_induk_id',
        'brand_name',
        'nama_cabang',
        'kota_cabang',
    ];

    public function customerInduk(): BelongsTo
    {
        return $this->belongsTo(CustomerInduk::class, 'customer_induk_id');
    }

    public function customer_induk()
    {
        // Brand merujuk ke satu Induk Customer
        return $this->belongsTo(CustomerInduk::class, 'customer_induk_id');
    }
}
