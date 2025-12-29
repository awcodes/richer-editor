<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Plugins;

use Awcodes\RicherEditor\Extensions\PhikiCodeBlock;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Phiki\Theme\Theme;
use Tiptap\Core\Extension;

class PhikiCodeBlockPlugin implements RichContentPlugin
{
    protected Theme|string|array|null $theme = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public function theme(Theme|string|array $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function getTheme(): Theme|string|array|null
    {
        return $this->theme;
    }

    /**
     * @return array<Extension>
     */
    public function getTipTapPhpExtensions(): array
    {
        return [
            app(PhikiCodeBlock::class, [
                'options' => [
                    'theme' => $this->getTheme() ?? [
                        'light' => Theme::GithubLight,
                        'dark' => Theme::GithubDark,
                    ],
                ],
            ]),
        ];
    }

    /**
     * @return array<string>
     */
    public function getTipTapJsExtensions(): array
    {
        return [];
    }

    /**
     * @return array<RichEditorTool>
     */
    public function getEditorTools(): array
    {
        return [];
    }

    /**
     * @return array<Action>
     */
    public function getEditorActions(): array
    {
        return [];
    }
}
