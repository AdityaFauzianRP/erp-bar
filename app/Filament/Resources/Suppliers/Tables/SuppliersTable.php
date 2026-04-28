<?php

namespace App\Filament\Resources\Suppliers\Tables;

use Filament\Actions\ActionGroup as ActionsActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction as ActionsDeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ActionGroup;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // KODE & STATUS (Badge Style)
                TextColumn::make('code')
                    ->label('ID & Status Produk')
                    ->fontFamily('mono')
                    ->weight(FontWeight::Bold)
                    ->color('primary')
                    ->searchable()
                    ->description(fn($record) => $record->is_active ? '✅ Active Partner' : '❌ Inactive'),

                // NAMA SUPPLIER DENGAN AVATAR TEKS
                TextColumn::make('name')
                    ->label('Nama Supplier')
                    ->weight(FontWeight::ExtraBold)
                    ->size('Large')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-building-office-2')
                    ->iconColor('primary')
                    ->description(fn($record) => "PIC Master: " . ($record->pic ?? 'N/A')),

                // KONTAK DENGAN BADGE & ACTION
                TextColumn::make('phone')
                    ->label('Kontak Utama')
                    ->icon('heroicon-m-phone')
                    ->badge()
                    ->color('info')
                    ->copyable()
                    ->copyMessage('Phone number copied')
                    ->searchable(),

                // INFORMASI BANK (Box Style look)
                TextColumn::make('bank_name')
                    ->label('Data Bank')
                    ->weight(FontWeight::Bold)
                    ->icon('heroicon-m-credit-card')
                    ->iconColor('success')
                    ->color('success')
                    ->description(fn($record) => "No: " . ($record->bank_account_number ?? 'Not Set'))
                    ->placeholder('Bank Data Empty'),

                // JUMLAH PRODUK (Menambah keramaian data)
                TextColumn::make('product_suppliers_count')
                    ->label('Produk Terdaftar')
                    ->counts('product_suppliers') // Pastikan relasi ini ada di model
                    ->suffix(' Items')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                // WAKTU DENGAN RELATIVE TIME
                TextColumn::make('created_at')
                    ->label('Bergabung Sejak')
                    ->dateTime('d M Y')
                    ->description(fn($record) => $record->created_at->diffForHumans())
                    ->color('gray')
                    ->sortable(),
            ])
            ->contentGrid([
                'md' => 1,
                'xl' => 1, // Tetap list, tapi kita buat padat
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Partnership Status')
                    ->placeholder('All Status')
                    ->trueLabel('Active Partners')
                    ->falseLabel('Inactive/History')
                    // Gunakan ini sebagai pengganti icons()
                    // ->trueIcon('heroicon-m-check-badge')
                    // ->falseIcon('heroicon-m-x-circle')
                    ->native(false),
            ])
            ->actions([
                // Action Group agar terlihat rapi (titik tiga)
                ActionsActionGroup::make([
                    ActionsEditAction::make()->color('primary'),
                    ActionsDeleteAction::make(),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->button()
                    ->label('Actions')
                    ->color('gray'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->striped() // Membuat baris selang-seling warna
            ->poll('60s'); // Membuat tabel terkesan "live"
    }
}
