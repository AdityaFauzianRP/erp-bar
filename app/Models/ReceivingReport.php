<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceivingReport extends Model
{
    //

    protected $fillable = [
        'purchase_id',
        'receive_number',
        'received_date',
        'delivery_note_number',
        'received_by',
        'notes'
    ];
    protected $casts = [
        'delivery_note_number' => 'array',
    ];

    public function items()
    {
        return $this->hasMany(ReceivingItem::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function user() // Kita beri nama 'user' agar sesuai dengan Resource
    {
        return $this->belongsTo(\App\Models\User::class, 'received_by');
    }

    // Atau jika kamu mau tetap menggunakan nama 'receivedBy'
    public function receivedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'received_by');
    }

    protected static function booted()
    {
        static::created(function ($receivingReport) {
            // Ambil data Purchase terkait
            $purchase = $receivingReport->purchase;

            if ($purchase) {
                // Ubah status menjadi 'Proses Penerimaan'
                $purchase->update([
                    'status' => 'Proses Penerimaan'
                ]);
            }
        });
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
