<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Purchase extends Model
{

    use HasFactory;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // 1. Audit Trail: Created By
            $model->created_by = Auth::id();

            // Set Branch otomatis dari user yang login
            if (Auth::user() && Auth::user()->active_branch_id) {
                $model->branch_id = Auth::user()->active_branch_id;
            }

            // 2. Logika Penomoran: PO/Tahun/Bulan/Tanggal/Counter
            $now = now();
            $datePart = $now->format('Y/m/d');

            // Hitung PO di bulan & tahun ini untuk reset counter
            $countThisMonth = self::whereYear('created_at', $now->year)
                ->whereMonth('created_at', $now->month)
                ->count();

            $counter = str_pad($countThisMonth + 1, 4, '0', STR_PAD_LEFT);
            $model->po_number = "PO/{$datePart}/{$counter}";
        });

        static::updating(function ($model) {
            // Audit Trail: Edited By
            $model->edited_by = Auth::id();
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
    public function details(): HasMany
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_id');
    }

    protected $casts = [
        'approved_at' => 'datetime',
        // 'created_at' => 'datetime', // Biasanya sudah otomatis, tapi boleh ditulis
    ];
}
