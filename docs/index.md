---
title: Richer Editor
description: Extensions and tools that add embeds, emoji, source editing, slash commands and syntax highlighting to Filament's Rich Editor.
---

# Richer Editor

Richer Editor is a collection of plugins, tools and helpers for Filament's `RichEditor` field. Each piece is opt-in — you register only the plugins you want, and add their toolbar buttons yourself.

It covers three areas:

- **In the editor** — extra plugins (embeds, emoji, slash menu, full screen, source editing, syntax-highlighted code blocks), nested toolbar dropdowns, and tools for authoring.
- **When rendering** — macros on Filament's `RichContentRenderer` for linked headings, Markdown output and server-side code highlighting, plus a table-of-contents builder.
- **While developing** — a rich content faker for seeders and tests, and a debug tool for inspecting editor state.

## Compatibility

| Package version | Filament version |
|-----------------|------------------|
| 1.x             | 4.x              |
| 2.x             | 4.x & 5.x        |

Richer Editor requires PHP 8.2 or later, the `dom` extension, and `filament/forms`. It also pulls in `phiki/phiki` for server-side syntax highlighting and `league/html-to-markdown` for Markdown output.

## Where to go next

- [Installation](installation.md) — install the package and import its CSS.
- [Plugins](editor/plugins.md) — what each plugin adds and how to register it.
- [Tools](editor/tools.md) — toolbar dropdowns, editor height and prebuilt tools.
- [Code blocks](editor/code-blocks.md) — syntax highlighting in the editor and in rendered output.
- [Rendering](rendering.md) — linked headings, Markdown, and a table of contents.
- [Rich content faker](faker.md) — generate realistic content for seeders and tests.
