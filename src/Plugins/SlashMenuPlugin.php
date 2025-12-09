<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Plugins;

use Awcodes\RicherEditor\Tools\SlashMenu;
use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Support\Facades\FilamentAsset;
use Tiptap\Core\Extension;

class SlashMenuPlugin implements RichContentPlugin
{
    use EvaluatesClosures;

    protected array|Closure|null $items = null;

    protected string|Closure|null $noResultsMessage = null;

    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * @return array<Extension>
     */
    public function getTipTapPhpExtensions(): array
    {
        return [];
    }

    /**
     * @return array<string>
     *
     * @throws Exception
     */
    public function getTipTapJsExtensions(): array
    {
        return [
            FilamentAsset::getScriptSrc('richer-editor/slash-menu', 'awcodes/richer-editor'),
        ];
    }

    /**
     * @return array<RichEditorTool>
     */
    public function getEditorTools(): array
    {
        return [
            SlashMenu::make('slashMenu')
                ->hiddenLabel()
                ->noResultsMessage($this->getNoResultsMessage())
                ->items($this->getItems()),
        ];
    }

    /**
     * @return array<Action>
     *
     * @throws Exception
     */
    public function getEditorActions(): array
    {
        return [];
    }

    public function items(array|Closure $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function noResultsMessage(string|Closure $message): static
    {
        $this->noResultsMessage = $message;

        return $this;
    }

    public function getItems(): array
    {
        return $this->evaluate($this->items) ?? [];
    }

    public function getNoResultsMessage(): ?string
    {
        return $this->evaluate($this->noResultsMessage) ?? __('richer-editor::richer-editor.tools.slash_menu.no_results');
    }
}
