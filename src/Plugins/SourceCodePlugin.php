<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Plugins;

use DOMDocument;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Tiptap\Core\Extension;

class SourceCodePlugin implements RichContentPlugin
{
    protected ?Width $modalWidth = null;

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
        return [
            RichEditorTool::make('sourceCode')
                ->label(__('richer-editor::richer-editor.source_code.label'))
                ->action(arguments: '{ source: $getEditor().getHTML() }')
                ->icon(Heroicon::OutlinedCodeBracketSquare)
                ->iconAlias('richer-editor:toolbar.source_code'),
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
            Action::make('sourceCode')
                ->modalHeading(__('richer-editor::richer-editor.source_code.label'))
                ->modalWidth(fn (): Width => $this->getModalWidth())
                ->fillForm(function (array $arguments): array {
                    if (! $arguments['source']) {
                        return ['source' => '<p></p>'];
                    }

                    $dom = new DOMDocument;
                    $dom->encoding = 'UTF-8';
                    $dom->preserveWhiteSpace = false;
                    $dom->loadHTML(mb_convert_encoding($arguments['source'], 'HTML-ENTITIES', 'UTF-8'));
                    $bodyContent = '';
                    foreach ($dom->getElementsByTagName('body')->item(0)->childNodes as $node) {
                        $bodyContent .= $dom->saveXML($node)."\n";
                    }
                    $prettySource = mb_trim($bodyContent);

                    return ['source' => $prettySource];
                })
                ->schema([
                    CodeEditor::make('source')
                        ->hiddenLabel()
                        ->language(CodeEditor\Enums\Language::Html)
                        ->wrap()
                        ->extraAttributes(['class' => 'source_code_editor']),
                ])
                ->action(function (RichEditor $component, array $arguments, array $data): void {
                    $content = $data['source'] ?? '<p></p>';

                    $component->runCommands(
                        [
                            EditorCommand::make(
                                'setContent',
                                arguments: [$content],
                            ),
                        ],
                    );
                })
                ->stickyModalFooter(),
        ];
    }

    public function width(Width $width): static
    {
        $this->modalWidth = $width;

        return $this;
    }

    public function getModalWidth(): Width
    {
        return $this->modalWidth ?? Width::FiveExtraLarge;
    }
}
