<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    use HasFactory;

    protected $table = 'customer_groups';

    protected $fillable = [
        'name',
        'code',
        'description',
        'price_package',
        'is_active'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->code)) {
                // Ambil nomor terakhir dari database
                $lastRecord = static::orderBy('id', 'desc')->first();
                $lastNumber = $lastRecord ? (int) substr($lastRecord->code, -4) : 0;

                // Generate format: CUST-G- diikuti 4 digit angka
                $model->code = 'CUST-G-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
