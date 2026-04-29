<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'price_package',
        'description',
        'is_active',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->code)) {
                $prefix = 'SCH-';
                $lastRecord = self::where('code', 'LIKE', $prefix . '%')
                    ->orderBy('code', 'desc')
                    ->first();

                if ($lastRecord) {
                    $lastNumber = intval(substr($lastRecord->code, -4));
                    $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $newNumber = '0001';
                }

                $model->code = $prefix . $newNumber;
            }
        });
    }
}
