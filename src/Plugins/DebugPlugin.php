<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Plugins;

use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Support\Icons\Heroicon;
use Tiptap\Core\Extension;

class DebugPlugin implements RichContentPlugin
{
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
        return [];
    }

    /**
     * @return array<RichEditorTool>
     */
    public function getEditorTools(): array
    {
        return app()->isLocal()
            ? [
                RichEditorTool::make('debug')
                    ->label(__('richer-editor::richer-editor.debug.label'))
                    ->icon(Heroicon::OutlinedBugAnt)
                    ->iconAlias('richer-editor:toolbar.debug')
                    ->jsHandler('console.log($getEditor())'),
            ]
            : [];
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
}
