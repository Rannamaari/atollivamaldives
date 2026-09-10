<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Pages\SocialSharingAnalytics;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\HtmlString;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel->default()->id('admin')->path('admin')->login()->brandName('Atolliva Maldives')->colors(['primary' => Color::Teal])
            ->renderHook(PanelsRenderHook::HEAD_END, fn (): HtmlString => new HtmlString(<<<'HTML'
                <style>
                    /* Keep rich-editor controls available while writing a long article. */
                    .fi-fo-rich-editor trix-toolbar {
                        position: sticky;
                        top: 5rem;
                        z-index: 30;
                        display: block;
                        margin: -0.25rem -0.25rem 0.75rem;
                        padding: 0.5rem;
                        border-radius: 0.75rem;
                        background: rgb(255 255 255 / 0.96);
                        box-shadow: 0 8px 20px rgb(15 23 42 / 0.10);
                        backdrop-filter: blur(10px);
                    }

                    .dark .fi-fo-rich-editor trix-toolbar {
                        background: rgb(31 41 55 / 0.96);
                    }
                </style>
                HTML))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->pages([Dashboard::class, SocialSharingAnalytics::class])->widgets([Widgets\AccountWidget::class, Widgets\FilamentInfoWidget::class])
            ->middleware([EncryptCookies::class, AddQueuedCookiesToResponse::class, StartSession::class, AuthenticateSession::class, ShareErrorsFromSession::class, VerifyCsrfToken::class, SubstituteBindings::class, DisableBladeIconComponents::class, DispatchServingFilamentEvent::class])
            ->authMiddleware([Authenticate::class]);
    }
}
