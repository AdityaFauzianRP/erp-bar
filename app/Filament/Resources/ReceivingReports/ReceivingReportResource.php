<?php

namespace App\Filament\Resources\ReceivingReports;

use App\Filament\Resources\ReceivingReports\Pages\CreateReceivingReport;
use App\Filament\Resources\ReceivingReports\Pages\EditReceivingReport;
use App\Filament\Resources\ReceivingReports\Pages\ListReceivingReports;
use App\Filament\Resources\ReceivingReports\Pages\ReceiveGoods;
use App\Filament\Resources\ReceivingReports\Schemas\ReceivingReportForm;
use App\Filament\Resources\ReceivingReports\Tables\ReceivingReportsTable;
use App\Models\ReceivingReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReceivingReportResource extends Resource
{
    protected static ?string $model = ReceivingReport::class;

    protected static string|BackedEnum|null $navigationIcon  = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Penerimaan Barang';
    protected static string|\UnitEnum|null $navigationGroup = 'Pengeluaran';
    protected static ?string $modelLabel = 'Penerimaan Barang';
    protected static ?string $pluralModelLabel = 'Daftar Penerimaan Barang';

    public static function form(Schema $schema): Schema
    {
        return ReceivingReportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReceivingReportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    // 2. Mengubah label Model (Mempengaruhi judul halaman & tombol Create)
    public static function getModelLabel(): string
    {
        return 'Proses Penerimaan Baru';
    }

    

    // 3. Mengubah Breadcrumb agar tidak tertulis 'Create'
    protected static ?string $breadcrumb = 'Proses Penerimaan Baru';

    public static function getPages(): array
    {
        return [
            'index' => ListReceivingReports::route('/'),
            'create' => CreateReceivingReport::route('/create'),
            'edit' => EditReceivingReport::route('/{record}/edit'),
        ];
    }
}
