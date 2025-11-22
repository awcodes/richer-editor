<?php

namespace Awcodes\RicherEditor;

use Awcodes\RicherEditor\Extensions\Link;
use Awcodes\RicherEditor\Support\RichContentRendererMixin;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class RicherEditorServiceProvider extends PackageServiceProvider
{
    public static string $name = 'richer-editor';

    public static string $viewNamespace = 'richer-editor';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasTranslations()
            ->hasViews(static::$viewNamespace)
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('awcodes/richer-editor');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }
    }

    public function packageRegistered(): void
    {
        $this->app->bind(
            \Tiptap\Marks\Link::class,
            Link::class,
        );

        RichEditor::macro('maxHeight', function (int | string | null $value = '400px'): static {
            $this->extraAttributes([
                'style' => "max-height: {$value};",
                'class' => 'has-max-height',
            ]);

            return $this;
        });
    }

    public function packageBooted(): void
    {
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        RichContentRenderer::mixin(new RichContentRendererMixin);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'awcodes/richer-editor';
    }

    /** @return array<Asset> */
    protected function getAssets(): array
    {
        return [
            Js::make(
                id: 'rich-content-plugins/code-block-lowlight',
                path: __DIR__ . '/../resources/dist/code-block-lowlight.js'
            )->loadedOnRequest(),
            Js::make(
                id: 'rich-content-plugins/code-block-shiki',
                path: __DIR__ . '/../resources/dist/code-block-shiki.js'
            )->loadedOnRequest(),
            Js::make(
                id: 'rich-content-plugins/embed',
                path: __DIR__ . '/../resources/dist/embed.js'
            )->loadedOnRequest(),
            Js::make(
                id: 'rich-content-plugins/id',
                path: __DIR__ . '/../resources/dist/id.js'
            )->loadedOnRequest(),
            Js::make(
                id: 'rich-content-plugins/video',
                path: __DIR__ . '/../resources/dist/video.js'
            )->loadedOnRequest(),
            Js::make(
                id: 'rich-content-plugins/fullscreen',
                path: __DIR__ . '/../resources/dist/fullscreen.js'
            )->loadedOnRequest(),
            Js::make(
                id: 'rich-content-plugins/link',
                path: __DIR__ . '/../resources/dist/link.js'
            )->loadedOnRequest(),
            Js::make(
                id: 'rich-content-plugins/figure',
                path: __DIR__ . '/../resources/dist/figure.js'
            )->loadedOnRequest(),
            Js::make(
                id: 'rich-content-plugins/emoji',
                path: __DIR__ . '/../resources/dist/emoji.js'
            )->loadedOnRequest(),
            Js::make(
                id: 'rich-content-plugins/slash-menu',
                path: __DIR__ . '/../resources/dist/slash-menu.js'
            )->loadedOnRequest(),
        ];
    }

    /** @return array<class-string> */
    protected function getCommands(): array
    {
        return [
            //
        ];
    }

    /** @return array<string> */
    protected function getRoutes(): array
    {
        return [];
    }

    /** @return array<string, mixed> */
    protected function getScriptData(): array
    {
        return [];
    }
}
