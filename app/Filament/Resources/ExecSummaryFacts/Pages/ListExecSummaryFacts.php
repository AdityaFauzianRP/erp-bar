<?php

namespace App\Filament\Resources\ExecSummaryFacts\Pages;

use App\Filament\Resources\ExecSummaryFacts\ExecSummaryFactResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExecSummaryFacts extends ListRecords
{
    protected static string $resource = ExecSummaryFactResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         CreateAction::make(),
    //     ];
    // }

    public function mount(): void
    {
        parent::mount();

        $this->tableFilters['Periode']['dari'] = now()->startOfMonth()->format('Y-m-d');
        $this->tableFilters['Periode']['sampai'] = now()->format('Y-m-d');
    }

    // Di class ListRecords
    // Tambahkan type hint ?array agar sama dengan parent-nya
    public ?array $tableFilters = [
        'Periode' => [
            'dari' => null,
            'sampai' => null,
        ]
    ];

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('export_excel')
                ->label('Download Excel')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    // Ambil data filter langsung dari state Livewire
                    // Sesuaikan key nya dengan wire:model di Blade (tableFilters.Periode)
                    $filters = $this->tableFilters['Periode'] ?? [];

                    $dari = $filters['dari'] ?? null;
                    $sampai = $filters['sampai'] ?? null;

                    if (!$dari || !$sampai) {
                        \Filament\Notifications\Notification::make()
                            ->title('Pilih tanggal terlebih dahulu')
                            ->danger()
                            ->send();
                        return;
                    }

                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\ExecutiveSummaryExport($dari, $sampai),
                        "Executive-Summary-{$dari}-{$sampai}.xlsx"
                    );
                }),
        ];
    }
}
