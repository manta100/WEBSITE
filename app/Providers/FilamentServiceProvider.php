<?php

namespace App\Providers;

use Filament\Filament;
use Filament\Panel;
use Filament\PanelProvider;
use App\Filament\Pages\Dashboard as AdminDashboard;
use App\Filament\Resources;
use Illuminate\Support\Facades\Vite;

class FilamentServiceProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->brandLogo(fn () => view('filament.admin.logo'))
            ->colors([
                'primary' => '#3b82f6',
                'secondary' => '#64748b',
                'success' => '#22c55e',
                'danger' => '#ef4444',
                'warning' => '#f59e0b',
                'info' => '#06b6d4',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->pages([
                AdminDashboard::class,
            ])
            ->widgets([
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\RevenueChart::class,
            ])
            ->middleware([
                \Illuminate\Cookie\Middleware\EncryptCookies::class,
                \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                \Illuminate\Session\Middleware\StartSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
            ])
            ->authMiddleware([
                \App\Http\Middleware\AdminAuthenticate::class,
            ])
            ->tenant(\App\Models\Tenant::class, ownershipRelationship: 'tenants')
            ->databaseNotifications();
    }
}
