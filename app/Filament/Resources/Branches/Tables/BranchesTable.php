<?php

namespace App\Filament\Resources\Branches\Tables;

use Filament\Actions\BulkActionGroup as ActionsBulkActionGroup;
use Filament\Actions\DeleteAction as ActionsDeleteAction;
use Filament\Actions\DeleteBulkAction as ActionsDeleteBulkAction;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;

class BranchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode Cabang')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->description(fn ($record) => $record->created_at->format('d M Y')),

                TextColumn::make('name')
                    ->label('Nama Kantor Cabang')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->address),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Filter Status')
                    ->placeholder('Semua Status')
                    ->trueLabel('Hanya Cabang Aktif')
                    ->falseLabel('Hanya Cabang Non-Aktif'),
            ])
            ->actions([
                ActionsEditAction::make(),
                ActionsDeleteAction::make(),
            ])
            ->bulkActions([
                ActionsBulkActionGroup::make([
                    ActionsDeleteBulkAction::make(),
                ]),
            ]);
    }
}