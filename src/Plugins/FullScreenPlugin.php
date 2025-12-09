<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Plugins;

use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\HtmlString;
use Tiptap\Core\Extension;

class FullScreenPlugin implements RichContentPlugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    /** @return array<Extension> */
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
        return [];
    }

    /** @return array<RichEditorTool> */
    public function getEditorTools(): array
    {
        return [
            RichEditorTool::make('fullscreen')
                ->label('Fullscreen')
                ->icon(new HtmlString('<svg class="enter-fullscreen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill="none" d="M0 0h24v24H0z"/><path d="M20 3h2v6h-2V5h-4V3h4zM4 3h4v2H4v4H2V3h2zm16 16v-4h2v6h-6v-2h4zM4 19h4v2H2v-6h2v4z"/></svg><svg class="exit-fullscreen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill="none" d="M0 0h24v24H0z"/><path d="M18 7h4v2h-6V3h2v4zM8 9H2V7h4V3h2v6zm10 8v4h-2v-6h6v2h-4zM8 15v6H6v-4H2v-2h6z"/></svg>'))
                ->activeJsExpression('$root.classList.contains(\'fullscreen\')')
                ->jsHandler('window.toggleRichEditorFullscreen($root)')
                ->extraAttributes([
                    'class' => 'fullscreen-toggle',
                    'x-load-js' => '[\''.FilamentAsset::getScriptSrc('richer-editor/fullscreen', 'awcodes/richer-editor').'\']',
                ]),
        ];
    }

    /** @return array<Action> */
    public function getEditorActions(): array
    {
        return [];
    }
}
