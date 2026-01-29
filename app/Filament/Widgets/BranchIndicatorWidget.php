<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class BranchIndicatorWidget extends BaseWidget
{
    // Mengatur agar hanya ada 2 kolom dalam satu baris
    protected int | array | null $columns = 2;

    protected static ?int $sort = -2;

    protected function getStats(): array
    {
        $user = Auth::user();
        $activeBranch = $user->activeBranch;

        return [
            // 1. Stat Nama Akun & Email
            Stat::make('Selamat Datang,', $user->name)
                ->description($user->email)
                ->descriptionIcon('heroicon-m-user-circle')
                ->color('primary') // Warna biru biar elegan
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-blue-50/50 transition border-l-4 border-blue-500',
                    'onclick' => "window.location.href='/erp/edit-profile'",
                ]),

            // 2. Stat Lokasi Kerja (Indikator Cabang)
            Stat::make('Lokasi Kerja Aktif', $activeBranch?->name ?? 'Belum Dipilih')
                ->description($activeBranch ? "Kode: {$activeBranch->code}" : 'Mohon atur cabang aktif di profil')
                ->descriptionIcon($activeBranch ? 'heroicon-m-building-office-2' : 'heroicon-m-exclamation-circle')
                ->color($activeBranch ? 'success' : 'danger')
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-gray-50 transition border-l-4 ' . ($activeBranch ? 'border-green-500' : 'border-red-500'),
                    'onclick' => "window.location.href='/erp/edit-profile'",
                ]),
        ];
    }
}