<?php

namespace App\Filament\Resources\OperationalReports;

use App\Filament\Resources\OperationalReports\Pages\CreateOperationalReport;
use App\Filament\Resources\OperationalReports\Pages\EditOperationalReport;
use App\Filament\Resources\OperationalReports\Pages\ListOperationalReports;
use App\Filament\Resources\OperationalReports\Pages\ViewOperationalReport;
use App\Filament\Resources\OperationalReports\Schemas\OperationalReportForm;
use App\Filament\Resources\OperationalReports\Schemas\OperationalReportInfolist;
use App\Filament\Resources\OperationalReports\Tables\OperationalReportsTable;
use App\Models\OperationalReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OperationalReportResource extends Resource
{
    protected static ?string $model = OperationalReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Laporan Operasional';
    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $modelLabel = 'Laporan Operasional';
    protected static ?string $pluralModelLabel = 'Laporan Operasional';

    public static function form(Schema $schema): Schema
    {
        return OperationalReportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OperationalReportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OperationalReportsTable::configure($table);
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
            'index' => ListOperationalReports::route('/'),
        ];
    }
}
