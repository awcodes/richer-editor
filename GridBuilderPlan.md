# Grid Builder Extension - Implementation Plan

## Overview

A custom TipTap extension and Filament v4 plugin that provides a grid layout system with table-like column management (add, delete, merge, split). Uses a separate node type from Filament's built-in grid. It is important that you do not use any Filament code that is not part of v4.

**Node names:** `gridBuilder` / `gridBuilderColumn`
**CSS classes:** `grid-builder` / `grid-builder-col`
**UI:** Bubble menu on column focus + toolbar insert button
**Insert flow:** Quick insert (2-column default), then modify via bubble menu

---

## Files to Create

### 1. `resources/js/grid-builder.js` — JS Entry Point

Single file that exports the GridBuilder extension bundle. Contains:

- **GridBuilderColumn node** — `gridBuilderColumn`
  - `content: 'block+'`, `isolating: true`
  - Attributes: `data-col-span` (default 1), `style` (renders `grid-column: span N`)
  - Parses `<div class="grid-builder-col">`

- **GridBuilder node** — `gridBuilder`
  - `group: 'block'`, `defining: true`, `isolating: true`, `content: 'gridBuilderColumn+'`
  - Attributes: `data-cols`, `data-from-breakpoint` (default `md`), `style` (renders `grid-template-columns`)
  - Parses `<div class="grid-builder">`
  - Commands:
    - `insertGridBuilder({ columns = [1, 1], fromBreakpoint = 'md' })` — Insert new grid at selection
    - `addColumnBefore()` — Find current gridBuilderColumn, insert a new span-1 column before it, update parent `data-cols`
    - `addColumnAfter()` — Same but after current column
    - `deleteColumn()` — Remove current column, update parent `data-cols`. If last column, delete entire grid
    - `mergeColumnRight()` — Increase current column's span by right neighbor's span, move right neighbor's content into current column, delete right neighbor, keep parent `data-cols` the same (total span unchanged)
    - `mergeColumnLeft()` — Same but merge with left neighbor
    - `splitColumn()` — If current column span > 1, replace it with N span-1 columns. First column gets the content, rest get empty paragraphs. Update parent `data-cols`

- **GridBuilderBubbleMenu plugin** — Custom ProseMirror plugin
  - On `update()`: check if selection is inside a `gridBuilderColumn` node
  - If yes: show floating menu above the column DOM element
  - Menu buttons: `Add Before` | `Add After` | `Delete` | `Merge ←` | `Merge →` | `Split`
  - `Merge ←` hidden when column is first child
  - `Merge →` hidden when column is last child
  - `Split` hidden when `data-col-span` is 1
  - Position via `getBoundingClientRect()` on the column DOM element
  - Menu DOM created once, appended to editor wrapper, shown/hidden as needed

### 2. `src/Extensions/GridBuilder.php` — PHP Extension (server-side rendering)

```
class GridBuilder extends Node
    $name = 'gridBuilder'
    addOptions: HTMLAttributes => ['class' => 'grid-builder']
    addAttributes: data-cols (default '2'), data-from-breakpoint (default 'md'), style (rendered from cols)
    parseHTML: tag 'div' with class 'grid-builder'
    renderHTML: ['div', mergedAttributes, 0]
```

### 3. `src/Extensions/GridBuilderColumn.php` — PHP Column Extension

```
class GridBuilderColumn extends Node
    $name = 'gridBuilderColumn'
    addOptions: HTMLAttributes => ['class' => 'grid-builder-col']
    addAttributes: data-col-span (default '1'), style (rendered from span)
    parseHTML: tag 'div' with class 'grid-builder-col'
    renderHTML: ['div', mergedAttributes, 0]
```

### 4. `src/Plugins/GridBuilderPlugin.php` — Filament Plugin

