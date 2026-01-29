<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPrice extends Model
{
    protected $fillable = ['customer_induk_id', 'product_id', 'harga_khusus'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customerInduk()
    {
        return $this->belongsTo(CustomerInduk::class);
    }
}