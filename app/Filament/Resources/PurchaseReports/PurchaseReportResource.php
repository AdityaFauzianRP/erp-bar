<?php

namespace App\Filament\Resources\PurchaseReports;

use App\Filament\Resources\PurchaseReports\Pages\CreatePurchaseReport;
use App\Filament\Resources\PurchaseReports\Pages\EditPurchaseReport;
use App\Filament\Resources\PurchaseReports\Pages\ListPurchaseReports;
use App\Filament\Resources\PurchaseReports\Pages\ViewPurchaseReport;
use App\Filament\Resources\PurchaseReports\Schemas\PurchaseReportForm;
use App\Filament\Resources\PurchaseReports\Schemas\PurchaseReportInfolist;
use App\Filament\Resources\PurchaseReports\Tables\PurchaseReportsTable;
use App\Models\PurchaseReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PurchaseReportResource extends Resource
{
    protected static ?string $model = PurchaseReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static ?string $navigationLabel = 'Laporan Pembelian';
    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $modelLabel = 'Laporan Pembelian';
    protected static ?string $pluralModelLabel = 'Laporan Pembelian';

    public static function form(Schema $schema): Schema
    {
        return PurchaseReportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PurchaseReportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PurchaseReportsTable::configure($table);
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
            'index' => ListPurchaseReports::route('/'),
        ];
    }
}
