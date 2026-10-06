<?php

declare(strict_types=1);

namespace Workbench\App\Providers\Filament;

use Awcodes\RicherEditor\Plugins\EmbedPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Theme;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Workbench\App\Filament\Pages\Auth\Login;

class AdminPanelProvider extends PanelProvider
{
    public function register(): void
    {
        parent::register();

        // RichContentRenderer::toHtml() sanitizes its output, and Filament's sanitizer drops iframes, so an app that
        // renders EmbedPlugin content has to allow them.
        $this->app->extend(HtmlSanitizerConfig::class, fn (HtmlSanitizerConfig $config): HtmlSanitizerConfig => EmbedPlugin::allowEmbedsIn($config));
    }

    public function boot(): void
    {
        FilamentAsset::register([
            Theme::make('workbench', __DIR__ . '/../../../resources/dist/theme.css'),
        ], 'workbench');
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->colors(['primary' => Color::Indigo])
            ->theme('workbench')
            ->discoverResources(
                in: __DIR__ . '/../../Filament/Resources',
                for: 'Workbench\App\Filament\Resources',
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
