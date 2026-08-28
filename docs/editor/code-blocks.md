---
title: Code blocks
description: Syntax-highlighted code blocks in the editor with Shiki, and matching output when the content is rendered.
---

# Code blocks

Richer Editor offers two routes to highlighted code, and they solve different problems.

- **`CodeBlockShikiPlugin`** turns the editor's native `codeBlock` node into a highlighted one, and can highlight the same nodes again server-side when the content is rendered.
- **`HighlightedCodeBlock`** is a custom block — a distinct node authors insert deliberately, rendered with Phiki.

Both rely on the stylesheet from [Installation](../installation.md) for dark-mode switching.

## Highlighting in the editor

Register `CodeBlockShikiPlugin` and place its `codeBlock` button:

```php
use Awcodes\RicherEditor\Plugins\CodeBlockShikiPlugin;
use Phiki\Theme\Theme;

RichEditor::make('content')
    ->plugins([
        CodeBlockShikiPlugin::make()
            ->defaultTheme(Theme::TokyoNight)
            ->themes(light: Theme::GithubLight, dark: Theme::GithubDark),
    ]);
```

Themes accept a [Phiki `Theme`](https://phiki.dev) enum case or any bundled Shiki theme name as a string, so this is equivalent:

```php
CodeBlockShikiPlugin::make()
    ->defaultTheme('tokyo-night')
    ->themes(light: 'github-light', dark: 'github-dark');
```

When you supply `themes()`, the light theme is rendered inline and the dark theme is exposed through CSS variables, which the package's stylesheet switches under Filament's dark mode. Without `themes()`, only `defaultTheme` applies and code blocks stay a single theme in both.

Each code block carries a language dropdown so authors can change the highlighting language. By default it lists every language Shiki bundles — narrow it with `languages()`:

```php
CodeBlockShikiPlugin::make()
    ->languages(['php', 'blade', 'js', 'ts', 'css', 'html', 'json', 'bash']);
```

## Highlighting rendered output

Shiki runs in the browser, so it cannot highlight stored content rendered server-side. The plugin also ships a PHP Tiptap extension that highlights the same `codeBlock` nodes with [Phiki](https://phiki.dev). Register the plugin on the renderer with the same theme configuration so the output matches the editor:

```php
use Awcodes\RicherEditor\Plugins\CodeBlockShikiPlugin;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Phiki\Theme\Theme;

RichContentRenderer::make($content)
    ->plugins([
        CodeBlockShikiPlugin::make()
            ->themes(light: Theme::GithubLight, dark: Theme::GithubDark),
    ])
    ->toHtml();
```

The output uses the `.phiki` styles from the package's stylesheet for dark-mode switching, so nothing further is needed.

## The highlighted code block

`HighlightedCodeBlock` is a custom block rather than a plugin. Register it with `blocks()`:

```php
use Awcodes\RicherEditor\Blocks\HighlightedCodeBlock;

RichEditor::make('content')
    ->blocks([
        HighlightedCodeBlock::class,
    ]);
```

When rendering, pass its themes through `customBlocks()`. Any of [Phiki's supported themes](https://phiki.dev/multi-themes) will do:

```php
use Awcodes\RicherEditor\Blocks\HighlightedCodeBlock;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Phiki\Theme\Theme;

RichContentRenderer::make($content)
    ->customBlocks([
        HighlightedCodeBlock::class => [
            'light' => Theme::GithubLight,
            'dark' => Theme::GithubDark,
        ],
    ])
    ->toHtml();
```

## Highlighting native code blocks with Phiki

`PhikiCodeBlockPlugin` rewrites native `codeBlock` nodes to Phiki-highlighted ones at render time, via the `phikiCodeBlocks()` macro.

> [!CAUTION]
> Do not apply this globally. It does not work with Filament's rich content attributes when storing or reading content in a form context.

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

`PhikiCodeBlockPlugin::make()->theme(...)` sets the theme, accepting a `Theme` enum case, a string, or an array of light and dark themes.
