<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mt-10">
            <div class="hidden md:block md:col-span-4"></div>

            <div class="col-span-1 md:col-span-8">
                <div class="flex flex-col items-end gap-3">
                    <x-filament::button 
                        type="submit" 
                        size="xl"
                        class="w-full bg-black text-white hover:bg-gray-900 border border-white/10 transition-all duration-300 tracking-[0.3em] uppercase text-[10px] font-bold py-4 rounded-none shadow-[4px_4px_0px_0px_rgba(255,255,255,0.05)] hover:shadow-none"
                    >
                        Simpan Perubahan
                    </x-filament::button>
                </div>
            </div>
        </div>
    </form>
</x-filament-panels::page>