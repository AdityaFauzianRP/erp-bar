<?php

namespace App\Filament\Resources\AssetCategories;

use App\Filament\Resources\AssetCategories\Pages\CreateAssetCategory;
use App\Filament\Resources\AssetCategories\Pages\EditAssetCategory;
use App\Filament\Resources\AssetCategories\Pages\ListAssetCategories;
use App\Filament\Resources\AssetCategories\Schemas\AssetCategoryForm;
use App\Filament\Resources\AssetCategories\Tables\AssetCategoriesTable;
use App\Models\AssetCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AssetCategoryResource extends Resource
{
    // protected static ?string $model = AssetCategory::class;

    // protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    // // protected static ?string $navigationLabel = 'Data Kategori Aset';
    // // protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Aset';
    // // protected static ?string $modelLabel = 'Data Kategori Aset';
    // // protected static ?string $pluralModelLabel = 'Data Kategori Aset';

    // public static function form(Schema $schema): Schema
    // {
    //     return AssetCategoryForm::configure($schema);
    // }

    // public static function table(Table $table): Table
    // {
    //     return AssetCategoriesTable::configure($table);
    // }

    // public static function getRelations(): array
    // {
    //     return [
    //         //
    //     ];
    // }

    // public static function getPages(): array
    // {
    //     return [
    //         'index' => ListAssetCategories::route('/'),
    //         'create' => CreateAssetCategory::route('/create'),
    //         'edit' => EditAssetCategory::route('/{record}/edit'),
    //     ];
    // }
}
