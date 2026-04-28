<?php

namespace App\Filament\Resources\WasteReports;

use App\Filament\Resources\WasteReports\Pages\CreateWasteReport;
use App\Filament\Resources\WasteReports\Pages\EditWasteReport;
use App\Filament\Resources\WasteReports\Pages\ListWasteReports;
use App\Filament\Resources\WasteReports\Pages\ViewWasteReport;
use App\Filament\Resources\WasteReports\Schemas\WasteReportForm;
use App\Filament\Resources\WasteReports\Schemas\WasteReportInfolist;
use App\Filament\Resources\WasteReports\Tables\WasteReportsTable;
use App\Models\WasteReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WasteReportResource extends Resource
{
    protected static ?string $model = WasteReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBoxXMark;

        protected static ?string $navigationLabel = 'Laporan Barang Susak';
    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $modelLabel = 'Laporan Barang Susak';
    protected static ?string $pluralModelLabel = 'Laporan Barang Susak';

    public static function form(Schema $schema): Schema
    {
        return WasteReportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WasteReportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WasteReportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWasteReports::route('/'),
        ];
    }
}
