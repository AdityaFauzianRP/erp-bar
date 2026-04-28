<?php

namespace App\Filament\Resources\Purchases;

use App\Filament\Resources\Purchases\Pages\CreatePurchase;
use App\Filament\Resources\Purchases\Pages\DirectPurchase;
use App\Filament\Resources\Purchases\Pages\EditPurchase;
use App\Filament\Resources\Purchases\Pages\ListPurchases;
use App\Filament\Resources\Purchases\Schemas\PurchaseForm;
use App\Filament\Resources\Purchases\Tables\PurchasesTable;
use App\Models\Purchase;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static string|BackedEnum|null $navigationIcon  = Heroicon::ShoppingCart;
    protected static ?string $navigationLabel = 'Pembelian';
    protected static string|\UnitEnum|null $navigationGroup = 'Pengeluaran';

    protected static ?string $modelLabel = 'Pembelian';
    protected static ?string $pluralModelLabel = 'Daftar Pembelian';

    public static function getNavigationBadge(): ?string
    {
        // Tetap pertahankan pengecekan permission jika memang diperlukan
        if (! auth()->user()->can('ApprovePurchase')) {
            return null;
        }

        // Mengubah query untuk menghitung kolom pending_change yang tidak NULL
        return static::getModel()::where('status', 'Menunggu Approval')
            ->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        if (! auth()->user()->can('ApprovePurchase')) {
            return null;
        }

        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return PurchaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PurchasesTable::configure($table);
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
            'index' => ListPurchases::route('/'),
            'create' => CreatePurchase::route('/create'),
            'edit' => EditPurchase::route('/{record}/edit'),
            'direct-purchase' => DirectPurchase::route('/direct-purchase'),
            'direct-edit' => DirectPurchase::route('/{record}/direct-edit'),
        ];
    }
}
