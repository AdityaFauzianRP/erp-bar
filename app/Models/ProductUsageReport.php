<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductUsageReport extends Model
{
    // Hubungkan ke nama VIEW yang dibuat tadi
    protected $table = 'v_laporan_detail_pemakaian';

    // View biasanya read-only, jadi matikan timestamps bawaan Laravel
    public $timestamps = false;

    // Definisikan primary key jika ID produk unik di view ini
    protected $primaryKey = 'product_id';

    public function customer()
    {
        // Sesuaikan 'CustomerBrand' dengan nama class Model untuk tabel 'customer_brands'
        return $this->belongsTo(CustomerBrand::class, 'customer_brand_id');
    }
}
