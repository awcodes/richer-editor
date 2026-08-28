<img src="https://res.cloudinary.com/aw-codes/image/upload/c_scale,w_1200/v1772226864/thumbnails/awcodes-richer-editor.webp" alt="richer editor opengraph image" width="1200" height="auto" class="filament-hidden" style="width: 100%;" />

# Richer Editor 

A collection of extensions and tools to enhance the Filament Rich Editor field.

[![Latest Version](https://img.shields.io/github/release/awcodes/richer-editor.svg?style=flat-square&color=blue&label=Release)](https://github.com/awcodes/richer-editor/releases)
[![MIT Licensed](https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/awcodes/richer-editor.svg?style=flat-square&color=blue&label=Downloads)](https://packagist.org/packages/awcodes/richer-editor)
[![GitHub Repo stars](https://img.shields.io/github/stars/awcodes/richer-editor?style=flat-square&color=blue&label=Stars)](https://github.com/awcodes/richer-editor/stargazers)

## Compatibility

| Package Version | Filament Version |
|-----------------|------------------|
| 1.x             | 4.x              |
| 2.x             | 4.x & 5.x        |

<!-- [docs_start] -->

## Installation

You can install the package via composer:

```bash
composer require awcodes/richer-editor
```

> [!IMPORTANT]
> If you have not set up a custom theme and are using Filament Panels, follow the instructions in the [Filament Docs](https://filamentphp.com/docs/5.x/styling/overview#creating-a-custom-theme) first.

After setting up a custom theme, add the plugin's CSS and views to your theme.css file or your app.css file if using the standalone packages.

```css
@import '../../../../vendor/awcodes/richer-editor/resources/css/index.css';

@source '../../../../vendor/awcodes/richer-editor/resources/views/**/*.blade.php';
```

## Editor Usage

> [!WARNING]
> The following plugins are experimental and should not be used at the moment. See their docblocks for more information.
> - FigurePlugin
> - VideoPlugin

### Plugins

```php
use Awcodes\RicherEditor\Plugins\DebugPlugin;
use Awcodes\RicherEditor\Plugins\EmbedPlugin;
use Awcodes\RicherEditor\Plugins\EmojiPlugin;
use Awcodes\RicherEditor\Plugins\FullScreenPlugin;
use Awcodes\RicherEditor\Plugins\IdPlugin;
use Awcodes\RicherEditor\Plugins\LinkPlugin;
use Awcodes\RicherEditor\Plugins\SourceCodePlugin;
use Awcodes\RicherEditor\Plugins\FakerPlugin;
use Awcodes\RicherEditor\Plugins\CodeBlockShikiPlugin;

RichEditor::make('content')
    ->plugins([
        DebugPlugin::make(), // only works in local environment
        EmbedPlugin::make(),
        EmojiPlugin::make(), // Doesn't have a toolbar button
        FullScreenPlugin::make(),
        IdPlugin::make(), // Doesn't have a toolbar button
        LinkPlugin::make(), // Requires IdPlugin
        SourceCodePlugin::make(),
        FakerPlugin::make(), // only works in local environment
        CodeBlockShikiPlugin::make(),
    ])
    ->toolbarButtons([
        ['embed', 'sourceCode', 'fullscreen', 'debug', 'fakeHeading', 'fakeParagraphs', 'fakeBulletList', 'fakeNumberedList', 'codeBlock'],
    ])
```

### Max Height

```php
use Filament\Forms\Components\RichEditor\RichEditorTool;

RichEditor::make('content')
    ->maxHeight('400px')
```

### Nested Tool Groups (Dropdowns)

```php
use Awcodes\RicherEditor\Tools\ToolGroup;
use Filament\Forms\Components\RichEditor\RichEditorTool;

RichEditor::make('content')
    ->tools([
        ToolGroup::make('headingTools')
            ->label('Headings')
            ->icon(Heroicon::H1)
            ->displayAsLabel()
            ->items([
                'h1', 
                'h2', 
                'h3',
                RichEditorTool::make('h4')...
            ]),
        ToolGroup::make('devTools')
            ->label('Developer Tools')
            ->icon(Heroicon::Sparkles)
            ->displayAsLabel()
            ->items([
                'debug', 
                'fakeHeading', 
                'fakeParagraphs', 
                'fakeBulletList', 
                'fakeNumberedList'
            ]),
    ])
    ->toolbarButtons([
        ['headingTools', 'devTools'],
    ])
```

### Prebuilt Tools

The following tools are depreciated and will be removed in a future release. Please use the heading tools provided by Filament instead.

* Heading Four Tool
* Heading Five Tool
* Heading Six Tool

```php
use Awcodes\RicherEditor\Tools\HeadingFourTool;
use Awcodes\RicherEditor\Tools\HeadingFiveTool;
use Awcodes\RicherEditor\Tools\HeadingSixTool;

RichEditor::make('content')
    ->tools([
        HeadingFourTool::make(),
        HeadingFiveTool::make(),
        HeadingSixTool::make(),
    ])
    ->toolbarButtons([
        ['h4', 'h5', 'h6'],
    ])
```

### Prebuilt Blocks

#### Highlighted Code Block (Phiki)

```php
use Awcodes\RicherEditor\Blocks\HighlightedCodeBlock;

RichEditor::make('content')
    ->blocks([
        HighlightedCodeBlock::class,
    ])

// when rendering the content, you can change the theme using any of Phiki's supported themes. See https://phiki.dev/multi-themes

use Awcodes\RicherEditor\Blocks\HighlightedCodeBlock;
use Phiki\Theme\Theme;

RichContentRenderer::make($content)
    ->customBlocks([
        HighlightedCodeBlock::class => [
            'light' => Theme::GithubLight,
            'dark' => Theme::GithubDark,
        ],
    ])
    ->toHtml()
```

### Code Block Syntax Highlighting (Shiki)

The `CodeBlockShikiPlugin` highlights code blocks in the editor using [Shiki](https://shiki.style). You can set the theme used to render code blocks, and optionally supply separate light/dark themes.

Themes accept either a [Phiki `Theme`](https://phiki.dev) enum case or any bundled Shiki theme name as a string. See [Shiki's themes](https://shiki.style/themes) for the full list.

```php
use Awcodes\RicherEditor\Plugins\CodeBlockShikiPlugin;
use Phiki\Theme\Theme;

RichEditor::make('content')
    ->plugins([
        CodeBlockShikiPlugin::make()
            ->defaultTheme(Theme::TokyoNight)
            ->themes(light: Theme::GithubLight, dark: Theme::GithubDark),
    ])

// strings work too
CodeBlockShikiPlugin::make()
    ->defaultTheme('tokyo-night')
    ->themes(light: 'github-light', dark: 'github-dark')
```

When you supply `themes()`, the light theme is rendered inline and the dark theme is exposed via CSS variables. The dark variant is applied automatically under Filament's dark mode by the styles in this package's `resources/css/index.css` — make sure it is imported into your theme (see [Installation](#installation)). Without `themes()`, only `defaultTheme` is used and code blocks stay a single theme.

Each code block shows a language dropdown so authors can switch the highlighting language. By default it lists every language Shiki bundles; pass `languages()` to curate the list:

```php
CodeBlockShikiPlugin::make()
    ->languages(['php', 'blade', 'js', 'ts', 'css', 'html', 'json', 'bash'])
```

#### Rendering outside the editor

Shiki runs in the browser, so it cannot highlight code blocks when stored content is rendered server-side. The plugin ships a PHP Tiptap extension that highlights `codeBlock` nodes with [Phiki](https://phiki.dev) using the same themes. Register the plugin on the renderer with the same theme configuration to keep rendered output in sync with the editor:

```php
use Awcodes\RicherEditor\Plugins\CodeBlockShikiPlugin;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Phiki\Theme\Theme;

RichContentRenderer::make($content)
    ->plugins([
        CodeBlockShikiPlugin::make()
            ->themes(light: Theme::GithubLight, dark: Theme::GithubDark),
    ])
    ->toHtml()
```

The rendered output uses the `.phiki` styles in `resources/css/index.css` for dark-mode switching, so no extra setup is required beyond importing that file.

## Rendering Usage

### Rendering Headings as links

```php
use Filament\Forms\Components\RichEditor\RichContentRenderer;

RichContentRenderer::make($content)
    ->linkHeadings(level: 3, wrap: false)
    ->toHtml()
```

### Rendering as Markdown

This feature uses [HTML To Markdown for PHP](https://github.com/thephpleague/html-to-markdown) by [thephpleague](https://github.com/thephpleague). Please see their documentation for available options.

```php
use Filament\Forms\Components\RichEditor\RichContentRenderer;

RichContentRenderer::make($content)
    ->toMarkdown(options: [])
```

### Rendering native code blocks with Phiki syntax highlighting.

> [!CAUTION]
> This should **NOT** be used globally as it will not work with Filament's rich content attributes when storing/reading content in the database when in a form context.

```php
use Awcodes\RicherEditor\Plugins\PhikiCodeBlockPlugin;
use Filament\Forms\Components\RichEditor\RichContentRenderer;

RichContentRenderer::make($content)
    ->plugins([
        PhikiCodeBlockPlugin::make(),
    ])
    ->phikiCodeBlocks()
    ->toHtml();
```

### Rendering Table of Contents

```php
use Awcodes\RicherEditor\Support\TableOfContents;

TableOfContents::make($content)
    ->asHtml();
    
/** or as an array to handle the output yourself */

$toc = TableOfContents::make($content)
    ->asArray();
```

## Utilities

### Rich Content Faker

```php
use Awcodes\RicherEditor\Support\RichContentFaker;

$richContent = RichContentFaker::make()
    ->heading(level: 2)
    ->paragraphs(
        count: 1, 
        links: false, 
        code: false, 
        bold: false, 
        italic: false, 
        underline: false, 
        strike: false, 
        subscript: false, 
        superscript: false, 
        mergeTags: [], 
        highlight: false
    )
    ->lead(paragraphs: 1, links: false)
    ->small(paragraphs: 1, links: false)
    ->list(count: 3, links: false, ordered: false)
    ->image(source: null, width: 1280, height: 720)
    ->details(open: false, links: false)
    ->codeBlock(language: 'sh', prefix: 'language-')
    ->blockquote()
    ->hr()
    ->br()
    ->table(cols: null)
    ->grid(cols: [1,1,1], breakpoint: 'md')
    ->customBlock(
        id: 'batman', 
        config: [
            'name' => 'Batman', 
            'color' => 'black', 
            'side' => 'hero'
        ]
    )
    ->emptyParagraph()
    // rendering (only use one)
    ->asHtml()
    ->asJson()
    ->asText();
```

<!-- [docs_end] -->

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Adam Weston](https://github.com/awcodes)
- [The League of Extraordinary Packages](https://github.com/thephpleague)
- [Phiki](https://github.com/phikiphp/phiki)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
