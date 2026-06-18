<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationGroup;
use App\Filament\Widgets\CustomAccountWidget;
use Illuminate\Support\Facades\Blade;
use Filament\View\PanelsRenderHook;
use Filament\Support\Facades\FilamentColor;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('Sistem Monitoring Perkembangan Anak PAUD')
            ->login(Login::class)
            ->navigationGroups([
                NavigationGroup::make('Data Perkembangan')
                    ->icon('heroicon-o-clipboard-document-list'),
                NavigationGroup::make('Siswa')
                    ->icon('heroicon-o-user'),
                NavigationGroup::make('Pengurus')
                    ->icon('heroicon-o-user-plus'),
            ])
            ->renderHook( PanelsRenderHook::HEAD_END, fn (): string => 
            Blade::render(' <style> /* Target nama di User Menu dropdown */ .fi-user-menu-label 
            { display: flex !important; flex-direction: column !important; align-items: flex-start !important; line-height: 1.2 !important; } 
             /* Memanipulasi teks setelah tanda "|" agar jadi baris baru */ .fi-user-menu-label { white-space: pre-wrap; } </style> '), )

            ->colors([
                'primary' => Color::Blue,
                'secondary' => Color::Green,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                CustomAccountWidget::class,
            ])
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
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render('
                    <style>
                        body {
                            background-image: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.85)), url("/storage/yukkie.png") !important;
                            background-size: cover !important;
                            background-position: center !important;
                            background-attachment: fixed !important;
                        }
                        
                        .fi-layout, .fi-main {
                            background-color: transparent !important;
                        }

                        .fi-topbar .fi-global-search,
                        .fi-global-search {
                            display: none !important;
                        }

                        .fi-dropdown-panel, .fi-modal-window, .fi-no-notification {
                            background-color: rgba(255, 255, 255, 0.95) !important;
                            backdrop-filter: blur(16px) !important;
                            border-radius: 1rem !important;
                            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3) !important;
                        }
                        .dark .fi-dropdown-panel, .dark .fi-modal-window, .dark .fi-no-notification {
                            background-color: rgba(30, 41, 59, 0.95) !important; 
                        }

                        .fi-simple-layout {
                            min-height: 100vh !important;
                            display: flex !important;
                            align-items: center !important;
                            justify-content: center !important;
                        }
                        .fi-simple-main {
                            width: 100% !important;
                            max-width: 28rem !important;
                            margin: 0 auto !important;
                        }

                        .fi-simple-main .fi-section {
                            background-color: rgba(255, 255, 255, 0.95) !important;
                            backdrop-filter: blur(10px) !important;
                            border-radius: 1rem !important;
                        }

                        .fi-simple-header {
                            display: flex !important;
                            flex-direction: column !important;
                            align-items: center !important;
                            text-align: center !important;
                            textcolor: #1e293b !important;
                        }

                        .fi-simple-layout .fi-logo {
                            justify-content: center !important;
                            margin: 0 auto !important;
                        }

                        .fi-topbar {
                            background-color: rgba(255, 238, 0, 0.7) !important; 
                            backdrop-filter: blur(12px) !important;
                            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
                        }
                        .fi-logo {
                            color: #ffffff !important; 
                            text-shadow: 1px 1px 3px rgba(0,0,0,0.6); 
                        }

                        .fi-main .fi-section, 
                        .fi-ta-content {
                            background-color: rgba(240, 253, 244, 0.9) !important; 
                            backdrop-filter: blur(10px) !important;
                            border: 1px solid rgba(134, 239, 172, 0.4) !important;
                            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
                        }
                        .dark .fi-main .fi-section, 
                        .dark .fi-ta-content {
                            background-color: rgb(0, 0, 0, 0.5) !important; 
                        }
                        
                        .fi-wi-stats-overview-stat {
                            background-color: rgba(240, 253, 244, 0.9) !important; 
                            backdrop-filter: blur(10px) !important;
                            border: 1px solid rgba(134, 239, 172, 0.4) !important;
                            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
                            border-radius: 0.75rem !important;
                        }

                        .fi-wi-stats-overview .fi-section {
                            background: none !important;
                            border: none !important;
                            box-shadow: none !important;
                            backdrop-filter: none !important;
                        }

                        .dark .fi-wi-stats-overview .fi-section {
                            background: none !important;
                            border: none !important;
                            box-shadow: none !important;
                            backdrop-filter: none !important;
                        }

                        .dark .fi-wi-stats-overview-stat {
                            background-color: rgba(30, 41, 59, 0.9) !important; 
                        }

                        .fi-sidebar {
                            background-color: none !important;
                        }

                        .dark .fi-sidebar {
                            background-color: none !important;
                        }

                        .fi-sidebar-item > a {
                            border-radius: 0.75rem !important;
                            background-color: rgba(255, 255, 255, 0.75) !important;
                            border: 1px solid rgba(134, 239, 172, 0.4) !important;
                        }

                        .dark .fi-sidebar-item > a {
                            background-color: rgba(30, 41, 59, 0.5) !important;
                            border: 1px solid rgba(134, 239, 172, 0.4) !important;
                        }

                        .fi-user-menu-label { 
                            display: flex !important; 
                            flex-direction: column !important; 
                            align-items: flex-start !important; 
                            line-height: 1.2 !important; 
                            white-space: pre-wrap; 
                        }
                    </style>
                ')
            );
    }
}
