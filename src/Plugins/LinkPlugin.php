<?php

namespace Awcodes\RicherEditor\Plugins;

use Awcodes\RicherEditor\Extensions\Link;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\Heroicon;
use Tiptap\Core\Extension;

class LinkPlugin implements RichContentPlugin
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
            //            app(Link::class),
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
            FilamentAsset::getScriptSrc('rich-content-plugins/link', 'awcodes/richer-editor'),
        ];
    }

    /**
     * @return array<RichEditorTool>
     */
    public function getEditorTools(): array
    {
        return [
            RichEditorTool::make('link')
                ->label(__('richer-editor::richer-editor.link.label'))
                ->icon(Heroicon::OutlinedLink)
                ->iconAlias('richer-editor:toolbar.link')
                ->action(arguments: '{ href: $getEditor().getAttributes(\'link\')?.href, id: $getEditor().getAttributes(\'link\')?.id, target: $getEditor().getAttributes(\'link\')?.target, hreflang: $getEditor().getAttributes(\'link\')?.hreflang, rel: $getEditor().getAttributes(\'link\')?.rel, referrerpolicy: $getEditor().getAttributes(\'link\')?.referrerpolicy }'),
        ];
    }

    /**
     * @return array<Action>
     *
     * @throws Exception
     */
    public function getEditorActions(): array
    {
        return [
            Action::make('link')
                ->label(__('richer-editor::richer-editor.link.label'))
                ->modalHeading(__('richer-editor::richer-editor.link.label'))
                ->modalWidth(Width::Large)
                ->fillForm(fn (array $arguments): array => [
                    'href' => $arguments['href'] ?? null,
                    'id' => $arguments['id'] ?? null,
                    'target' => $arguments['target'] ?? '',
                    'hreflang' => $arguments['hreflang'] ?? null,
                    'rel' => $arguments['rel'] ?? null,
                    'referrerpolicy' => $arguments['referrerpolicy'] ?? null,
                ])
                ->schema([
                    Grid::make(['md' => 3])
                        ->schema([
                            TextInput::make('href')
                                ->label(fn () => trans('richer-editor::richer-editor.link.href'))
                                ->columnSpan('full')
                                ->requiredWithout('id')
                                ->validationAttribute('URL'),
                            TextInput::make('id')
                                ->label(fn () => trans('richer-editor::richer-editor.link.id')),
                            Select::make('target')
                                ->label(fn () => trans('richer-editor::richer-editor.link.target.label'))
                                ->selectablePlaceholder(false)
                                ->options([
                                    '' => trans('richer-editor::richer-editor.link.target.self'),
                                    '_blank' => trans('richer-editor::richer-editor.link.target.new_window'),
                                    '_parent' => trans('richer-editor::richer-editor.link.target.parent'),
                                    '_top' => trans('richer-editor::richer-editor.link.target.top'),
                                ]),
                            TextInput::make('hreflang')
                                ->label(fn () => trans('richer-editor::richer-editor.link.hreflang')),
                            TextInput::make('rel')
                                ->label(fn () => trans('richer-editor::richer-editor.link.rel'))
                                ->columnSpan('full'),
                            TextInput::make('referrerpolicy')
                                ->label(fn () => trans('richer-editor::richer-editor.link.referrerpolicy'))
                                ->columnSpan('full'),
                        ]),
                ])
                ->action(function (array $arguments, array $data, RichEditor $component): void {
                    $isSingleCharacterSelection = ($arguments['editorSelection']['head'] ?? null) === ($arguments['editorSelection']['anchor'] ?? null);

                    if (blank($data['href'])) {
                        $component->runCommands(
                            [
                                ...($isSingleCharacterSelection ? [EditorCommand::make(
                                    'extendMarkRange',
                                    arguments: ['link'],
                                )] : []),
                                EditorCommand::make('unsetLink'),
                            ],
                            editorSelection: $arguments['editorSelection'],
                        );

                        return;
                    }

                    $component->runCommands(
                        [
                            ...($isSingleCharacterSelection ? [EditorCommand::make(
                                'extendMarkRange',
                                arguments: ['link'],
                            )] : []),
                            EditorCommand::make(
                                'setLink',
                                arguments: [[
                                    ...$data,
                                ]],
                            ),
                        ],
                        editorSelection: $arguments['editorSelection'],
                    );
                }),
        ];
    }
}
