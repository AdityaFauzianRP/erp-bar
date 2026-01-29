<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'description', 'is_active'];

    protected static function booted()
    {
        static::creating(function ($category) {
            if (empty($category->code)) {
                $lastRecord = self::orderBy('id', 'desc')->first();
                $nextId = $lastRecord ? $lastRecord->id + 1 : 1;
                $category->code = 'EXP-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}