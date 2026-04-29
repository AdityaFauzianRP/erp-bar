<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'unit_id',
        'is_active',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->code)) {
                $prefix = 'RM-' . date('Ym') . '-';
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

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
