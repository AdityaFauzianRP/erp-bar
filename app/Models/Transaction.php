<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 't_transaksi';

    protected $guarded = [];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'tenggat_waktu' => 'date',
        'subtotal' => 'decimal:2',
        'pajak' => 'decimal:2',
        'total_akhir' => 'decimal:2',
        'jumlah_terbayar' => 'decimal:2',
    ];

    /**
     * Relasi ke Rekening Bank Internal
     */
    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    /**
     * Relasi ke Purchase Order (Jika ada)
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    /**
     * Relasi ke Supplier (Jika ada)
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Relasi ke User penginput
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Jadwal Cicilan (Jika metode = Cicilan)
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(TransactionSchedule::class, 'transaction_id');
    }

    
}