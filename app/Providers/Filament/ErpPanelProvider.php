<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Resources\Users\Widgets\CustomAccountWidget;
use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ErpPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('erp')
            ->path('erp')
            ->login()
            ->brandName('Core System MBG')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make()
                    ->label('Dashboard'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label('Master Data')
                    ->icon('heroicon-o-server-stack'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label('Inventory')
                    ->icon('heroicon-o-cube'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label('Pengeluaran')
                    ->icon('heroicon-o-shopping-bag'),
                // Filament\Navigation\NavigationGroup::make()
                //     ->label('Manajemen Aset')
                //     ->icon('heroicon-o-building-office-2'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label('Laporan')
                    ->icon('heroicon-o-document-text'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label('Manajemen Pengguna')
                    ->icon('heroicon-o-users'),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->renderHook(
                'panels::head.end',
                fn(): string => new HtmlString('
                    <style>
                        html:not(.dark) .fi-sidebar { 
                            background-color: white !important; 
                            box-shadow: 2px 0 10px rgba(0,0,0,0.05) !important; 
                            border-right: none !important; 
                        }
                        html:not(.dark) .fi-sidebar-header { 
                            background-color: white !important; 
                            border-bottom: 1px solid #f3f4f6; 
                        }
                        html:not(.dark) .fi-sidebar-nav { 
                            background-color: white !important; 
                        }
                        html.dark .fi-sidebar {
                            box-shadow: 2px 0 10px rgba(0,0,0,0.3) !important;
                            border-right: 1px solid rgba(255,255,255,0.05) !important;
                        }
                        .fi-ta-pagination {
                            display: flex !important;
                            justify-content: flex-end !important;
                            width: 100% !important;
                        }
                    </style>
                ')
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                CustomAccountWidget::class,
            ])

            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn() => view('filament.partials.clock')
            )

            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
            FilamentShieldPlugin::make()
                ->navigationGroup('Manajemen Pengguna')
            ])
            ->authMiddleware([
                Authenticate::class,
            ])

            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('Profil Saya')
                    ->icon('heroicon-o-user-circle')
                    ->url(fn(): string => EditProfile::getUrl()) 
            ]);
    }
}
