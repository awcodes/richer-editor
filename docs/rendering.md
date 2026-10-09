---
title: Rendering
description: Macros and helpers for rendering stored rich content as linked headings, Markdown, or a table of contents.
---

# Rendering

Richer Editor registers three macros on Filament's `RichContentRenderer`, available anywhere you render stored content.

![A rendered post beside a table of contents built from its headings, with linked headings, a Phiki-highlighted code block and a responsive embed](assets/rendered-light.png#gh-light-mode-only)
![A rendered post beside a table of contents built from its headings, with linked headings, a Phiki-highlighted code block and a responsive embed](assets/rendered-dark.png#gh-dark-mode-only)

## Linked headings

Turn headings into anchor links:

```php
use Filament\Forms\Components\RichEditor\RichContentRenderer;

RichContentRenderer::make($content)
    ->linkHeadings(level: 3, wrap: false)
    ->toHtml();
```

`level` sets how deep to go, defaulting to `3`. `wrap` controls whether the heading's text is wrapped in the link or the link is placed alongside it; it defaults to `false`.

For headings to carry stable ids, register [`IdPlugin`](editor/plugins.md) on the field they were authored in.

## Markdown

Convert stored content to Markdown:

```php
use Filament\Forms\Components\RichEditor\RichContentRenderer;

RichContentRenderer::make($content)
    ->toMarkdown(options: []);
```

Conversion uses [HTML To Markdown for PHP](https://github.com/thephpleague/html-to-markdown); anything in `options` is passed straight to its converter, so see that project's documentation for what it accepts.

Note this returns a Markdown string rather than a renderer, so it ends the chain — there is no `toHtml()` after it.

## Table of contents

`TableOfContents` reads the headings out of stored content and builds a nested table of contents.

```php
use Awcodes\RicherEditor\Support\TableOfContents;

TableOfContents::make($content)
    ->asHtml();
```

Or take the structure and render it yourself:

```php
use Awcodes\RicherEditor\Support\TableOfContents;

$toc = TableOfContents::make($content)
    ->asArray();
```

Both accept a `maxDepth` argument controlling how many heading levels are included, defaulting to `3`:

```php
TableOfContents::make($content)->asHtml(maxDepth: 2);
```

## Embeds

`toHtml()` sanitizes its output, and Filament's sanitizer removes `<iframe>` elements. Without extra configuration, `EmbedPlugin` content renders as an empty `<div class="embed">`.

`EmbedPlugin::allowEmbedsIn()` allows the iframe the embed renders. It keeps an iframe's `src` only when the URL is `https` and on one of the embed hosts: `www.youtube.com`, `www.youtube-nocookie.com` and `player.vimeo.com`. Apply it to Filament's sanitizer configuration in a service provider's `register()` method:

```php
use Awcodes\RicherEditor\Plugins\EmbedPlugin;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

public function register(): void
{
    $this->app->extend(
        HtmlSanitizerConfig::class,
        fn (HtmlSanitizerConfig $config): HtmlSanitizerConfig => EmbedPlugin::allowEmbedsIn($config),
    );
}
```

Pass your own list of hosts as the second argument if you embed players from elsewhere:

```php
EmbedPlugin::allowEmbedsIn($config, [...EmbedPlugin::EMBED_HOSTS, 'media.example.com']);
```

The sanitizer configuration is shared by everything that sanitizes HTML through Filament, so this allows these iframes wherever Filament sanitizes, not only in rich content.

## Code blocks

Rendering highlighted code is covered separately — see [Code blocks](editor/code-blocks.md).
