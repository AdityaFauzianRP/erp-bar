<?php

namespace App\Filament\Resources\TravelRequests\Pages;

use App\Filament\Resources\TravelRequests\TravelRequestResource;
use App\Models\OperationalExpenseItem;
use App\Models\OperationalProduct;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateTravelRequest extends CreateRecord
{
    protected static string $resource = TravelRequestResource::class;

    // Properti Publik agar terbaca oleh Blade
    public $search = '';
    public $items = [];
    public $selectedCategoryId = null; // Filter modal berdasarkan kategori baris
    public $activeRowIndex = null;    // Melacak baris mana yang sedang dipilih barangnya

    /**
     * Menambah baris kosong ke tabel
     * User harus pilih kategori dulu di baris ini baru bisa pilih barang
     */
    public function addNewRow()
    {
        $this->items[] = [
            'category_id' => null,
            'product_id' => null,
            'name' => '',
            'unit_name' => '',
            'qty' => 1,
            'description' => '',
            'price' => 0,
        ];
    }

    /**
     * Meriset pilihan barang tanpa menghapus baris
     */
    public function resetItem($index)
    {
        $this->items[$index]['product_id'] = null;
        $this->items[$index]['name'] = '';
        $this->items[$index]['unit_name'] = '';
    }

    /**
     * Computed Property: Mengambil daftar produk untuk modal picker
     * Otomatis terfilter berdasarkan $selectedCategoryId yang dikirim dari JS di Blade
     */
    public function getProductsProperty()
    {
        if (!$this->selectedCategoryId) {
            return collect();
        }

        $query = OperationalProduct::where('is_active', true)
            ->where('category_id', $this->selectedCategoryId);

        if (strlen($this->search) > 0) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        return $query->limit(15)->get();
    }

    /**
     * Fungsi yang dipanggil saat user klik "Pilih" di Modal
     */
    public function selectProduct($productId)
    {
        $product = OperationalProduct::with('unit')->find($productId);

        if ($product && $this->activeRowIndex !== null) {
            $this->items[$this->activeRowIndex]['product_id'] = $product->id;
            $this->items[$this->activeRowIndex]['name'] = $product->name;
            $this->items[$this->activeRowIndex]['unit_name'] = $product->unit->name ?? '-';
            // Optional: Jika ingin harga otomatis dari master produk
            // $this->items[$this->activeRowIndex]['price'] = $product->price;
        }

        // Reset state modal agar bersih kembali
        $this->activeRowIndex = null;
        $this->selectedCategoryId = null;
        $this->search = '';
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    /**
     * Logic Simpan ke Database
     */
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return DB::transaction(function () use ($data) {
            // 1. Hitung total amount dari semua item
            $total = 0;
            foreach ($this->items as $item) {
                $total += ($item['qty'] * $item['price']);
            }

            $data['total_amount'] = $total;
            $data['status'] = 'Draft';
            $data['is_request'] = 1; // Tandai ini sebagai request, bukan data final

            // 2. Simpan Header (OperationalExpense)
            $record = parent::handleRecordCreation($data);

            // 3. Simpan Detail (OperationalExpenseItem)
            foreach ($this->items as $item) {
                // Jangan simpan jika product_id belum dipilih
                if (!$item['product_id']) continue;

                OperationalExpenseItem::create([
                    'operational_expense_id' => $record->id,
                    'category_id'            => $item['category_id'], // Simpan kategori per item
                    'operational_product_id' => $item['product_id'],
                    'qty'                    => $item['qty'],
                    'price'                  => $item['price'],
                    'description'            => $item['description'],
                    'subtotal'               => $item['qty'] * $item['price'],
                    'status'                 => 'approved', // Default untuk item baru
                ]);
            }

            return $record;
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan Data ');
    }
    

    // 2. Menghilangkan Tombol "Create & Create Another"
    protected function getCreateAnotherFormAction(): Action
    {
        // Kita buat action kosong (empty) agar tidak muncul di view
        return parent::getCreateAnotherFormAction()
            ->hidden();
    }

}
