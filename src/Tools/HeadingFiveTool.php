<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Tools;

use Filament\Forms\Components\RichEditor\RichEditorTool;
use Illuminate\Support\HtmlString;

class HeadingFiveTool extends RichEditorTool
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('richer-editor::richer-editor.tools.h5'))
            ->jsHandler('$getEditor()?.chain().focus().toggleHeading({ level: 5 }).run()')
            ->activeKey('heading')
            ->activeOptions(['level' => 5])
            ->icon(new HtmlString('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><!-- Icon from SidekickIcons by Andri Soone - https://github.com/ndri/sidekickicons/blob/master/LICENSE --><path fill="currentColor" d="M1.75 3a.75.75 0 0 1 .75.75v3.5h4v-3.5a.75.75 0 0 1 1.5 0v8.5a.75.75 0 0 1-1.5 0v-3.5h-4v3.5a.75.75 0 0 1-1.5 0v-8.5A.75.75 0 0 1 1.75 3m9.408 3a.75.75 0 0 0-.75.705l-.156 2.68a.75.75 0 0 0 .748.795h1.578c.425 0 .538.092.58.136s.092.143.092.383c0 .282-.024.543-.066.647c-.041.1.007.1-.235.129a8.4 8.4 0 0 1-1.85-.063a.75.75 0 0 0-.843.645a.75.75 0 0 0 .644.841c.76.102 1.409.128 2.182.073l.035-.002c.699-.082 1.255-.566 1.455-1.055c.2-.49.178-.933.178-1.215c0-.464-.11-1-.502-1.414s-.99-.605-1.67-.605h-.783l.07-1.18H14a.75.75 0 0 0 .75-.75A.75.75 0 0 0 14 6Z"/></svg>'))
            ->iconAlias('forms:components.rich-editor.toolbar.h5');
    }

    public static function make(?string $name = 'h5'): static
    {
        $static = app(static::class, ['name' => $name]);

        $static->configure();

        return $static;
    }
}
