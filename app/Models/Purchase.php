<?php

namespace App\Models;

use Carbon\Carbon;
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
            $datePart = $now->format('y/m/d');

            $carbonDate = Carbon::parse($now);

            $yearShort = $carbonDate->format('y');
            $month     = $carbonDate->format('m');
            $day       = $carbonDate->format('d');

            // Filter berdasarkan tahun dan bulan yang sama untuk reset counter tiap bulan
            $count = ProformaInvoice::whereYear('date', $carbonDate->year)
                ->whereMonth('date', $carbonDate->month)
                ->count();

            // Pad counter jadi 4 digit, misal: 0001
            $counter = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

            $countThisMonth = self::whereYear('created_at', $now->year)
                ->whereMonth('created_at', $now->month)
                ->count();

            $counter = str_pad($countThisMonth + 1, 4, '0', STR_PAD_LEFT);
            $model->po_number = "PO{$yearShort}{$month}{$day}{$counter}";
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

    public function branch()
    {
        return $this->belongsTo(Branch::class); // Sesuaikan dengan nama model Branch kamu
    }

    protected $casts = [
        'approved_at' => 'datetime',
        // 'created_at' => 'datetime', // Biasanya sudah otomatis, tapi boleh ditulis
        'due_date' => 'date',
        'image_direct' => 'array',
    ];

    public function transactions()
    {
        // Relasi ke tabel t_transaksi
        return $this->hasMany(\App\Models\Transaction::class, 'purchase_id');
    }
}
