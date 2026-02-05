<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Plugins;

use Awcodes\RicherEditor\Support\RichContentFaker;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use Tiptap\Core\Extension;

class FakerPlugin implements RichContentPlugin
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
                RichEditorTool::make(name: 'fakeHeading')
                    ->label(__(key: 'richer-editor::richer-editor.faker.heading'))
                    ->icon(Heroicon::H2)
                    ->iconAlias(alias: 'forms:components.rich-editor.toolbar.fake_heading')
                    ->action(action: 'faker', arguments: '{type: \'heading\'}'),
                RichEditorTool::make('fakeParagraphs')
                    ->label(__('richer-editor::richer-editor.faker.paragraphs'))
                    ->icon(new HtmlString('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><!-- Icon from Majesticons by Gerrit Halfmann - https://github.com/halfmage/majesticons/blob/main/LICENSE --><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 5h-3m-5 0h5m-5 0v10m0-10h-1c-1.667 0-5 1-5 5s3.333 5 5 5h1m0 4v-4m5 4V5"/></svg>'))
                    ->iconAlias('forms:components.rich-editor.toolbar.fake_paragraphs')
                    ->action(action: 'faker', arguments: '{type: \'paragraph\', count: 3}'),
                RichEditorTool::make('fakeBulletList')
                    ->label(__('richer-editor::richer-editor.faker.bullet_list'))
                    ->icon(Heroicon::ListBullet)
                    ->iconAlias('forms:components.rich-editor.toolbar.fake_bullet_list')
                    ->action(action: 'faker', arguments: '{type: \'list\', count: 3, ordered: false}'),
                RichEditorTool::make('fakeNumberedList')
                    ->label(__('richer-editor::richer-editor.faker.numbered_list'))
                    ->icon(Heroicon::NumberedList)
                    ->iconAlias('forms:components.rich-editor.toolbar.fake_numbered_list')
                    ->action(action: 'faker', arguments: '{type: \'list\', count: 3, ordered: true}'),
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
        return [
            Action::make('faker')
                ->action(function (array $arguments, array $data, RichEditor $component): void {
                    $content = RichContentFaker::make();

                    if (empty($arguments['type'] ?? null)) {
                        return;
                    }

                    match ($arguments['type']) {
                        'heading' => $content->heading(),
                        'paragraph' => $content->paragraphs($arguments['count'] ?? 1),
                        'list' => $content->list(
                            count: $arguments['count'] ?? 3,
                            ordered: $arguments['ordered'] ?? false,
                        ),
                    };

                    $component->runCommands(
                        [
                            EditorCommand::make(
                                'insertContent',
                                arguments: [$content->asHtml()],
                            ),
                        ],
                        editorSelection: $arguments['editorSelection'],
                    );
                }),
        ];
    }
}
