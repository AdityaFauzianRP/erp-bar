<?php

namespace App\Filament\Resources\ExecSummaryFacts\Tables;

use App\Exports\ExecutiveSummaryExport;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class ExecSummaryFactsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->header(function ($livewire) {
                // Kita ambil data berdasarkan state internal Livewire (bukan dari filter table lagi)
                $dari = $livewire->tableFilters['Periode']['dari'] ?? null;
                $sampai = $livewire->tableFilters['Periode']['sampai'] ?? null;

                $data = null;
                if ($dari && $sampai) {
                    $data = \App\Models\ExecSummaryFact::getSummary($dari, $sampai);
                }

                return view('filament.exec-summary.stats-overview', [
                    'data' => $data,
                    'dari' => $dari,
                    'sampai' => $sampai
                ]);
            })
            ->columns([])
            ->filters([]) ;// Kosongkan ini
            // Biarkan action ini untuk trigger download
    }
}