```php
class GridBuilderPlugin implements RichContentPlugin
    make(): static
    getTipTapPhpExtensions(): [app(GridBuilder::class), app(GridBuilderColumn::class)]
    getTipTapJsExtensions(): [FilamentAsset::getScriptSrc('richer-editor/grid-builder', ...)]
    getEditorTools(): [
        RichEditorTool::make('gridBuilder')
            ->label(...)
            ->icon(Heroicon::OutlinedViewColumns)
            ->jsHandler('$getEditor().chain().focus().insertGridBuilder().run()')
    ]
    getEditorActions(): [] // No modal needed - quick insert
```

---

## Files to Modify

### 5. `bin/build.js` — Add to extensions array

Add `'grid-builder'` to the `extensions` array so esbuild compiles it.

### 6. `src/RicherEditorServiceProvider.php` — Register asset

Add to `getAssets()`:
```php
Js::make(id: static::$name.'/grid-builder', path: $dist.'/grid-builder.js')->loadedOnRequest(),
```

### 7. `resources/css/index.css` — Add grid-builder editor styles

- Grid layout base styles (display: grid, gap)
- Column border/padding styles for editor context
- Bubble menu styling (position: absolute, background, buttons, z-index)
- Column hover/focus indicators

### 8. `resources/lang/en/richer-editor.php` — Add translations

Add `'grid_builder'` key with label for the toolbar tool.

---

## Implementation Order

1. **PHP Extensions** — `GridBuilder.php` and `GridBuilderColumn.php` (server-side rendering)
2. **PHP Plugin** — `GridBuilderPlugin.php` (Filament integration)
3. **JS Extension** — `grid-builder.js` (TipTap nodes + commands + bubble menu)
4. **CSS** — Editor styles in `index.css`
5. **Build config** — Add to `bin/build.js` extensions array
6. **Service provider** — Register JS asset
7. **Translations** — Add toolbar label
8. **Build & test** — `npm run build`, verify in Filament form

---

## Command Implementation Details

### `addColumnBefore()` / `addColumnAfter()`
1. Find the `gridBuilderColumn` node containing the selection using `findParentNodeOfType`
2. Create a new `gridBuilderColumn` node with `data-col-span: 1` and a default paragraph child
3. Insert it before/after the current column in the transaction
4. Update the parent `gridBuilder`'s `data-cols` attribute (increment by 1)

### `deleteColumn()`
1. Find the current `gridBuilderColumn` and its parent `gridBuilder`
2. If it's the only column, delete the entire `gridBuilder` node
3. Otherwise, delete the column and update parent's `data-cols` (decrement by the deleted column's span)

### `mergeColumnRight()` / `mergeColumnLeft()`
1. Find current column and the target neighbor
2. If no neighbor in that direction, abort
3. Collect the neighbor's content nodes
4. Increase current column's `data-col-span` by the neighbor's span
5. Append neighbor's content to current column
6. Delete the neighbor column
7. Parent `data-cols` stays the same (total span is unchanged)

### `splitColumn()`
1. Find current column, check `data-col-span > 1`
2. Get current span value N
3. Replace the column with N new columns, each with `data-col-span: 1`
4. First new column gets the original content
5. Remaining N-1 columns get empty paragraphs
6. Parent `data-cols` stays the same (total span is unchanged)

---

## Edge Cases

- **Delete last column** → Delete entire gridBuilder node
- **Merge at boundary** → Hide merge-left button on first column, merge-right on last
- **Split span-1 column** → Hide split button when span is 1
- **Nested content** → Columns accept `block+` content, so headings, lists, images etc. all work
- **Empty grid** → Should never happen since minimum is 1 column with 1 paragraph

---

## Verification

1. Run `npm run build` — verify `resources/dist/grid-builder.js` is generated
2. Add `GridBuilderPlugin::make()` to a test form's `RichEditor::make('content')->plugins([...])`
3. Add `'gridBuilder'` to `toolbarButtons`
4. Verify: clicking toolbar button inserts a 2-column grid
5. Verify: clicking inside a column shows the bubble menu
6. Verify: all 6 commands work (add before/after, delete, merge left/right, split)
7. Verify: saving and loading preserves the grid structure (PHP round-trip)
