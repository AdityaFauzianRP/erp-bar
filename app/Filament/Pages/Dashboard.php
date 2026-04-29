<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Tables\Actions\Action;

class Dashboard extends BaseDashboard
{
    // protected static string $view = 'filament.pages.dashboard';

    // You can customize the navigation settings if you want this page to appear in the sidebar
    protected static ?string $navigationLabel = 'Dashboard';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    public function mount(): void
    {
        // Any logic you need when the page loads
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
