<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Plugins;

use Awcodes\RicherEditor\Extensions\Figure;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\HtmlString;
use Tiptap\Core\Extension;

/**
 * @experimental This plugin is not ready for production use yet. Need to figure out proper rendering in the Figure extension. Possible bug with native Filament attachMedia tool. Possible solution https://github.com/ueberdosis/tiptap-php/pull/83
 */
class FigurePlugin implements RichContentPlugin
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
            app(Figure::class),
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
            FilamentAsset::getScriptSrc('richer-editor/figure', 'awcodes/richer-editor'),
        ];
    }

    /**
     * @return array<RichEditorTool>
     */
    public function getEditorTools(): array
    {
        return [
            RichEditorTool::make('imageToFigure')
                ->label(__('richer-editor::richer-editor.figure.image_to_figure.label'))
                ->icon(new HtmlString('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M5 20q-.825 0-1.412-.587T3 18V6q0-.825.588-1.412T5 4h14q.825 0 1.413.588T21 6v7q0 .425-.288.713T20 14t-.712-.288T19 13V6H5v12h9q.425 0 .713.288T15 19t-.288.713T14 20zm14 0h-1q-.425 0-.712-.288T17 19t.288-.712T18 18h1v-1q0-.425.288-.712T20 16t.713.288T21 17v1h1q.425 0 .713.288T23 19t-.288.713T22 20h-1v1q0 .425-.288.713T20 22t-.712-.288T19 21zm-5-5h3q.425 0 .713-.288T18 14v-.55q0-.325-.213-.537t-.537-.213t-.537.213t-.213.537v.05h-2v-3h2v.05q0 .325.213.538t.537.212t.538-.213t.212-.537V10q0-.425-.288-.712T17 9h-3q-.425 0-.712.288T13 10v4q0 .425.288.713T14 15m-7 0h3q.425 0 .713-.288T11 14v-.55q0-.325-.213-.537t-.537-.213t-.537.213t-.213.537v.05h-2v-3h2v.05q0 .325.213.538t.537.212t.538-.213t.212-.537V10q0-.425-.288-.712T10 9H7q-.425 0-.712.288T6 10v4q0 .425.288.713T7 15"/></svg>'))
                ->activeKey('figure')
                ->iconAlias('richer-editor:toolbar.figure.image_to_figure')
                ->jsHandler('$getEditor().chain().focus().imageToFigure().run()'),
            RichEditorTool::make('figureToImage')
                ->label(__('richer-editor::richer-editor.figure.figure_to_image.label'))
                ->icon(new HtmlString('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="m9.025 9l1.5 1.5H7.5v3h2q0-.225.15-.375t.375-.15h.45q.225 0 .375.15t.15.375v.5q0 .425-.288.713T10 15H7q-.425 0-.712-.288T6 14v-4q0-.425.288-.712T7 9zM21 6v10q0 .5-.325.763t-.7.262t-.675-.262T19 16V6H8.975q-.5 0-.75-.312T7.975 5t.25-.687t.75-.313H19q.825 0 1.413.588T21 6m-3 8q0 .225-.088.438t-.262.337L16.375 13.5h.125q0-.225.15-.375t.375-.15h.45q.225 0 .375.15t.15.375zm-1-5q.425 0 .713.288T18 10v.5q0 .225-.15.375t-.375.15h-.45q-.225 0-.375-.15t-.15-.375h-2v1.125l-1.5-1.5V10q0-.425.288-.712T14 9zM5 20q-.825 0-1.412-.587T3 18V6q0-.625.338-1.112t.862-.713L6.025 6H5v12h10.175L1.375 4.225q-.3-.3-.3-.712t.3-.713t.713-.3t.712.3l18.375 18.375q.3.3.3.713t-.3.712t-.712.3t-.713-.3L17.175 20z"/></svg>'))
                ->iconAlias('richer-editor:toolbar.figure.figure_to_image')
                ->jsHandler('$getEditor().chain().focus().figureToImage().run()'),
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
}
