<?php

namespace App\Filament\Resources\Branches\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BranchForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                ComponentsSection::make('Informasi Kantor Cabang')
                    ->description('Kelola data lokasi dan operasional cabang.')
                    ->icon('heroicon-o-home-modern')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Cabang')
                            ->placeholder('Contoh: Cabang Jakarta Pusat')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true) // Trigger update saat kursor pindah
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if (!$state) return;

                                // Logika: Ambil 3 huruf pertama, buat uppercase + angka random 3 digit
                                // Contoh: Jakarta -> JKT-742
                                $prefix = Str::upper(Str::substr($state, 0, 3));
                                $code = $prefix . '-' . rand(100, 999);
                                
                                $set('code', $code);
                            }),

                        TextInput::make('code')
                            ->label('Kode Cabang')
                            ->required()
                            // ->readonly() // Buat readonly agar tidak diubah manual
                            ->unique(ignoreRecord: true)
                            ->dehydrated()
                            ->helperText('Kode ini dibuat otomatis oleh sistem.')
                            ->disabled(),

                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->placeholder('Masukkan alamat fisik cabang...')
                            ->rows(3)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->helperText('Jika non-aktif, user dari cabang ini tidak bisa login.')
                            ->default(true),
                            // ->color('success'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}