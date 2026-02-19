<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Spatie\Permission\Traits\HasRoles;

class PersonalPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('personal')
            ->path('personal')
            ->login()
            ->colors([ // estp de aca me sirve para poder dar un diferente estilo a este panel
                'primary' => Color::Amber,
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Purple,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->discoverResources(in: app_path('Filament/Personal/Resources'), for: 'App\Filament\Personal\Resources')
            ->discoverPages(in: app_path('Filament/Personal/Pages'), for: 'App\Filament\Personal\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Personal/Widgets'), for: 'App\Filament\Personal\Widgets')
            ->widgets([
                //AccountWidget::class,
                //FilamentInfoWidget::class,
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
            ->navigationItems([
                NavigationItem::make('RMCF')
                    ->url('https://www.realmadrid.com/landings/copas-de-europa-ganadas/', shouldOpenInNewTab: true)
                    ->icon(Heroicon::RocketLaunch)
                    ->sort(3),
            ])
            //Otra forma de switch panel
            ->userMenuItems([
                MenuItem::make( )
                    ->label('Admin')
                    ->url('/admin')
                    ->icon(Heroicon::Cog6Tooth)
            ]);
        //->topNavigation(); subir el navbar
    }
}
