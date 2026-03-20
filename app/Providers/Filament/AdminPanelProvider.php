<?php

namespace App\Providers\Filament;

use App\Filament\Enums\NavigationGroup as NavGroupEnum;
use App\Filament\Pages\Auth\Login;
use App\Http\Middleware\EnsureSetupIsCompleted;
use App\Http\Middleware\FilamentAuthenticate;
use App\Livewire\Topbar;
use Filament\Actions\Action;
use Filament\Enums\GlobalSearchPosition;
use Filament\Facades\Filament;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login(Login::class)
            ->maxContentWidth('full')
            ->passwordReset()
            ->colors([
                'primary' => Color::Blue,
                'dark' => '#000000',
            ])
            ->brandName(config('app.name'))
            ->sidebarWidth('18rem')
            ->globalSearch(position: GlobalSearchPosition::Sidebar)
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->globalSearchFieldKeyBindingSuffix()
            ->topbarLivewireComponent(Topbar::class)
            ->navigationGroups([
                NavigationGroup::fromEnum($g1 = NavGroupEnum::PropertyManagement)
                    ->label(fn () => $g1->getLabel())
                    ->icon(Heroicon::OutlinedBuildingOffice2),
                NavigationGroup::fromEnum($g2 = NavGroupEnum::ContentManagement)
                    ->label(fn () => $g2->getLabel())
                    ->icon(Heroicon::OutlinedDocumentText),
                NavigationGroup::fromEnum($g3 = NavGroupEnum::Marketing)
                    ->label(fn () => $g3->getLabel())
                    ->icon(Heroicon::OutlinedMegaphone),
                NavigationGroup::fromEnum($g4 = NavGroupEnum::LocationPolicies)
                    ->label(fn () => $g4->getLabel())
                    ->icon(Heroicon::OutlinedMapPin),
                NavigationGroup::fromEnum($g5 = NavGroupEnum::Settings)
                    ->label(fn () => $g5->getLabel())
                    ->icon(Heroicon::OutlinedCog6Tooth),
            ])
            ->userMenuItems([
                Action::make('profile')
                    ->label(__('admin.my_profile'))
                    ->url('/admin-profile')
                    ->icon(Heroicon::OutlinedUser)
                    ->sort(-1),
                'logout' => Action::make('logout')
                    ->label(__('admin.log_out'))
                    ->color('danger')
                    ->icon(Heroicon::ArrowLeftEndOnRectangle)
                    ->url(fn (): string => Filament::getLogoutUrl())
                    ->postToUrl()
                    ->sort(PHP_INT_MAX),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
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
                \App\Http\Middleware\SetLocale::class,
                EnsureSetupIsCompleted::class,
            ])
            ->authMiddleware([
                FilamentAuthenticate::class,
            ])
            ->spa();

    }
}
