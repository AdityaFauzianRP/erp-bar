<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Tables\Actions\Action;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';


    public function mount(): void
    {
        
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh')
                ->action(fn() => $this->refresh()),
        ];
    }
}
