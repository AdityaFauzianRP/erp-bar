<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_induk_id',
        'branch_name',
        'city',
        'address',
        'phone',
        'is_active',
    ];

    /**
     * Relasi ke Customer Induk
     */
    public function customerInduk(): BelongsTo
    {
        return $this->belongsTo(CustomerInduk::class, 'customer_induk_id');
    }
}