<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class WasteReport extends Model
{
    /** Terhubung ke VIEW v_laporan_qty_rusak */
    protected $table = 'v_laporan_qty_rusak';

    /**
     * Jika MySQL 8.0+ (VIEW punya kolom `No` dari ROW_NUMBER()),
     * jadikan `No` sebagai primary key non-increment.
     * Jika DB 5.7 (VIEW tanpa `No`), lihat catatan di bawah.
     */
    protected $primaryKey = 'No';
    public $incrementing  = false;
    protected $keyType    = 'int';

    /** VIEW tidak memiliki timestamps */
    public $timestamps = false;

    /** Read-only model */
    protected $guarded = [];

    /* ======================
       SCOPES YANG BERGUNA
       ====================== */

    /** Filter periode tanggal */
    public function scopeBetweenDate(Builder $q, string $start, string $end): Builder
    {
        return $q->whereBetween('Tanggal', [$start, $end]);
    }

    /** Filter kategori: Penjualan / Pembelian / Gudang */
    public function scopeKategori(Builder $q, string $kategori): Builder
    {
        return $q->where('Kategori', $kategori);
    }

    /** Pencarian cepat berdasarkan produk/satuan */
    public function scopeSearch(Builder $q, string $kw): Builder
    {
        $like = '%' . $kw . '%';

        return $q->where(function (Builder $qq) use ($like) {
            $qq->where('Produk', 'like', $like)
               ->orWhere('Satuan', 'like', $like);
        });
    }

    /** Urutan default */
    public function scopeDefaultOrder(Builder $q): Builder
    {
        return $q->orderBy('Tanggal')
                 ->orderBy('Kategori')
                 ->orderBy('Produk')
                 ->orderBy('No');
    }
}