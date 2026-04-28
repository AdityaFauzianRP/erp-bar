<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerProductPrice extends Model
{
    protected $fillable = [
        'customer_induk_id',
        'product_id',
        'special_price',
    ];

    public function customerInduk(): BelongsTo
    {
        return $this->belongsTo(CustomerInduk::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
