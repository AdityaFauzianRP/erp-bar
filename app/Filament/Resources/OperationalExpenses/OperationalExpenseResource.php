<?php

namespace App\Filament\Resources\OperationalExpenses;

use App\Filament\Resources\OperationalExpenses\Pages\CreateOperationalExpense;
use App\Filament\Resources\OperationalExpenses\Pages\EditOperationalExpense;
use App\Filament\Resources\OperationalExpenses\Pages\ListOperationalExpenses;
use App\Filament\Resources\OperationalExpenses\Schemas\OperationalExpenseForm;
use App\Filament\Resources\OperationalExpenses\Tables\OperationalExpensesTable;
use App\Models\OperationalExpense;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OperationalExpenseResource extends Resource
{
    protected static ?string $model = OperationalExpense::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CurrencyBangladeshi;

    protected static ?string $navigationLabel = 'Pengeluaran Operational';
    protected static string|\UnitEnum|null $navigationGroup = 'Pengeluaran';
    protected static ?string $modelLabel = 'Pengeluaran Operational';
    protected static ?string $pluralModelLabel = 'Daftar Pengeluaran Operational';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', 'Approve');
    }

    public static function form(Schema $schema): Schema
    {
        return OperationalExpenseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OperationalExpensesTable::configure($table);
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
            'index' => ListOperationalExpenses::route('/'),
            'create' => CreateOperationalExpense::route('/create'),
            'edit' => EditOperationalExpense::route('/{record}/edit'),
        ];
    }
}
