<?php

namespace App\Filament\Resources\TravelRequests\Schemas;

use App\Models\OperationalExpense;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TravelRequestForm
{
    public static function generateExpenseNumber()
    {
        $todayPrefix = 'OPS' . date('ymd'); // Contoh: OPS260314

        // Cari record yang nomornya DIMULAI dengan prefix hari ini
        $lastRecord = OperationalExpense::where('number', 'like', $todayPrefix . '%')
            ->orderBy('number', 'desc') // Urutkan berdasarkan nomor terbesar, bukan ID
            ->first();

        if (!$lastRecord) {
            $number = 1;
        } else {
            // Ambil 4 digit terakhir dari nomor yang prefixnya sama
            $lastNumber = (int) substr($lastRecord->number, -4);
            $number = $lastNumber + 1;
        }

        return $todayPrefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make('Pengeluaran Operational')
                    ->schema([
                        TextInput::make('number')
                            ->default(fn() => self::generateExpenseNumber()) // Panggil fungsi helper
                            ->required()
                            ->readOnly(),

                        DatePicker::make('date')
                            ->default(now())
                            ->required()
                            ->disabled(fn($record) => $record?->status === 'Approve'), // DISABLE JIKA Approve

                        Select::make('creator_id') // Gunakan foreign key id-nya
                            ->label('PEMOHON')
                            ->relationship('creator', 'name') // Hubungkan ke relasi 'creator' dan ambil kolom 'name'
                            ->disabled() // Kunci agar tidak bisa diedit
                            ->dehydrated(false) // Penting! Agar Filament tidak mencoba menyimpan field ini ke DB saat save
                            ->visible(fn($operation) => $operation === 'edit') // Muncul HANYA saat halaman Edit
                            ->preload(),


                        Textarea::make('notes')
                            ->columnSpanFull()
                            ->disabled(fn($record) => $record?->status === 'Approve'), // DISABLE JIKA Approve

                        // Select::make('expense_category_id')
                        //     ->label('Kategori (Expense)')
                        //     ->options(\App\Models\ExpenseCategory::pluck('name', 'id'))
                        //     ->searchable()
                        //     ->preload()
                        //     ->required()
                        //     ->columnSpanFull()
                        //     // Tambahkan 3 baris di bawah ini:
                        //     ->default(3)
                        //     ->disabled()
                        //     ->dehydrated(),

                    ])->columns(3)
                    ->columnSpanFull(),

                Section::make('Daftar Barang')
                    ->schema([
                        ViewField::make('items')
                            ->view('filament.components.operational-expense.item-picker2')
                            ->columnSpanFull()
                            ->dehydrated(false)
                            ->reactive()
                            ->afterStateHydrated(function ($set, $state) {
                                if (!$state) $set('items', []);
                            })
                            ->disabled(function ($record) {
                                if (!$record) {
                                    return false;
                                }

                                return $record->status === 'Approve';
                            }),

                        Hidden::make('total_amount')
                            ->dehydrated(true),
                    ])
                    ->columnSpanFull(),

                Section::make('Bukti Pendukung')
                    ->schema([
                        FileUpload::make('attachment')
                            ->label('Upload Bukti Pengeluaran')
                            ->image()
                            ->multiple() // Mengaktifkan upload banyak file
                            ->reorderable() // Opsional: supaya urutan foto bisa digeser-geser
                            ->appendFiles() // Supaya kalau upload lagi, file lama tidak terhapus
                            ->disk('public') // <--- TAMBAHKAN INI SECARA EKSPLISIT
                            ->directory('operational-attachments')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(2048)
                            ->placeholder('Unggah foto nota atau kwitansi di sini (bisa lebih dari satu)')
                            ->columnSpanFull(),                    ])
                    ->columnSpanFull(),
            ]);
    }
}
