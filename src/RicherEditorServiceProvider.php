<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use League\HTMLToMarkdown\HtmlConverter;
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
            ->hasViews(static::$viewNamespace);
    }

    public function packageRegistered(): void
    {
        $this->app->bind(
            \Tiptap\Marks\Link::class,
            Extensions\Link::class,
        );
    }

    public function packageBooted(): void
    {
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        RichEditor::macro('maxHeight', function (int|string|null $value = '400px'): static {
            $this->extraAttributes([
                'style' => "max-height: {$value};",
                'class' => 'has-max-height',
            ]);

            return $this;
        });

        RichContentRenderer::macro('toMarkdown', function (?array $options = []): string {
            $editor = $this->getEditor();

            $this->processCustomBlocks($editor);
            $this->processFileAttachments($editor);
            $this->processMergeTags($editor);
            $this->processNodes($editor);

            return (new HtmlConverter($options))->convert(
                $editor->getHtml()
            );
        });

        RichContentRenderer::macro('phikiCodeBlocks', function (): static {
            $this->nodeProcessors[] = function (&$node): void {
                if ($node->type !== 'codeBlock') {
                    return;
                }

                if (! $node?->attrs->language) {
                    return;
                }

                $node->type = 'phikiCodeBlock';
            };

            return $this;
        });

        RichContentRenderer::macro('linkHeadings', function (int $level = 3, bool $wrap = false): static {
            $this->nodeProcessors[] = function (&$node) use ($level, $wrap): void {
                if ($node->type !== 'heading') {
                    return;
                }

                if ($node->attrs->level > $level) {
                    return;
                }

                if (! property_exists($node->attrs, 'id') || $node->attrs->id === null) {
                    $node->attrs->id = str(collect($node->content)->map(fn ($node) => $node->text ?? null)->implode(' '))->slug()->toString();
                }

                if ($wrap) {
                    $text = str(collect($node->content)->map(fn ($node) => $node->text ?? null)->implode(' '))->toString();

                    $node->content = [
                        (object) [
                            'type' => 'text',
                            'marks' => [
                                [
                                    'type' => 'link',
                                    'attrs' => [
                                        'href' => '#'.$node->attrs->id,
                                        'class' => 'toc-link',
                                    ],
                                ],
                            ],
                            'text' => $text,
                        ],
                    ];
                } else {
                    array_unshift($node->content, (object) [
                        'type' => 'text',
                        'text' => '#',
                        'marks' => [
                            [
                                'type' => 'link',
                                'attrs' => [
                                    'href' => '#'.$node->attrs->id,
                                    'class' => 'toc-link',
                                ],
                            ],
                        ],
                    ]);
                }
            };

            return $this;
        });
    }

    protected function getAssetPackageName(): ?string
    {
        return 'awcodes/richer-editor';
    }

    /** @return array<Asset> */
    protected function getAssets(): array
    {
        $dist = __DIR__.'/../resources/dist';

        return [
            Js::make(
                id: static::$name.'/code-block-lowlight',
                path: $dist.'/code-block-lowlight.js'
            )->loadedOnRequest(),

            Js::make(
                id: static::$name.'/code-block-shiki',
                path: $dist.'/code-block-shiki.js'
            )->loadedOnRequest(),

            Js::make(
                id: static::$name.'/embed',
                path: $dist.'/embed.js'
            )->loadedOnRequest(),

            Js::make(
                id: static::$name.'/id',
                path: $dist.'/id.js'
            )->loadedOnRequest(),

            Js::make(
                id: static::$name.'/video',
                path: $dist.'/video.js'
            )->loadedOnRequest(),

            Js::make(
                id: static::$name.'/fullscreen',
                path: $dist.'/fullscreen.js'
            )->loadedOnRequest(),

            Js::make(
                id: static::$name.'/link',
                path: $dist.'/link.js'
            )->loadedOnRequest(),

            Js::make(
                id: static::$name.'/figure',
                path: $dist.'/figure.js'
            )->loadedOnRequest(),

            Js::make(
                id: static::$name.'/emoji',
                path: $dist.'/emoji.js'
            )->loadedOnRequest(),

            Js::make(
                id: static::$name.'/slash-menu',
                path: $dist.'/slash-menu.js'
            )->loadedOnRequest(),
        ];
    }
}
