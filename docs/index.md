---
title: Richer Editor
description: Extensions and tools that add embeds, emoji, source editing, slash commands and syntax highlighting to Filament's Rich Editor.
---

# Richer Editor

Richer Editor is a collection of plugins, tools and helpers for Filament's `RichEditor` field. Each piece is opt-in — you register only the plugins you want, and add their toolbar buttons yourself.

![A Rich Editor with Richer Editor's toolbar: formatting buttons, a Headings dropdown, lists, a highlighted PHP code block with a language picker, embed and blocks buttons, and a Developer tools dropdown](assets/editor-light.png#gh-light-mode-only)
![A Rich Editor with Richer Editor's toolbar: formatting buttons, a Headings dropdown, lists, a highlighted PHP code block with a language picker, embed and blocks buttons, and a Developer tools dropdown](assets/editor-dark.png#gh-dark-mode-only)

It covers three areas:

- **In the editor** — extra plugins (embeds, emoji, slash menu, full screen, source editing, syntax-highlighted code blocks), nested toolbar dropdowns, and tools for authoring.
- **When rendering** — macros on Filament's `RichContentRenderer` for linked headings, Markdown output and server-side code highlighting, plus a table-of-contents builder.
- **While developing** — a rich content faker for seeders and tests, and a debug tool for inspecting editor state.

## Where to go next

- [Installation](installation.md) — install the package and import its CSS.
- [Plugins](editor/plugins.md) — what each plugin adds and how to register it.
- [Tools](editor/tools.md) — toolbar dropdowns, editor height and prebuilt tools.
- [Code blocks](editor/code-blocks.md) — syntax highlighting in the editor and in rendered output.
- [Rendering](rendering.md) — linked headings, Markdown, and a table of contents.
- [Rich content faker](faker.md) — generate realistic content for seeders and tests.
