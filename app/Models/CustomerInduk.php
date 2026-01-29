<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerInduk extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $lastNumber = static::max('id') ?? 0;
            $model->code = 'CUS-' . date('Y') . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        });
    }

    public function branches(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CustomerBranch::class, 'customer_induk_id');
    }

    public function customerPrices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CustomerPrice::class, 'customer_induk_id');
    }
}
