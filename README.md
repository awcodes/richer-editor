[![Latest Version on Packagist](https://img.shields.io/packagist/v/awcodes/richer-editor.svg?style=flat-square)](https://packagist.org/packages/awcodes/richer-editor)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/awcodes/richer-editor/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/awcodes/richer-editor/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/awcodes/richer-editor/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/awcodes/richer-editor/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/awcodes/richer-editor.svg?style=flat-square)](https://packagist.org/packages/awcodes/richer-editor)

# A collection of extensions and tools to enhance the Filament Rich Editor field.

## Installation

You can install the package via composer:

```bash
composer require awcodes/richer-editor
```

> [!IMPORTANT]
> If you have not set up a custom theme and are using Filament Panels follow the instructions in the [Filament Docs](https://filamentphp.com/docs/4.x/styling/overview#creating-a-custom-theme) first.

After setting up a custom theme add the plugin's css and views to your theme.css file or your app.css file if using the standalone packages.

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

RichEditor::make('content')
    ->plugins([
        DebugPlugin::make(), // only works in local environment
        EmbedPlugin::make(),
        EmojiPlugin::make(), // Doesn't have a toolbar button
        FullScreenPlugin::make(),
        IdPlugin::make(), // Doesn't have a toolbar button
        LinkPlugin::make(), // Requires IdPlugin
        SourceCodePlugin::make(),
    ])
    ->toolbarButtons([
        ['embed', 'sourceCode', 'fullscreen', 'debug'],
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
    ])
    ->toolbarButtons([
        ['headingTools'],
    ])
```

### Prebuilt Tools

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

// when rendering the content you can change the theme using any of Phiki's supported themes. See https://phiki.dev/multi-themes

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
    ->paragraphs(count: 1, withRandomLinks: false)
    ->link()
    ->lead(pargraphs: 1)
    ->small()
    ->unorderedList(count: 1)
    ->orderedList(count: 1)
    ->image(source: null, width: 1280, height: 720)
    ->details(open: false)
    ->code(className: 'language-php')
    ->codeBlock(language: 'sh', prefix: 'language-')
    ->blockquote()
    ->hr()
    ->br()
    ->table(cols: null)
    ->grid(cols: [1,1,1], breakpoint: 'md')
    ->emptyParagraph()
    // rendering (only use one)
    ->asHtml()
    ->asJson()
    ->asText();
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

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
