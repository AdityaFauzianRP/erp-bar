<?php

namespace App\Filament\Resources\OperationalItemReports;

use App\Filament\Resources\OperationalItemReports\Pages\CreateOperationalItemReport;
use App\Filament\Resources\OperationalItemReports\Pages\EditOperationalItemReport;
use App\Filament\Resources\OperationalItemReports\Pages\ListOperationalItemReports;
use App\Filament\Resources\OperationalItemReports\Pages\ViewOperationalItemReport;
use App\Filament\Resources\OperationalItemReports\Schemas\OperationalItemReportForm;
use App\Filament\Resources\OperationalItemReports\Schemas\OperationalItemReportInfolist;
use App\Filament\Resources\OperationalItemReports\Tables\OperationalItemReportsTable;
use App\Models\OperationalItemReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OperationalItemReportResource extends Resource
{
    protected static ?string $model = OperationalItemReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Laporan Detail Operasional';
    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $modelLabel = 'Laporan Detail Operasional';
    protected static ?string $pluralModelLabel = 'Laporan Detail Operasional';

    public static function form(Schema $schema): Schema
    {
        return OperationalItemReportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OperationalItemReportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OperationalItemReportsTable::configure($table);
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
            'index' => ListOperationalItemReports::route('/'),
        ];
    }
}
