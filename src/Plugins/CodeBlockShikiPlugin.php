<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Plugins;

use Awcodes\RicherEditor\Extensions\ShikiCodeBlock;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\Heroicon;
use Phiki\Theme\Theme;
use Tiptap\Core\Extension;

class CodeBlockShikiPlugin implements RichContentPlugin
{
    protected Theme|string|null $defaultTheme = null;

    protected Theme|string|null $lightTheme = null;

    protected Theme|string|null $darkTheme = null;

    /**
     * @var array<string>|null
     */
    protected ?array $languages = null;

    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * The theme used to render code blocks. Accepts a Phiki Theme enum case or
     * any bundled Shiki theme name.
     */
    public function defaultTheme(Theme|string $theme): static
    {
        $this->defaultTheme = $theme;

        return $this;
    }

    /**
     * The light/dark themes to preload for code blocks. Accepts Phiki Theme
     * enum cases or any bundled Shiki theme names.
     */
    public function themes(Theme|string $light, Theme|string $dark): static
    {
        $this->lightTheme = $light;
        $this->darkTheme = $dark;

        return $this;
    }

    /**
     * Restrict the per-block language dropdown to a curated list of Shiki
     * language ids. Defaults to every language Shiki bundles when unset.
     *
     * @param  array<string>  $languages
     */
    public function languages(array $languages): static
    {
        $this->languages = $languages;

        return $this;
    }

    /**
     * @return array<Extension>
     */
    public function getTipTapPhpExtensions(): array
    {
        return [
            app(ShikiCodeBlock::class, [
                'options' => [
                    'theme' => $this->getRenderTheme(),
                ],
            ]),
        ];
    }

    /**
     * @return array<string>
     *
     * @throws Exception
     */
    public function getTipTapJsExtensions(): array
    {
        $src = FilamentAsset::getScriptSrc('richer-editor/code-block-shiki', 'awcodes/richer-editor');

        $params = array_filter([
            'defaultTheme' => $this->resolveTheme($this->defaultTheme),
            'lightTheme' => $this->resolveTheme($this->lightTheme),
            'darkTheme' => $this->resolveTheme($this->darkTheme),
            'languages' => $this->languages !== null ? implode(',', $this->languages) : null,
        ]);

        if ($params !== []) {
            $src .= (str_contains($src, '?') ? '&' : '?').http_build_query($params);
        }

        return [$src];
    }

    /**
     * @return array<RichEditorTool>
     */
    public function getEditorTools(): array
    {
        return [
            RichEditorTool::make('codeBlock')
                ->label(__('filament-forms::components.rich_editor.tools.code_block'))
                ->jsHandler('$getEditor()?.chain().focus().toggleCodeBlock().run()')
                ->icon(Heroicon::CodeBracket)
                ->iconAlias('forms:components.rich-editor.toolbar.code-block'),
        ];
    }

    /**
     * @return array<Action>
     */
    public function getEditorActions(): array
    {
        return [];
    }

    /**
     * The theme passed to Phiki when rendering code blocks outside the editor.
     * Mirrors the editor: a light/dark pair when both are set, otherwise the
     * single default theme (falling back to the in-editor default).
     *
     * @return Theme|string|array<string, Theme|string>
     */
    protected function getRenderTheme(): Theme|string|array
    {
        if ($this->lightTheme !== null && $this->darkTheme !== null) {
            return [
                'light' => $this->lightTheme,
                'dark' => $this->darkTheme,
            ];
        }

        return $this->defaultTheme ?? 'tokyo-night';
    }

    protected function resolveTheme(Theme|string|null $theme): ?string
    {
        return $theme instanceof Theme ? $theme->value : $theme;
    }
}
