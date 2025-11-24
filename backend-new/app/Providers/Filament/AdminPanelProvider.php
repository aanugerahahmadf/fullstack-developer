<?php

namespace App\Providers\Filament;

use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Navigation\NavigationGroup;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Filament\Pages\Auth\Login;
use App\Livewire\UsernameComponent;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->brandName('Kilang Pertamina Internasional')
            //->brandLogo(asset('images/logo-pertamina.png'))
            ->brandLogoHeight('7rem')
            ->colors([
                'primary' => Color::Red,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([

            ])
            ->sidebarCollapsibleOnDesktop()
            //->sidebarWidth('16rem')
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Playlist And Maps')
                    ->icon('bxs-map-pin')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Contact Us')
                    ->icon('bxs-message-detail')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Account')
                    ->icon('bxs-user-account'),
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
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->plugin(
                FilamentEditProfilePlugin::make()
                    ->setTitle('Profile')
                    ->setNavigationLabel('Profile')
                    ->setNavigationGroup('Account')
                    ->setSort(10)
                    ->shouldShowAvatarForm(
                        value: true,
                        directory: 'avatars', // image will be stored in 'storage/app/public/avatars
                        rules: 'mimes:jpeg,png|max:51200' // only accept jpeg and png files with a maximum size of 50MB (51200 KB)
                    )
                    ->customProfileComponents([
                        UsernameComponent::class,
                    ])
            )
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(fn (): string => Auth::user()?->name ?? 'Profile')
                    ->url(fn (): string => EditProfilePage::getUrl())
                    ->icon('elusive-adjust-alt')
                    ->visible(function (): bool {
                        return Auth::check();
                    }),
            ]);
    }
}