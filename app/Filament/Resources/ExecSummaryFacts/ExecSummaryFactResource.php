<?php

namespace App\Filament\Resources\ExecSummaryFacts;

use App\Filament\Resources\ExecSummaryFacts\Pages\CreateExecSummaryFact;
use App\Filament\Resources\ExecSummaryFacts\Pages\EditExecSummaryFact;
use App\Filament\Resources\ExecSummaryFacts\Pages\ListExecSummaryFacts;
use App\Filament\Resources\ExecSummaryFacts\Pages\ViewExecSummaryFact;
use App\Filament\Resources\ExecSummaryFacts\Schemas\ExecSummaryFactForm;
use App\Filament\Resources\ExecSummaryFacts\Schemas\ExecSummaryFactInfolist;
use App\Filament\Resources\ExecSummaryFacts\Tables\ExecSummaryFactsTable;
use App\Models\ExecSummaryFact;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExecSummaryFactResource extends Resource
{
    protected static ?string $model = ExecSummaryFact::class;

    protected static ?string $defaultSort = null;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartBar;

    protected static ?string $navigationLabel = 'Ringkasan Laporan';
    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Ringkasan Laporan';
    protected static ?string $pluralModelLabel = 'Ringkasan Laporan';

    public static function form(Schema $schema): Schema
    {
        return ExecSummaryFactForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExecSummaryFactInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExecSummaryFactsTable::configure($table);
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
            'index' => ListExecSummaryFacts::route('/'),
        ];
    }
}
