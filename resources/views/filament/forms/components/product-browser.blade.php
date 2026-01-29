@php
    // Mengambil ID Supplier dari form utama
    $supplierId = $get('../../supplier_id');
    
    // Mengambil input pencarian dari field 'search' di dalam modal
    $search = $get('search');

    // Query produk berdasarkan supplier dan pencarian
    $products = \App\Models\Product::query()
        ->whereHas('suppliers', function ($q) use ($supplierId) {
            $q->where('supplier_id', $supplierId);
        })
        ->when($search, function ($q) use ($search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        })
        ->with(['unit', 'suppliers'])
        ->get();
@endphp

<div class="overflow-x-auto border border-gray-200 rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Item</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($products as $product)
                <tr class="hover:bg-green-50 cursor-pointer border-b" 
                    x-on:click="
                        @php
                            $harga = $product->suppliers->where('id', $supplierId)->first()?->pivot->harga_beli_khusus ?? 0;
                            $unit = $product->unit->name ?? 'pcs';
                            $path = $getStatePath(); 
                            $basePath = str_replace('product_browser', '', $path);
                        @endphp

                        $wire.set('{{ $basePath }}product_id', '{{ $product->id }}');
                        $wire.set('{{ $basePath }}product_name', '{{ $product->name }}');
                        $wire.set('{{ $basePath }}unit_name', '{{ $unit }}');
                        $wire.set('{{ $basePath }}unit_price', {{ $harga }});
                        
                        {{-- Hitung subtotal: harga * quantity yang sedang diinput --}}
                        $wire.set('{{ $basePath }}subtotal', {{ $harga }} * ($wire.get('{{ $basePath }}quantity') || 1));

                        close(); 
                    ">
                    <td class="px-4 py-2 text-sm text-blue-600 font-mono">{{ $product->code }}</td>
                    <td class="px-4 py-2 text-sm font-bold text-gray-900">{{ $product->name }}</td>
                    <td class="px-4 py-2 text-sm text-gray-500">{{ $product->unit->name ?? '-' }}</td>
                    <td class="px-4 py-2 text-right">
                        <span class="text-green-600 font-bold">Pilih +</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                        Tidak ada produk ditemukan untuk supplier ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>