<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationalExpense extends Model
{
    protected $fillable = [
        'number',
        'date',
        'total_amount',
        'notes',
        'created_by',
        'user_id',
        'expense_category_id',
        'status',
        'approved_at',
        'approved_by',
        'attachment',
        'is_request',
    ];

    protected $casts = [
        'date' => 'datetime',
        'total_amount' => 'decimal:2',
        'attachment' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function booted()
    {
        static::creating(function ($expense) {
            if (!$expense->user_id) {
                $expense->user_id = auth()->id();
            }
            $expense->created_by = auth()->id();
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(OperationalExpenseItem::class);
    }

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // app/Models/OperationalExpense.php

}
