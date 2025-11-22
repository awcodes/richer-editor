[![Latest Version on Packagist](https://img.shields.io/packagist/v/awcodes/richer-editor.svg?style=flat-square)](https://packagist.org/packages/awcodes/richer-editor)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/awcodes/richer-editor/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/awcodes/richer-editor/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/awcodes/richer-editor/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/awcodes/richer-editor/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/awcodes/richer-editor.svg?style=flat-square)](https://packagist.org/packages/awcodes/richer-editor)

# A collection of extensions and tools to enhance the Filament Rich Editor field.

> [!CAUTION]
> This package is a work in progress at the moment and is not yet stable or recommended for production use.

## Installation

You can install the package via composer:

```bash
composer require awcodes/richer-editor
```

> [!IMPORTANT]
> If you have not set up a custom theme and are using Filament Panels follow the instructions in the [Filament Docs](https://filamentphp.com/docs/4.x/styling/overview#creating-a-custom-theme) first.

After setting up a custom theme add the plugin's css to your theme.css file or your app.css file if using the standalone packages.

```css
@import '../../../../vendor/awcodes/richer-editor/resources/css/index.css';
```

## Usage

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
        DebugPlugin::make(),
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

### Prebuild Tools

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
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
