<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealisasiItemReport extends Model
{
    // Hubungkan ke nama VIEW di database
    protected $table = 'v_laporan_realisasi_item';

    // Karena ini VIEW, kita matikan fitur penulisan data
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = 'id_key'; // Menggunakan id_key dari view
    
    protected $casts = [
        'tanggal' => 'date',
        'qty_pesanan' => 'float',
        'qty_dikirim' => 'float',
        'qty_bagus' => 'float',
        'sisa_kirim' => 'float',
        'persentase_realisasi' => 'float',
    ];
}