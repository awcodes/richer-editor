<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Plugins;

use Awcodes\RicherEditor\Extensions\Video;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\Heroicon;
use Tiptap\Core\Extension;

/**
 * @experimental This plugin is not ready for production use yet. Need to tie it into a file upload system.
 */
class VideoPlugin implements RichContentPlugin
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
            app(Video::class),
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
            FilamentAsset::getScriptSrc('richer-editor/video', 'awcodes/richer-editor'),
        ];
    }

    /**
     * @return array<RichEditorTool>
     */
    public function getEditorTools(): array
    {
        return [
            RichEditorTool::make('video')
                ->label(__('richer-editor::richer-editor.video.label'))
                ->action()
                ->icon(Heroicon::OutlinedFilm)
                ->iconAlias('richer-editor:toolbar.video'),
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
            Action::make('video')
                ->modalWidth(Width::Large)
                ->fillForm([
                    'options' => [
                        'controls',
                    ],
                    'responsive' => true,
                    'width' => 16,
                    'height' => 9,
                ])
                ->schema([
                    TextInput::make('src')
                        ->label(fn (): \Illuminate\Contracts\Translation\Translator|string|array => trans('richer-editor::richer-editor.video.url'))
                        ->live()
                        ->required(),
                    CheckboxList::make('options')
                        ->hiddenLabel()
                        ->gridDirection('row')
                        ->columns(3)
                        ->options(fn (Get $get): array => [
                            'autoplay' => trans('richer-editor::richer-editor.video.autoplay'),
                            'loop' => trans('richer-editor::richer-editor.video.loop'),
                            'controls' => trans('richer-editor::richer-editor.video.controls'),
                        ])
                        ->dehydrateStateUsing(fn (Get $get, $state): array => [
                            'autoplay' => in_array('autoplay', $state) ? 1 : 0,
                            'loop' => in_array('loop', $state) ? 1 : 0,
                            'controls' => in_array('controls', $state) ? 1 : 0,
                        ]),
                    Checkbox::make('responsive')
                        ->default(true)
                        ->live()
                        ->label(fn (): \Illuminate\Contracts\Translation\Translator|string|array => trans('richer-editor::richer-editor.video.responsive'))
                        ->afterStateUpdated(function (callable $set, $state): void {
                            if ($state) {
                                $set('width', '16');
                                $set('height', '9');
                            } else {
                                $set('width', '640');
                                $set('height', '480');
                            }
                        })
                        ->columnSpan('full'),
                    Group::make([
                        TextInput::make('width')
                            ->live()
                            ->required()
                            ->label(fn (): \Illuminate\Contracts\Translation\Translator|string|array => trans('richer-editor::richer-editor.video.width'))
                            ->default('16'),
                        TextInput::make('height')
                            ->live()
                            ->required()
                            ->label(fn (): \Illuminate\Contracts\Translation\Translator|string|array => trans('richer-editor::richer-editor.video.height'))
                            ->default('9'),
                    ])->columns(['md' => 2]),
                ])
                ->action(function (array $arguments, array $data, RichEditor $component): void {
                    $component->runCommands(
                        [
                            EditorCommand::make(
                                'setVideo',
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
