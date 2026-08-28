---
title: Plugins
description: Register Richer Editor's plugins on a RichEditor field and add their toolbar buttons.
---

# Plugins

Plugins are registered per field with `plugins()`. Most contribute a toolbar button, which you then have to place yourself with `toolbarButtons()` — registering a plugin alone does not add it to the toolbar.

```php
use Awcodes\RicherEditor\Plugins\CodeBlockShikiPlugin;
use Awcodes\RicherEditor\Plugins\DebugPlugin;
use Awcodes\RicherEditor\Plugins\EmbedPlugin;
use Awcodes\RicherEditor\Plugins\EmojiPlugin;
use Awcodes\RicherEditor\Plugins\FakerPlugin;
use Awcodes\RicherEditor\Plugins\FullScreenPlugin;
use Awcodes\RicherEditor\Plugins\IdPlugin;
use Awcodes\RicherEditor\Plugins\LinkPlugin;
use Awcodes\RicherEditor\Plugins\SourceCodePlugin;

RichEditor::make('content')
    ->plugins([
        DebugPlugin::make(),
        EmbedPlugin::make(),
        EmojiPlugin::make(),
        FullScreenPlugin::make(),
        IdPlugin::make(),
        LinkPlugin::make(),
        SourceCodePlugin::make(),
        FakerPlugin::make(),
        CodeBlockShikiPlugin::make(),
    ])
    ->toolbarButtons([
        ['embed', 'sourceCode', 'fullscreen', 'debug', 'fakeHeading', 'fakeParagraphs', 'fakeBulletList', 'fakeNumberedList', 'codeBlock'],
    ]);
```

## What each plugin adds

| Plugin | Toolbar button | Notes |
|---|---|---|
| `EmbedPlugin` | `embed` | Insert embedded media. |
| `LinkPlugin` | `link` | Replaces the link dialog with one that also sets `id`, `target`, `hreflang`, `rel` and `referrerpolicy`. |
| `IdPlugin` | — | Registers an `id` attribute on headings and links. Needed for the id set in the link dialog to survive, and for anchor links generally. |
| `EmojiPlugin` | — | Tiptap's emoji extension with emoticons enabled, so `:)` and friends become emoji as you type. |
| `FullScreenPlugin` | `fullscreen` | Expands the editor to fill the viewport. |
| `SourceCodePlugin` | `sourceCode` | Opens a modal for editing the underlying HTML. |
| `CodeBlockShikiPlugin` | `codeBlock` | Syntax-highlighted code blocks — see [Code blocks](code-blocks.md). |
| `SlashMenuPlugin` | — | A `/` command menu for inserting nodes. |
| `DebugPlugin` | `debug` | Inspects editor state. **Local environment only.** |
| `FakerPlugin` | `fakeHeading`, `fakeParagraphs`, `fakeBulletList`, `fakeNumberedList` | Inserts placeholder content while authoring. **Local environment only.** |

`DebugPlugin` and `FakerPlugin` both check `app()->isLocal()` and contribute no tools outside a local environment, so leaving them registered in production is harmless — their buttons simply do not appear.

> [!WARNING]
> `FigurePlugin` and `VideoPlugin` are experimental and should not be used yet. Both carry `@experimental` docblocks explaining what is unfinished.

## Slash menu

`SlashMenuPlugin` adds a `/` menu inside the editor. Its trigger is a hidden-label tool, so there is no toolbar button to place.

```php
use Awcodes\RicherEditor\Plugins\SlashMenuPlugin;

RichEditor::make('content')
    ->plugins([
        SlashMenuPlugin::make()
            ->items([...])
            ->noResultsMessage('Nothing matches that'),
    ]);
```

Both `items()` and `noResultsMessage()` accept a closure as well as a plain value.

## Source code editing

`SourceCodePlugin` opens the field's HTML in a modal. Two options adjust it:

```php
use Awcodes\RicherEditor\Plugins\SourceCodePlugin;
use Filament\Support\Enums\Width;

RichEditor::make('content')
    ->plugins([
        SourceCodePlugin::make()
            ->width(Width::SevenExtraLarge)
            ->encoding('UTF-8'),
    ]);
```

`width()` takes a `Width` enum case and sets the modal's width, defaulting to `Width::FiveExtraLarge`. `encoding()` sets the character encoding used when parsing the HTML, defaults to `UTF-8`, and accepts a closure.
