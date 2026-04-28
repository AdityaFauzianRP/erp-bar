<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionSchedule extends Model
{
    use HasFactory;

    protected $table = 't_transaksi_jadwal';

    protected $guarded = [];

    protected $casts = [
        'tanggal_jatuh_tempo' => 'date',
        'nominal' => 'decimal:2',
    ];

    /**
     * Relasi balik ke Transaksi Induk
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}