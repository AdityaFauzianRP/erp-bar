<!-- resources/views/filament/pages/dashboard.blade.php -->
<x-filament::page>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- Example widget placeholders -->
        <x-filament::widget-card>
            <x-slot name="title">Selamat Datang</x-slot>
            <p>Anda berhasil masuk ke sistem ERP.</p>
        </x-filament::widget-card>
        <x-filament::widget-card>
            <x-slot name="title">Ringkasan Hari Ini</x-slot>
            <p>Statistik singkat dapat ditampilkan di sini.</p>
        </x-filament::widget-card>
        <x-filament::widget-card>
            <x-slot name="title">Aktivitas Terbaru</x-slot>
            <p>Daftar aktivitas terbaru...</p>
        </x-filament::widget-card>
    </div>
</x-filament::page>
