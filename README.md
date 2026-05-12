<img src="https://res.cloudinary.com/aw-codes/image/upload/c_scale,w_1200/v1772226864/thumbnails/awcodes-richer-editor.webp" alt="richer editor opengraph image" width="1200" height="auto" class="filament-hidden" style="width: 100%;" />

[![Latest Version](https://img.shields.io/github/release/awcodes/richer-editor.svg?style=flat-square)](https://github.com/awcodes/richer-editor/releases)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/awcodes/richer-editor.svg?style=flat-square)](https://packagist.org/packages/awcodes/richer-editor)
![GitHub Repo stars](https://img.shields.io/github/stars/awcodes/richer-editor?style=flat-square)

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
| 2.x             | 5.x              |

<!-- [docs_start] -->

## Installation

You can install the package via composer:

```bash
composer require awcodes/richer-editor
```

> [!IMPORTANT]
> If you have not set up a custom theme and are using Filament Panels, follow the instructions in the [Filament Docs](https://filamentphp.com/docs/4.x/styling/overview#creating-a-custom-theme) first.

After setting up a custom theme, add the plugin's CSS and views to your theme.css file or your app.css file if using the standalone packages.

```css
@import '../../../../vendor/awcodes/richer-editor/resources/css/index.css';

@source '../../../../vendor/awcodes/richer-editor/resources/views/**/*.blade.php';
```

## Editor Usage

> [!WARNING]
> The following plugins are experimental and should not be used at the moment. See their docblocks for more information.
> - CodeBlockLowlightPlugin
> - CodeBlockShikiPlugin
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
    ])
    ->toolbarButtons([
        ['embed', 'sourceCode', 'fullscreen', 'debug', 'fakeHeading', 'fakeParagraphs', 'fakeBulletList', 'fakeNumberedList'],
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

* Heading Four
* Heading Five
* Heading Six

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
use Awcodes\RicherEditor\Support\RichContentRenderer;
use Awcodes\RicherEditor\Plugins\PhikiCodeBlockPlugin;

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
    ->lead(pargraphs: 1, links: false)
    ->small(pargraphs: 1, links: false)
    ->list(count: 3, links: false, ordered: false)
    ->image(source: null, width: 1280, height: 720)
    ->details(open: false, links: false)
    ->code(className: 'language-php')
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
