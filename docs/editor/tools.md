---
title: Tools
description: Group toolbar buttons into dropdowns, set the editor's height, and use the prebuilt heading tools.
---

# Tools

## Editor height

Cap how tall the editor grows before it scrolls:

```php
RichEditor::make('content')
    ->maxHeight('400px');
```

## Nested tool groups

`ToolGroup` collapses several toolbar buttons into a single dropdown. Register the groups with `tools()`, then place them by name in `toolbarButtons()` exactly as you would a normal button.

```php
use Awcodes\RicherEditor\Tools\ToolGroup;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Support\Icons\Heroicon;

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
                RichEditorTool::make('h4'),
            ]),
        ToolGroup::make('devTools')
            ->label('Developer Tools')
            ->icon(Heroicon::Sparkles)
            ->displayAsLabel()
            ->items([
                'debug',
                'fakeHeading',
                'fakeParagraphs',
                'fakeBulletList',
                'fakeNumberedList',
            ]),
    ])
    ->toolbarButtons([
        ['headingTools', 'devTools'],
    ]);
```

`items()` accepts the names of existing tools as strings, or `RichEditorTool` instances for tools you define inline. It also accepts a closure.

`displayAsLabel()` shows the group's label as the trigger instead of just its icon. Pass `false` — or omit the call — for an icon-only trigger. It accepts a closure too.

## Prebuilt heading tools

> [!WARNING]
> `HeadingFourTool`, `HeadingFiveTool` and `HeadingSixTool` are deprecated and will be removed in a future release. Use the heading tools Filament provides instead.

```php
use Awcodes\RicherEditor\Tools\HeadingFiveTool;
use Awcodes\RicherEditor\Tools\HeadingFourTool;
use Awcodes\RicherEditor\Tools\HeadingSixTool;

RichEditor::make('content')
    ->tools([
        HeadingFourTool::make(),
        HeadingFiveTool::make(),
        HeadingSixTool::make(),
    ])
    ->toolbarButtons([
        ['h4', 'h5', 'h6'],
    ]);
```
