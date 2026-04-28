<?php

namespace App\Filament\Resources\CustomerInduks\Tables;

use Filament\Actions\DeleteAction as ActionsDeleteAction;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

class CustomerInduksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // KODE DENGAN ICON TAG
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->icon('heroicon-m-hashtag')
                    ->iconColor('gray')
                    ->color('primary'),

                // NAMA PERUSAHAAN DENGAN AVATAR INISIAL (Visual Branding)
                TextColumn::make('name')
                    ->label('Nama Perusahaan')
                    ->searchable()
                    ->sortable()
                    ->weight('Bold')
                    ->size('Large')
                    ->description(fn($record) => $record->alias ?? '---')
                    ->icon('heroicon-m-building-office-2')
                    ->iconColor('primary'),

                // KONTAK DENGAN ICON INTERAKTIF
                TextColumn::make('email')
                    ->label('Kontak & Alamat')
                    ->icon('heroicon-m-envelope')
                    ->description(fn($record) => "📞 " . ($record->phone ?? '-'))
                    ->color('gray')
                    ->searchable(),

                // INFORMASI PEMBAYARAN (TOP) - Menjawab issue error sebelumnya
                TextColumn::make('term_of_payment')
                    ->label('Termin')
                    ->suffix(function ($record) {
                        $unit = strtolower($record->top_unit ?? 'day');

                        $mapping = [
                            'day'   => ' Hari',
                            'days'  => ' Hari',
                            'month' => ' Bulan',
                            'months' => ' Bulan',
                            'year'  => ' Tahun',
                            'years' => ' Tahun',
                        ];

                        return $mapping[$unit] ?? ' Hari';
                    })
                    ->icon('heroicon-m-credit-card')
                    ->badge()
                    ->color('warning'),

                // STATUS AKTIF DENGAN ICON BERWARNA
                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                // // CUSTOM PRICE INDICATOR
                // IconColumn::make('use_custom_price')
                //     ->label('Custom Price')
                //     ->boolean()
                //     ->trueIcon('heroicon-m-currency-dollar')
                //     ->falseIcon('heroicon-m-minus-small')
                //     ->color('info'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Status Aktif'),
                Tables\Filters\TernaryFilter::make('use_custom_price')->label('Harga Khusus'),
            ])
            ->actions([
                ActionsEditAction::make(),
                // ActionsDeleteAction::make(),
            ]);
    }
}
