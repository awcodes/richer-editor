<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Resources\Posts\Schemas;

use Awcodes\RicherEditor\Blocks\HighlightedCodeBlock;
use Awcodes\RicherEditor\Plugins\CodeBlockShikiPlugin;
use Awcodes\RicherEditor\Plugins\DebugPlugin;
use Awcodes\RicherEditor\Plugins\EmbedPlugin;
use Awcodes\RicherEditor\Plugins\EmojiPlugin;
use Awcodes\RicherEditor\Plugins\FakerPlugin;
use Awcodes\RicherEditor\Plugins\FullScreenPlugin;
use Awcodes\RicherEditor\Plugins\IdPlugin;
use Awcodes\RicherEditor\Plugins\LinkPlugin;
use Awcodes\RicherEditor\Plugins\SlashMenuPlugin;
use Awcodes\RicherEditor\Plugins\SourceCodePlugin;
use Awcodes\RicherEditor\Tools\ToolGroup;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Phiki\Theme\Theme;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->extraFieldWrapperAttributes(['data-focus' => 'title-field']),
                RichEditor::make('content')
                    ->label('Richer Editor')
                    ->plugins([
                        CodeBlockShikiPlugin::make()
                            ->themes(light: Theme::GithubLight, dark: Theme::GithubDark)
                            ->languages(['php', 'blade', 'js', 'ts', 'css', 'html', 'json', 'bash']),
                        DebugPlugin::make(),
                        EmbedPlugin::make(),
                        EmojiPlugin::make(),
                        FakerPlugin::make(),
                        FullScreenPlugin::make(),
                        IdPlugin::make(),
                        LinkPlugin::make(),
                        SlashMenuPlugin::make()
                            ->items([
                                'h2',
                                'h3',
                                'bulletList',
                                'blockquote',
                                'codeBlock',
                                'embed',
                                HighlightedCodeBlock::class,
                            ]),
                        SourceCodePlugin::make(),
                    ])
                    ->customBlocks([
                        HighlightedCodeBlock::class,
                    ])
                    ->tools([
                        ToolGroup::make('headingTools')
                            ->label('Headings')
                            ->icon(Heroicon::H1)
                            ->displayAsLabel()
                            ->items(['h1', 'h2', 'h3'])
                            ->extraAttributes(['data-focus-action' => 'heading-tools']),
                        ToolGroup::make('developerTools')
                            ->label('Developer tools')
                            ->icon(Heroicon::Sparkles)
                            ->displayAsLabel()
                            ->extraAttributes(['data-focus-action' => 'developer-tools'])
                            ->items([
                                'sourceCode',
                                'fullscreen',
                                'debug',
                                'fakeHeading',
                                'fakeParagraphs',
                            ]),
                    ])
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'strike', 'link'],
                        ['headingTools'],
                        ['bulletList', 'orderedList', 'blockquote', 'codeBlock'],
                        ['embed', 'attachFiles', 'customBlocks'],
                        ['developerTools'],
                        ['slashMenu'],
                    ])
                    ->maxHeight('500px')
                    ->extraFieldWrapperAttributes(['data-focus' => 'content-editor'])
                    ->columnSpanFull(),
            ]);
    }
}
