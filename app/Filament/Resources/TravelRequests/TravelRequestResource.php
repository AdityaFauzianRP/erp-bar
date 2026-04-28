<?php

namespace App\Filament\Resources\TravelRequests;

use App\Filament\Resources\TravelRequests\Pages\CreateTravelRequest;
use App\Filament\Resources\TravelRequests\Pages\EditTravelRequest;
use App\Filament\Resources\TravelRequests\Pages\ListTravelRequests;
use App\Filament\Resources\TravelRequests\Schemas\TravelRequestForm;
use App\Filament\Resources\TravelRequests\Tables\TravelRequestsTable;
use App\Models\OperationalExpense;
use App\Models\TravelRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TravelRequestResource extends Resource
{
    protected static ?string $model = OperationalExpense::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingLibrary;

    protected static ?string $navigationLabel = 'Pengajuan Operasional';
    protected static string|\UnitEnum|null $navigationGroup = 'Pengeluaran';
    protected static ?string $modelLabel = 'Pengajuan Operasional';
    protected static ?string $pluralModelLabel = 'Daftar Pengajuan Operasional';

    public static function getNavigationBadge(): ?string
    {
        // Ganti 'approve_operational_expense' dengan nama permission yang Anda miliki
        if (! auth()->user()->can('ApprovePerjalananDinas')) {
            return null;
        }

        return static::getModel()::where('status', 'Draft')
            ->where('is_request', 1)
            ->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        // Samakan pengecekan permission-nya agar warna badge tidak muncul tanpa angka
        if (! auth()->user()->can('ApprovePerjalananDinas')) {
            return null;
        }

        return 'danger';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('is_request', 1)
            ->when(! auth()->user()->can('ApprovePerjalananDinas'), function ($query) {
                // Jika user TIDAK punya izin Approve, hanya munculkan data miliknya sendiri
                return $query->where('user_id', auth()->id());
            });
    }

    public static function form(Schema $schema): Schema
    {
        return TravelRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TravelRequestsTable::configure($table);
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
            'index' => ListTravelRequests::route('/'),
            'create' => CreateTravelRequest::route('/create'),
            'edit' => EditTravelRequest::route('/{record}/edit'),
        ];
    }
}
