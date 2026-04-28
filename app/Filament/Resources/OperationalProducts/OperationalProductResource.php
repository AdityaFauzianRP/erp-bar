<?php

namespace App\Filament\Resources\OperationalProducts;

use App\Filament\Resources\OperationalProducts\Pages\CreateOperationalProduct;
use App\Filament\Resources\OperationalProducts\Pages\EditOperationalProduct;
use App\Filament\Resources\OperationalProducts\Pages\ListOperationalProducts;
use App\Filament\Resources\OperationalProducts\Schemas\OperationalProductForm;
use App\Filament\Resources\OperationalProducts\Tables\OperationalProductsTable;
use App\Models\OperationalProduct;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class OperationalProductResource extends Resource
{
    protected static ?string $model = OperationalProduct::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart'; // Icon Keranjang

    protected static ?string $navigationLabel = 'Daftar Produk Operasional';

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $pluralLabel = 'Produk Operasional';

    public static function form(Schema $schema): Schema
    {
        return OperationalProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OperationalProductsTable::configure($table);
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
            'index' => ListOperationalProducts::route('/'),
            'create' => CreateOperationalProduct::route('/create'),
            'edit' => EditOperationalProduct::route('/{record}/edit'),
        ];
    }
}
