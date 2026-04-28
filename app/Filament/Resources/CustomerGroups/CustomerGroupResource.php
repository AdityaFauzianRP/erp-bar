<?php

namespace App\Filament\Resources\CustomerGroups;

use App\Filament\Resources\CustomerGroups\Pages\CreateCustomerGroup;
use App\Filament\Resources\CustomerGroups\Pages\EditCustomerGroup;
use App\Filament\Resources\CustomerGroups\Pages\ListCustomerGroups;
use App\Filament\Resources\CustomerGroups\Schemas\CustomerGroupForm;
use App\Filament\Resources\CustomerGroups\Tables\CustomerGroupsTable;
use App\Models\CustomerGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CustomerGroupResource extends Resource
{
    protected static ?string $model = CustomerGroup::class;

    protected static string|BackedEnum|null $navigationIcon  = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Customer Group';
    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $modelLabel = 'Data Customer';
    protected static ?string $pluralModelLabel = 'Daftar Data Customer';

    public static function form(Schema $schema): Schema
    {
        return CustomerGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomerGroupsTable::configure($table);
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
            'index' => ListCustomerGroups::route('/'),
            'create' => CreateCustomerGroup::route('/create'),
            'edit' => EditCustomerGroup::route('/{record}/edit'),
        ];
    }
}
