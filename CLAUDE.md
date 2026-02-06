# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Richer Editor (`awcodes/richer-editor`) is a PHP/Laravel package that extends FilamentPHP v4.3+'s RichEditor component with additional TipTap-based plugins, extensions, and tools. It has both PHP (backend) and JavaScript (frontend) components.

## Commands

```bash
# PHP
composer test          # Run tests (Pest)
composer analyse       # Static analysis (PHPStan level 4)
composer lint          # Fix code style (Laravel Pint)
composer refactor      # Run Rector refactoring
composer test:lint     # Check code style (dry run)
composer test:refactor # Check refactoring (dry run)

# JavaScript
npm run dev            # Watch mode (esbuild)
npm run build          # Production build (minified)
```

Run a single test file: `./vendor/bin/pest tests/DebugTest.php`
Run a single test: `./vendor/bin/pest --filter="test name"`

## Architecture

### Plugin System

The core pattern is a **plugin-based architecture** where each feature is a self-contained plugin implementing the `RichContentPlugin` interface with four methods:

- `getTipTapPhpExtensions()` — PHP-side TipTap node/mark extensions
- `getTipTapJsExtensions()` — JavaScript asset paths (loaded on request via FilamentAsset)
- `getEditorTools()` — Toolbar buttons (`RichEditorTool` instances)
- `getEditorActions()` — Filament Actions for modals/interactions

### Source Layers

- **`src/Plugins/`** — High-level feature implementations (entry points). Each plugin coordinates a PHP extension, JS asset, toolbar tool, and optional modal action.
- **`src/Extensions/`** — Low-level TipTap PHP node/mark definitions (extend `Tiptap\Nodes\Node` or `Tiptap\Marks\Mark`). Handle HTML parsing and rendering.
- **`src/Tools/`** — Toolbar UI components extending `RichEditorTool`. `ToolGroup` provides nested dropdown menus.
- **`src/Blocks/`** — Custom content blocks extending `RichContentCustomBlock`.
- **`src/Support/`** — Utilities: `RichContentFaker` (fluent API for generating test content), `TableOfContents`, `PrismDefenseTransformer`.

### Service Provider (`RicherEditorServiceProvider`)

Uses Spatie's `PackageServiceProvider`. Key responsibilities:
- Registers JS assets as lazy-loaded (`->loadedOnRequest()`)
- Adds macros to `RichEditor` (`maxHeight`) and `RichContentRenderer` (`toMarkdown`, `phikiCodeBlocks`, `linkHeadings`)
- Binds custom `Link` extension to override TipTap's default

### JavaScript

Each `resources/js/*.js` file is a separate esbuild entry point that wraps/configures a TipTap extension. Built output goes to `resources/dist/`. Run `npm run build` before committing JS changes.

### Environment-Aware Plugins

`DebugPlugin` and `FakerPlugin` only activate when `app()->isLocal()` is true.

### Experimental Plugins

Marked with `@experimental` and should not be relied on: `CodeBlockLowlightPlugin`, `CodeBlockShikiPlugin`, `FigurePlugin`, `VideoPlugin`.

## Code Style

All PHP files must use `declare(strict_types=1)`. Key Pint rules enforced:
- Strict comparison (`===`/`!==` only)
- Global namespace imports (no leading backslashes)
- `protected_to_private` conversion where possible
- Class elements ordered: traits → constants → properties → constructor → magic → static methods → public → protected → private
- Use `mb_str_functions` over standard string functions
- `DateTimeImmutable` preferred over `DateTime`

## Branch Strategy

Main branch is `2.x`. PRs target `2.x`.
