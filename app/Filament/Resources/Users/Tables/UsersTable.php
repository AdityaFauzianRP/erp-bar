<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\BulkActionGroup as ActionsBulkActionGroup;
use Filament\Actions\DeleteBulkAction as ActionsDeleteBulkAction;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Inisial atau Foto
                TextColumn::make('name')
                    ->label('Nama Pengguna')
                    ->description(fn(User $record): string => $record->email)
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                // Badge Role dari Shield
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color('primary')
                    ->searchable(),

                TextColumn::make('branches.name')
                    ->label('Penugasan Cabang')
                    ->badge()
                    ->color('success')
                    ->searchable()
                    // Menampilkan indikator jika user sedang aktif di cabang tersebut
                    ->description(
                        fn(User $record): string =>
                        $record->activeBranch
                            ? "Aktif di: " . $record->activeBranch->name
                            : "Belum memilih cabang aktif"
                    ),

                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Saring berdasarkan Role'),
            ])
            ->recordActions([
                ActionsEditAction::make()
                    ->button() // Ubah jadi tombol agar lebih tegas
                    ->icon('heroicon-m-pencil-square'),
            ])
            ->headerActions([
                ActionsBulkActionGroup::make([
                    ActionsDeleteBulkAction::make(),
                ]),
            ]);
    }
}
