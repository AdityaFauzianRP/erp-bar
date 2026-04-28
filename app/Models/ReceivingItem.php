<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceivingItem extends Model
{
    protected $fillable = [
        'receiving_report_id',
        'purchase_item_id',
        'product_id',
        'qty_received',
        'qty_rejected',
        'reject_reason'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted()
    {
        static::creating(function ($item) {
            if (empty($item->product_id) && !empty($item->purchase_item_id)) {
                $purchaseItem = \App\Models\PurchaseItem::find($item->purchase_item_id);
                $item->product_id = $purchaseItem?->product_id;
            }
        });
    }

    public function receivingReport(): BelongsTo
    {
        // Pastikan nama foreign key sesuai, biasanya 'receiving_report_id'
        return $this->belongsTo(ReceivingReport::class, 'receiving_report_id');
    }

    /**
     * Relasi ke Purchase Item (Opsional tapi berguna untuk tracing)
     */
    public function purchaseItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseItem::class, 'purchase_item_id');
    }

    public function receiving()
{
    return $this->belongsTo(ReceivingReport::class, 'receiving_id');
}
}
