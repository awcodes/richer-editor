<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Resources\Posts\Schemas;

use Awcodes\RicherEditor\Blocks\HighlightedCodeBlock;
use Awcodes\RicherEditor\Plugins\CodeBlockShikiPlugin;
use Awcodes\RicherEditor\Plugins\EmbedPlugin;
use Awcodes\RicherEditor\Plugins\IdPlugin;
use Awcodes\RicherEditor\Support\TableOfContents;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Phiki\Theme\Theme;
use Workbench\App\Models\Post;

class PostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['lg' => 4])
                    ->schema([
                        Section::make('On this page')
                            ->schema([
                                TextEntry::make('toc')
                                    ->hiddenLabel()
                                    ->state(fn (Post $record): HtmlString => new HtmlString(
                                        TableOfContents::make($record->content)->asHtml(),
                                    )),
                            ])
                            ->extraAttributes(['data-focus' => 'table-of-contents']),
                        Section::make()
                            ->schema([
                                TextEntry::make('content')
                                    ->hiddenLabel()
                                    ->prose()
                                    ->state(fn (Post $record): HtmlString => new HtmlString(
                                        RichContentRenderer::make($record->content)
                                            ->plugins([
                                                CodeBlockShikiPlugin::make()
                                                    ->themes(light: Theme::GithubLight, dark: Theme::GithubDark),
                                                EmbedPlugin::make(),
                                                IdPlugin::make(),
                                            ])
                                            ->customBlocks([HighlightedCodeBlock::class])
                                            // A macro registered by RicherEditorServiceProvider, which phpstan only sees when the app is booted.
                                            // @phpstan-ignore method.notFound
                                            ->linkHeadings(level: 3)
                                            ->toHtml(),
                                    )),
                            ])
                            ->columnSpan(['lg' => 3])
                            ->extraAttributes(['data-focus' => 'rendered-content']),
                    ])
                    ->extraAttributes(['data-focus' => 'rendered-post'])
                    ->columnSpanFull(),
            ]);
    }
}
