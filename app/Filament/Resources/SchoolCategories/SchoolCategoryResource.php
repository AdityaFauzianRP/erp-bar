<?php

namespace App\Filament\Resources\SchoolCategories;

use App\Filament\Resources\SchoolCategories\Pages\CreateSchoolCategory;
use App\Filament\Resources\SchoolCategories\Pages\EditSchoolCategory;
use App\Filament\Resources\SchoolCategories\Pages\ListSchoolCategories;
use App\Filament\Resources\SchoolCategories\Schemas\SchoolCategoryForm;
use App\Filament\Resources\SchoolCategories\Tables\SchoolCategoriesTable;
use App\Models\SchoolCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SchoolCategoryResource extends Resource
{
    protected static ?string $model = SchoolCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Kategori Sekolah (Paket Harga)';

    protected static ?string $pluralLabel = 'Kategori Sekolah (Paket Harga)';

    public static function form(Schema $schema): Schema
    {
        return SchoolCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchoolCategoriesTable::configure($table);
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
            'index' => ListSchoolCategories::route('/'),
            'create' => CreateSchoolCategory::route('/create'),
            'edit' => EditSchoolCategory::route('/{record}/edit'),
        ];
    }
}
