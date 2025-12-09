<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Plugins;

use Awcodes\RicherEditor\Extensions\Embed;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\Heroicon;
use Tiptap\Core\Extension;

class EmbedPlugin implements RichContentPlugin
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
            app(Embed::class),
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
            FilamentAsset::getScriptSrc('richer-editor/embed', 'awcodes/richer-editor'),
        ];
    }

    /**
     * @return array<RichEditorTool>
     */
    public function getEditorTools(): array
    {
        return [
            RichEditorTool::make('embed')
                ->label(__('richer-editor::richer-editor.embed.label'))
                ->action()
                ->icon(Heroicon::OutlinedTv)
                ->iconAlias('richer-editor:toolbar.embed'),
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
            Action::make('embed')
                ->modalWidth(Width::Large)
                ->fillForm([
                    'responsive' => true,
                    'width' => 16,
                    'height' => 9,
                ])
                ->schema([
                    TextInput::make('src')
                        ->label(fn (): \Illuminate\Contracts\Translation\Translator|string|array => trans('richer-editor::richer-editor.embed.url'))
                        ->live()
                        ->required(),
                    CheckboxList::make('options')
                        ->hiddenLabel()
                        ->gridDirection('row')
                        ->columns(3)
                        ->visible(fn (Get $get): mixed => $get('src'))
                        ->options(function (Get $get): array {
                            if (str_contains((string) $get('src'), 'youtu')) {
                                return [
                                    'controls' => trans('richer-editor::richer-editor.embed.controls'),
                                    'nocookie' => trans('richer-editor::richer-editor.embed.nocookie'),
                                ];
                            }

                            return [
                                'autoplay' => trans('richer-editor::richer-editor.embed.autoplay'),
                                'loop' => trans('richer-editor::richer-editor.embed.loop'),
                                'title' => trans('richer-editor::richer-editor.embed.title'),
                                'byline' => trans('richer-editor::richer-editor.embed.byline'),
                                'portrait' => trans('richer-editor::richer-editor.embed.portrait'),
                            ];
                        })
                        ->dehydrateStateUsing(function (Get $get, $state): array {
                            if (str_contains((string) $get('src'), 'youtu')) {
                                return [
                                    'controls' => in_array('controls', $state) ? 1 : 0,
                                    'nocookie' => in_array('nocookie', $state) ? 1 : 0,
                                ];
                            }

                            return [
                                'autoplay' => in_array('autoplay', $state) ? 1 : 0,
                                'loop' => in_array('loop', $state) ? 1 : 0,
                                'title' => in_array('title', $state) ? 1 : 0,
                                'byline' => in_array('byline', $state) ? 1 : 0,
                                'portrait' => in_array('portrait', $state) ? 1 : 0,
                            ];
                        }),
                    TimePicker::make('start_at')
                        ->label(fn (): \Illuminate\Contracts\Translation\Translator|string|array => trans('richer-editor::richer-editor.embed.start_at'))
                        ->live()
                        ->date(false)
                        ->visible(fn (Get $get): bool => str_contains((string) $get('src'), 'youtu'))
                        ->afterStateHydrated(function (TimePicker $component, $state): void {
                            if (! $state) {
                                return;
                            }

                            $state = CarbonInterval::seconds($state)->cascade();
                            $component->state(Carbon::parse($state->h.':'.$state->i.':'.$state->s)->format('Y-m-d H:i:s'));
                        })
                        ->dehydrateStateUsing(function ($state): int|float {
                            if (! $state) {
                                return 0;
                            }

                            return Carbon::parse($state)->diffInSeconds('00:00:00');
                        }),
                    Checkbox::make('responsive')
                        ->default(true)
                        ->live()
                        ->label(fn (): \Illuminate\Contracts\Translation\Translator|string|array => trans('richer-editor::richer-editor.embed.responsive'))
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
                            ->label(fn (): \Illuminate\Contracts\Translation\Translator|string|array => trans('richer-editor::richer-editor.embed.width'))
                            ->default('16'),
                        TextInput::make('height')
                            ->live()
                            ->required()
                            ->label(fn (): \Illuminate\Contracts\Translation\Translator|string|array => trans('richer-editor::richer-editor.embed.height'))
                            ->default('9'),
                    ])->columns(['md' => 2]),
                ])
                ->action(function (array $arguments, array $data, RichEditor $component): void {
                    $component->runCommands(
                        [
                            EditorCommand::make(
                                'setEmbed',
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
