---
title: Rich content faker
description: Generate realistic rich editor content for seeders, factories and tests.
---

# Rich content faker

`RichContentFaker` builds rich editor content a block at a time, then renders it as HTML, JSON or plain text. It is intended for seeders, factories and tests, where you want content that exercises the editor's node types rather than a wall of lorem ipsum.

```php
use Awcodes\RicherEditor\Support\RichContentFaker;

$content = RichContentFaker::make()
    ->heading(level: 2)
    ->paragraphs(count: 2, links: true)
    ->list(count: 3, ordered: false)
    ->blockquote()
    ->asHtml();
```

Calls append in order, so the sequence you chain is the order the content comes out in. Finish with exactly one of `asHtml()`, `asJson()` or `asText()`.

## Blocks

| Method | Arguments |
|---|---|
| `heading()` | `level` — defaults to `2` |
| `paragraphs()` | `count`, plus the mark flags below |
| `lead()` | `paragraphs`, `links` |
| `small()` | `paragraphs`, `links` |
| `list()` | `count`, `links`, `ordered` |
| `blockquote()` | — |
| `codeBlock()` | `language` (default `sh`), `prefix` (default `language-`) |
| `image()` | `source`, `width` (default `1280`), `height` (default `720`) |
| `details()` | `open`, `links` |
| `table()` | `cols` |
| `grid()` | `cols` (default `[1, 1, 1]`), `breakpoint` (default `md`) |
| `customBlock()` | `id`, `config` |
| `link()` | — |
| `hr()`, `br()`, `emptyParagraph()` | — |

## Marks within paragraphs

`paragraphs()` takes a flag per mark, all defaulting to `false`, so you can generate text that exercises a particular one:

```php
use Awcodes\RicherEditor\Support\RichContentFaker;

RichContentFaker::make()
    ->paragraphs(
        count: 1,
        links: true,
        code: true,
        bold: true,
        italic: true,
        underline: true,
        strike: true,
        subscript: true,
        superscript: true,
        highlight: true,
        lead: false,
        small: false,
        mergeTags: ['first_name', 'last_name'],
    )
    ->asHtml();
```

`mergeTags` sprinkles merge tags from the names you pass. `lead` and `small` apply those styles to the generated paragraphs.

## Custom blocks

Generate a custom block by id, with whatever config that block expects:

```php
use Awcodes\RicherEditor\Support\RichContentFaker;

RichContentFaker::make()
    ->customBlock(
        id: 'batman',
        config: [
            'name' => 'Batman',
            'color' => 'black',
            'side' => 'hero',
        ],
    )
    ->asHtml();
```

## Output

`asHtml()` returns an HTML string, `asJson()` returns the content as an array of Tiptap nodes, and `asText()` returns plain text.

To render through a renderer you have configured yourself — with custom blocks registered, for instance — pass it to `renderUsing()` before calling `asHtml()`.

> [!TIP]
> `FakerPlugin` puts the same generators behind toolbar buttons inside the editor, for filling a field while working on a layout. It only registers its tools in a local environment — see [Plugins](editor/plugins.md).
