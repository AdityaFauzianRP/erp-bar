<?php

namespace App\Filament\Resources\CustomerInduks;

use App\Filament\Resources\CustomerInduks\Pages\CreateCustomerInduk;
use App\Filament\Resources\CustomerInduks\Pages\EditCustomerInduk;
use App\Filament\Resources\CustomerInduks\Pages\ListCustomerInduks;
use App\Filament\Resources\CustomerInduks\RelationManagers\BranchesRelationManager;
use App\Filament\Resources\CustomerInduks\RelationManagers\PricesRelationManager;
use App\Filament\Resources\CustomerInduks\Schemas\CustomerIndukForm;
use App\Filament\Resources\CustomerInduks\Tables\CustomerInduksTable;
use App\Models\CustomerInduk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CustomerIndukResource extends Resource
{
    protected static ?string $model = CustomerInduk::class;

    protected static string|BackedEnum|null $navigationIcon  = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Customer Sekolah';
    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $modelLabel = 'Data Customer';
    protected static ?string $pluralModelLabel = 'Daftar Data Customer';
    public static function form(Schema $schema): Schema
    {
        return CustomerIndukForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomerInduksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            // DAFTARKAN DI SINI
            // BranchesRelationManager::class,
            // PricesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomerInduks::route('/'),
            'create' => CreateCustomerInduk::route('/create'),
            'edit' => EditCustomerInduk::route('/{record}/edit'),
        ];
    }
}
