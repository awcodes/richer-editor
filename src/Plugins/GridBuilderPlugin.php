<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Plugins;

use Awcodes\RicherEditor\Extensions\GridBuilder;
use Awcodes\RicherEditor\Extensions\GridBuilderColumn;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\Heroicon;
use Tiptap\Core\Extension;

class GridBuilderPlugin implements RichContentPlugin
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
        return [
            app(GridBuilder::class),
            app(GridBuilderColumn::class),
        ];
    }

    /**
     * @return array<string>
     *
     * @throws Exception
     */
    public function getTipTapJsExtensions(): array
    {
        return [
            FilamentAsset::getScriptSrc('richer-editor/grid-builder-column', 'awcodes/richer-editor'),
            FilamentAsset::getScriptSrc('richer-editor/grid-builder', 'awcodes/richer-editor'),
        ];
    }

    /**
     * @return array<RichEditorTool>
     */
    public function getEditorTools(): array
    {
        return [
            RichEditorTool::make('gridBuilder')
                ->label(__('richer-editor::richer-editor.grid_builder.label'))
                ->jsHandler('$getEditor()?.chain().focus().insertGridBuilder().run()')
                ->icon(Heroicon::OutlinedViewColumns)
                ->iconAlias('richer-editor:toolbar.grid-builder'),
        ];
    }

    /**
     * @return array<Action>
     */
    public function getEditorActions(): array
    {
        return [];
    }
}
