<?php

namespace App\Filament\Resources\SalesDetailReports;

use App\Filament\Resources\SalesDetailReports\Pages\CreateSalesDetailReport;
use App\Filament\Resources\SalesDetailReports\Pages\EditSalesDetailReport;
use App\Filament\Resources\SalesDetailReports\Pages\ListSalesDetailReports;
use App\Filament\Resources\SalesDetailReports\Pages\ViewSalesDetailReport;
use App\Filament\Resources\SalesDetailReports\Schemas\SalesDetailReportForm;
use App\Filament\Resources\SalesDetailReports\Schemas\SalesDetailReportInfolist;
use App\Filament\Resources\SalesDetailReports\Tables\SalesDetailReportsTable;
use App\Models\SalesDetailReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SalesDetailReportResource extends Resource
{
    protected static ?string $model = SalesDetailReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    // grup laporan 

    protected static ?string $navigationLabel = 'Laporan Detail Penjualan';
    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $modelLabel = 'Laporan Detail Penjualan';
    protected static ?string $pluralModelLabel = 'Laporan Detail Penjualan';

    public static function form(Schema $schema): Schema
    {
        return SalesDetailReportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SalesDetailReportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalesDetailReportsTable::configure($table);
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
            'index' => ListSalesDetailReports::route('/'),
        ];
    }
}
