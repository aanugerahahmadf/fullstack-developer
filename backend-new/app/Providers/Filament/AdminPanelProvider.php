<?php

namespace App\Providers\Filament;

use Filament\Navigation\MenuItem;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Support\Assets\Css;
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
use Filament\View\PanelsRenderHook;

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
            ->brandLogo(fn () => view('vendor.filament.components.sidebar.brand'))
            ->darkModeBrandLogo(fn () => view('vendor.filament.components.sidebar.brand-dark'))
            ->assets([
                new Css('custom-filament', resource_path('css/filament.css')),
            ])
            ->topbar(true)
            ->colors([
                'primary' => Color::Red,
            ])
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn () => view('filament.footer'),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            // ->pages([
            //     Dashboard::class,
            // ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([

            ])
            ->font('Inter')
            //->sidebarFullyCollapsibleOnDesktop()
            ->topNavigation()
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Dashboard'),
                    // ->collapsed(),
                NavigationGroup::make()
                    ->label('All Roles For User'),
                    // ->collapsed(),
                    // ->icon('zondicon-shield'),
                // NavigationGroup::make()
                //     ->label('User')
                //     ->icon('bxs-user-account')
                //     ->collapsed(),
                NavigationGroup::make()
                    ->label('CRUD For All Pages'),
                    // ->collapsed(),
                    // ->icon('bxs-map-pin')
                // NavigationGroup::make()
                //     ->label('Contact Us')
                //     ->icon('bxs-message-detail')
                //     ->collapsed(),
                // NavigationGroup::make()
                //     ->label('Account'),
                //     ->collapsed()
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
            ->plugins([
                // Use a custom plugin that doesn't register the default RoleResource
                new class extends FilamentShieldPlugin {
                    public function register(Panel $panel): void
                    {
                        // Don't register the default RoleResource
                        // Our custom RoleResource will be discovered automatically
                    }
                    
                    public function boot(Panel $panel): void
                    {
                        // Set the navigation group for the plugin
                        $this->navigationGroup('Filament Shield');
                    }
                }
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
                    ->shouldRegisterNavigation(false) // Hide from navigation
                    ->setIcon('bxs-user-account')
                    ->setSort(10)
                    ->shouldShowAvatarForm(
                        value: true,
                        directory: 'avatars', // image will be stored in 'storage/app/public/avatars
                        rules: 'mimes:jpeg,png|max:102400000' // only accept jpeg and png files with a maximum size of 100GB (102400000 KB)
                    )
                    ->customProfileComponents([
                        UsernameComponent::class,
                    ])
            )
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(fn (): string => Auth::user()?->name ?? 'Profile')
                    ->url(fn (): string => EditProfilePage::getUrl())
                    ->icon('eos-account-circle')
                    ->visible(function (): bool {
                        return Auth::check();
                    }),
            ]);
    }
}