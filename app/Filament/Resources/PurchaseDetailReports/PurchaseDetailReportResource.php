<?php

namespace App\Filament\Resources\PurchaseDetailReports;

use App\Filament\Resources\PurchaseDetailReports\Pages\CreatePurchaseDetailReport;
use App\Filament\Resources\PurchaseDetailReports\Pages\EditPurchaseDetailReport;
use App\Filament\Resources\PurchaseDetailReports\Pages\ListPurchaseDetailReports;
use App\Filament\Resources\PurchaseDetailReports\Pages\ViewPurchaseDetailReport;
use App\Filament\Resources\PurchaseDetailReports\Schemas\PurchaseDetailReportForm;
use App\Filament\Resources\PurchaseDetailReports\Schemas\PurchaseDetailReportInfolist;
use App\Filament\Resources\PurchaseDetailReports\Tables\PurchaseDetailReportsTable;
use App\Models\PurchaseDetailReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PurchaseDetailReportResource extends Resource
{
    protected static ?string $model = PurchaseDetailReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptRefund;

    protected static ?string $navigationLabel = 'Laporan Detail Pembelian';
    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $modelLabel = 'Laporan Detail Pembelian';
    protected static ?string $pluralModelLabel = 'Laporan Detail Pembelian';

    public static function form(Schema $schema): Schema
    {
        return PurchaseDetailReportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PurchaseDetailReportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PurchaseDetailReportsTable::configure($table);
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
            'index' => ListPurchaseDetailReports::route('/'),
        ];
    }
}
