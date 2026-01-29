<?php

namespace App\Filament\Resources\ExpenseCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ExpenseCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->fontFamily('mono')
                    ->sortable()
                    ->searchable()
                    ->color('gray'),

                TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(50)
                    ->placeholder('Tidak ada keterangan'),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->boolean()
                    ->trueLabel('Hanya Kategori Aktif')
                    ->falseLabel('Hanya Kategori Non-Aktif'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-tag')
            ->emptyStateHeading('Belum ada kategori pengeluaran')
            ->emptyStateDescription('Buat kategori pertama Anda untuk mulai mencatat pengeluaran.');
    }
}
