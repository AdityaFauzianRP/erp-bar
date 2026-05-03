<div x-data="{ state: @entangle($getStatePath()) }" class="w-full">
    <div class="overflow-x-auto border border-gray-200 dark:border-white/10 rounded-xl shadow-sm bg-white dark:bg-gray-900">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 dark:bg-white/5 text-gray-600 dark:text-gray-300 uppercase text-xs">
                <tr>
                    <th class="px-6 py-4 font-bold">Produk</th>
                    <th class="px-4 py-4 font-bold text-center">Qty PO</th>
                    <th class="px-4 py-4 font-bold text-center text-primary-600">Qty Bagus</th>
                    <th class="px-4 py-4 font-bold text-center text-danger-600">Qty Reject</th>
                    <th class="px-6 py-4 font-bold">Alasan Reject</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                <template x-for="(item, index) in state" :key="index">
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900 dark:text-white" x-text="item.product_name"></span>
                                <span class="text-xs text-gray-500 mt-1">
                                    Sisa Tunggu: <strong class="text-primary-600" x-text="item.qty_pending + ' ' + item.unit_name"></strong>
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center font-medium text-gray-600 dark:text-gray-400" x-text="item.qty_order"></td>
                        <td class="px-4 py-4">
                            <div class="flex justify-center">
                                <input type="number" 
                                    x-model="state[index].qty_received"
                                    class="w-24 text-center rounded-lg border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-gray-700 sm:text-sm"
                                    :max="item.qty_pending" 
                                    min="0"
                                    placeholder="0">
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex justify-center">
                                <input type="number" 
                                    x-model="state[index].qty_rejected"
                                    class="w-24 text-center rounded-lg border-gray-300 shadow-sm focus:ring-danger-500 focus:border-danger-500 dark:bg-gray-800 dark:border-gray-700 sm:text-sm"
                                    min="0"
                                    placeholder="0">
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <input type="text" 
                                x-model="state[index].reject_reason"
                                placeholder="Contoh: Kemasan Rusak"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-gray-700 sm:text-sm">
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <div x-show="!state || state.length === 0" class="flex flex-col items-center justify-center p-12 text-gray-500">
            <x-heroicon-o-shopping-cart class="w-12 h-12 mb-3 opacity-20" />
            <p class="text-sm italic">Belum ada item. Silakan pilih Referensi PO terlebih dahulu.</p>
        </div>
    </div>

    @error($getStatePath())
        <p class="mt-2 text-sm text-danger-600 font-medium">{{ $message }}</p>
    @enderror
</div>