<?php

namespace App\Filament\Resources\CustomerInduks\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema; // Gunakan Schema, bukan Form
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Filament\Schemas\Components\Grid as ComponentsGrid;

class BranchesRelationManager extends RelationManager
{
    protected static string $relationship = 'branches';

    protected static ?string $title = 'Daftar Cabang / Titik Pengiriman';

    /**
     * Di Filament v4, parameter dan return type harus Schema
     */



    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([

                TextInput::make('branch_name')
                    ->label('Nama Cabang')
                    ->required()
                    ->placeholder('Contoh: Hakata Ikkousha Cipete'),

                TextInput::make('city')
                    ->label('Kota')
                    ->placeholder('Contoh: Jakarta Selatan'),

                TextInput::make('phone')
                    ->label('Nomor Telepon Cabang')
                    ->tel()
                    ->placeholder('021-xxxxxx'),

                Toggle::make('is_active')
                    ->label('Status Cabang Aktif')
                    ->default(true)
                    ->inline(false),

                Textarea::make('address')
                    ->label('Alamat Lengkap Cabang')
                    ->placeholder('Masukkan alamat lengkap pengiriman...')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('branch_name')
            ->columns([
                Tables\Columns\TextColumn::make('branch_name')
                    ->label('Nama Cabang')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('city')
                    ->label('Kota')
                    ->sortable(),

                Tables\Columns\TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(50)
                    ->tooltip(fn($record) => $record->address),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Status'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Cabang Baru')
                    ->icon('heroicon-m-plus')
                    ->modalHeading('Input Data Cabang'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
