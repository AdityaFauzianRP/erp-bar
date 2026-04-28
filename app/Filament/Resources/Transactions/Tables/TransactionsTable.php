<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                return $query->whereNot(function ($q) {
                    $q->where('kategori', 'PO-DIRECT')
                        ->where('status_bayar', 'Menunggu Approval'); // Sesuai status di DB Anda
                });
            })
            ->columns([
                TextColumn::make('nomor_transaksi')
                    ->label('ID TRANSAKSI')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable() // Bisa di-copy sekali klik
                    ->description(fn($record) => "Metode: " . ($record->metode_pembayaran ?? '-')),

                TextColumn::make('supplier.name') // Mengambil kolom 'name' dari relasi 'supplier'
                    ->label('SUPPLIER')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn($record) => "ID: " . ($record->supplier->code ?? '-')) // Opsional: Munculin kode supplier di bawah nama
                    ->wrap(), // Agar jika nama supplier panjang, otomatis turun ke bawah

                TextColumn::make('tanggal_transaksi')
                    ->label('WAKTU')
                    ->dateTime('d M Y, H:i') // Tampilkan jam agar lebih detail
                    ->sortable()
                    ->color('gray'),

                TextColumn::make('total_akhir')
                    ->label('NOMINAL')
                    ->money('IDR')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->alignment('right') // Angka wajib rata kanan biar rapi
                    ->color('success'),

                TextColumn::make('status_bayar')
                    ->label('STATUS')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Terbayar' => 'success',
                        'Menunggu Konfirmasi Pembayaran' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Terbayar' => 'heroicon-m-check-circle',
                        'Menunggu Konfirmasi Pembayaran' => 'heroicon-m-exclamation-triangle',
                        default => 'heroicon-m-minus-circle',
                    }),
            ])
            ->filters([
                // Filter berdasarkan Kategori Transaksi
                SelectFilter::make('kategori')
                    ->label('Jenis Transaksi')
                    ->options([
                        'PO-DIRECT' => 'PO Direct',
                        'REGULAR' => 'Regular',
                        'OPERATIONAL' => 'Operasional',
                    ]),

                // Filter berdasarkan Status Bayar
                SelectFilter::make('status_bayar')
                    ->label('Status Bayar')
                    ->options([
                        'Menunggu Konfirmasi Pembayaran' => 'Menunggu Konfirmasi',
                        'Terbayar' => 'Terbayar',
                    ])
                    ->native(false),

                // Filter Rentang Waktu Transaksi
                Filter::make('tanggal_transaksi')
                    ->form([
                        DatePicker::make('from')->label('Mulai Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('tanggal_transaksi', '>=', $date))
                            ->when($data['until'], fn($q, $date) => $q->whereDate('tanggal_transaksi', '<=', $date));
                    })
                    ->columns(2)
                    ->columnSpan(2)
            ])
            // Layout Filter agar rapi di atas tabel
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->deferFilters()
            ->actions([
                EditAction::make()
                    ->button() // Ubah icon edit jadi button agar terlihat eksklusif
                    ->size('sm')
                    ->label('Lanjutkan'),
            ])

            ->emptyStateHeading('Belum Ada Transaksi')
            ->emptyStateDescription('Data transaksi akan muncul di sini setelah dibuat.')
            ->striped()
            ->defaultSort('tanggal_transaksi', 'desc');
    }
}
