<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    protected $table = 'assets';

    protected $fillable = [
        'asset_category_id',
        'nama_aset',
        'tanggal_beli',
        'jumlah',
        'harga_satuan',
        'total_harga',
        'status',
        'catatan',
    ];

    protected static function boot()
    {
        parent::boot();

        // Otomatis hitung total_harga sebelum save
        static::saving(function ($asset) {
            $asset->total_harga = ($asset->jumlah ?? 0) * ($asset->harga_satuan ?? 0);
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }
}