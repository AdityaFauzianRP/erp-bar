<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Image as ComponentsImage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Intervention\Image\Laravel\Facades\Image;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {

        $isDisabled = fn($get) => in_array($get('status_bayar'), ['Terbayar', 'Lunas']);
        return $schema


            ->components([
                //
                Section::make('Informasi Utama')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nomor_transaksi')
                            ->disabled()
                            ->dehydrated(),
                        DatePicker::make('tanggal_transaksi')
                            ->label('Tanggal Pembayaran')
                            ->required()
                            ->disabled($isDisabled)
                            // ->native(false)
                            // ->displayFormat('d M Y')
                            ->default(now()),

                        DatePicker::make('created_at_display')
                            ->label('Tanggal PO Dibuat')
                            ->disabled() // Agar tidak bisa diedit
                            ->dehydrated(false) // Agar tidak error saat simpan (karena kolom ini tidak ada di tabel transaksi)
                            // ->native(false)
                            // ->displayFormat('d M Y')
                            ->formatStateUsing(function ($record) {
                                // Mengambil data dari relasi purchase
                                return $record?->purchase?->created_at;
                            }),

                        TextInput::make('kategori')
                            ->disabled(), // PO/PI/OPX
                        TextInput::make('status_bayar')
                            // ->options([
                            //     'Menunggu Konfirmasi Pembayaran' => 'Menunggu Konfirmasi',
                            //     'Terbayar' => 'Terbayar',
                            // ])
                            ->required()
                            ->disabled(),
                    ])
                    ->columnSpanFull(),

                Section::make('Metode & Pengaturan Pembayaran')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('metode_pembayaran')
                            ->options([
                                'Cash' => 'Cash (Tunai)',
                                'Transfer' => 'Transfer Bank (Tempo)',
                                // 'Cicilan' => 'Cicilan (Berjangka)',
                            ])
                            ->live() // Memicu perubahan form secara real-time
                            ->disabled($isDisabled)
                            ->required(),

                        // Muncul hanya jika Transfer atau Cicilan
                        Select::make('bank_account_id')
                            ->label('Pilih Bank Internal')
                            ->disabled($isDisabled)
                            ->relationship('bankAccount', 'bank_name')
                            ->visible(fn($get) => in_array($get('metode_pembayaran'), ['Transfer', 'Cicilan']))
                            ->required(fn($get) => in_array($get('metode_pembayaran'), ['Transfer', 'Cicilan'])),

                        // Muncul jika Transfer (Tenggat Waktu/Kalender)
                        // DatePicker::make('tenggat_waktu')
                        //     ->label('Tanggal Jatuh Tempo')
                        //     ->visible(fn($get) => $get('metode_pembayaran') === 'Transfer')
                        // ->required(fn($get) => $get('metode_pembayaran') === 'Transfer'),


                    ])
                    ->columnSpanFull(),

                Section::make('Skema Cicilan (Berjangka)')
                    ->icon('heroicon-o-calendar-days')
                    ->visible(fn($get) => $get('metode_pembayaran') === 'Cicilan')
                    ->columns(3)
                    ->schema([
                        TextInput::make('tenor')
                            ->label('Jumlah Tenor')
                            ->numeric()
                            ->suffix('Kali')
                            ->default(1)
                            ->live()
                            ->afterStateUpdated(fn($set, $get) => self::updateJadwalSchedules($set, $get))
                            ->required(fn($get) => $get('metode_pembayaran') === 'Cicilan'),

                        Select::make('interval_cicilan')
                            ->label('Interval Pembayaran')
                            ->options([
                                'bulan' => 'Per Bulan',
                                'minggu' => 'Per Minggu',
                                'hari' => 'Per Hari',
                            ])
                            ->default('bulan')
                            ->live()
                            ->afterStateUpdated(fn($set, $get) => self::updateJadwalSchedules($set, $get))
                            ->required(fn($get) => $get('metode_pembayaran') === 'Cicilan'),

                        DatePicker::make('tanggal_mulai_cicilan')
                            ->label('Mulai Cicilan Pertama')
                            ->default(now())
                            ->live()
                            ->afterStateUpdated(fn($set, $get) => self::updateJadwalSchedules($set, $get))
                            ->required(fn($get) => $get('metode_pembayaran') === 'Cicilan'),

                        // REPEATER UNTUK VISUALISASI JADWAL
                        Repeater::make('simulasi_jadwal')
                            ->label('Preview Jadwal Pembayaran')
                            ->schema([
                                TextInput::make('cicilan_ke')
                                    ->label('Termin')
                                    ->readOnly(),
                                TextInput::make('tanggal_jatuh_tempo')
                                    ->label('Rencana Tanggal Bayar')
                                    ->readOnly(),
                                TextInput::make('nominal')
                                    ->label('Nominal')
                                    ->prefix('Rp')
                                    ->readOnly(),
                            ])
                            ->columns(3)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->columnSpanFull()
                            ->helperText('Jadwal ini akan otomatis dibuat saat transaksi disimpan.'),
                    ])
                    ->columnSpanFull(),

                Section::make('Nilai Transaksi')
                    ->columns(3)
                    ->schema([
                        TextInput::make('subtotal')->numeric()->disabled(),
                        TextInput::make('pajak')->numeric()->disabled(),
                        TextInput::make('total_akhir')
                            ->numeric()
                            ->label('Total Tagihan')
                            ->prefix('Rp')
                            ->disabled(),
                    ])
                    ->columnSpanFull(),

                FileUpload::make('image')
                    ->label('Bukti Pembayaran')
                    ->image()
                    ->directory('pembayaran') // Folder: storage/app/public/pembayaran
                    ->live() // <--- INI WAJIB ADA
                    ->imageEditor()
                    ->disabled($isDisabled)
                    ->saveUploadedFileUsing(function ($file) {
                        $type = $file->getMimeType();
                        $path = $file->getRealPath();

                        // 1. Proses Kompresi dengan GD
                        if ($type === 'image/png') {
                            $image = imagecreatefrompng($path);
                        } else {
                            $image = imagecreatefromjpeg($path);
                        }

                        // 2. Buat Nama File Unik
                        $filename = 'bukti-' . time() . '.jpg';
                        $destinationPath = storage_path('app/public/pembayaran/' . $filename);

                        // Pastikan direktori ada
                        if (!file_exists(storage_path('app/public/pembayaran'))) {
                            mkdir(storage_path('app/public/pembayaran'), 0755, true);
                        }

                        // 3. Simpan sebagai JPEG dengan Kualitas 60% (Pasti di bawah 500kb)
                        imagejpeg($image, $destinationPath, 60);
                        imagedestroy($image);

                        // 4. Kembalikan path relatif untuk disimpan di DB (pembayaran/namafile.jpg)
                        return 'pembayaran/' . $filename;
                    })
                    ->columnSpanFull()
                    ->disk('public') // <--- WAJIB: Agar Filament tahu mencari di storage/app/public
                    ->helperText('Gambar akan otomatis dikompres di bawah 500KB sebelum disimpan.'),
            ]);
    }

    public static function updateJadwalSchedules($set, $get)
    {
        $total = (float) $get('total_akhir');
        $tenor = (int) $get('tenor');
        $interval = $get('interval_cicilan');
        $tglMulai = $get('tanggal_mulai_cicilan');

        if ($total <= 0 || $tenor <= 0 || !$tglMulai) {
            $set('simulasi_jadwal', []);
            return;
        }

        $nominalPerTermin = $total / $tenor;
        $schedules = [];

        for ($i = 1; $i <= $tenor; $i++) {
            $date = \Carbon\Carbon::parse($tglMulai);

            if ($interval === 'bulan') $date->addMonths($i - 1);
            if ($interval === 'minggu') $date->addWeeks($i - 1);
            if ($interval === 'hari') $date->addDays($i - 1);

            $schedules[] = [
                'cicilan_ke' => "Cicilan Ke-$i",
                'tanggal_jatuh_tempo' => $date->format('d-m-Y'),
                'nominal' => number_format($nominalPerTermin, 0, ',', '.'),
            ];
        }

        $set('simulasi_jadwal', $schedules);
    }
}
