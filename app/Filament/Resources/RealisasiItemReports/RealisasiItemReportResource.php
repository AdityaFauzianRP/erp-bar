<?php

namespace App\Filament\Resources\RealisasiItemReports;

use App\Filament\Resources\RealisasiItemReports\Pages\ManageRealisasiItemReports;
use App\Models\RealisasiItemReport;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RealisasiItemReportResource extends Resource
{
    protected static ?string $model = RealisasiItemReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $navigationLabel = 'Laporan Realisasi Order';

    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('nama_barang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('satuan'),

                TextColumn::make('qty_pesanan')
                    ->numeric()
                    ->alignCenter(),

                TextColumn::make('qty_dikirim')
                    ->numeric()
                    ->alignCenter(),

                TextColumn::make('qty_bagus')
                    ->numeric()
                    ->alignCenter()
                    ->color('success'),

                TextColumn::make('sisa_kirim')
                    ->numeric()
                    ->alignCenter()
                    ->color(fn($state) => $state > 0 ? 'danger' : 'gray'),

                TextColumn::make('persentase_realisasi')
                    ->label('% Realisasi')
                    ->suffix('%')
                    ->badge()
                    ->color(fn(float $state): string => match (true) {
                        $state >= 100 => 'success',
                        $state >= 50 => 'warning',
                        default => 'danger',
                    }),
            ])
            ->filters([
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['dari_tanggal'], fn($q) => $q->whereDate('tanggal', '>=', $data['dari_tanggal']))
                            ->when($data['sampai_tanggal'], fn($q) => $q->whereDate('tanggal', '<=', $data['sampai_tanggal']));
                    })
                    ->columns(2)
                    ->columnSpanFull(),
                    
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->actions([]) // Kosongkan karena ini hanya laporan
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageRealisasiItemReports::route('/'),
        ];
    }
}
