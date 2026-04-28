<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationalProduct extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category_id',
        'unit_id',
        'is_active',
    ];

    // Relasi ke ExpenseCategory
    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    // Relasi ke Unit
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
