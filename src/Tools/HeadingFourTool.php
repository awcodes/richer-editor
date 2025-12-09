<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Tools;

use Filament\Forms\Components\RichEditor\RichEditorTool;
use Illuminate\Support\HtmlString;

class HeadingFourTool extends RichEditorTool
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('richer-editor::richer-editor.tools.h4'))
            ->jsHandler('$getEditor()?.chain().focus().toggleHeading({ level: 4 }).run()')
            ->activeKey('heading')
            ->activeOptions(['level' => 4])
            ->icon(new HtmlString('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><!-- Icon from SidekickIcons by Andri Soone - https://github.com/ndri/sidekickicons/blob/master/LICENSE --><path fill="currentColor" d="M13.727 6.035a.75.75 0 0 0-.84.281l-3 4.252a.75.75 0 0 0 .613 1.182h2.25v.5a.75.75 0 0 0 .75.75a.75.75 0 0 0 .75-.75v-.5h.25a.75.75 0 0 0 .75-.75a.75.75 0 0 0-.75-.75h-.25v-3.5a.75.75 0 0 0-.523-.715m-.977 3.078v1.137h-.803ZM1.75 3a.75.75 0 0 1 .75.75v3.5h4v-3.5a.75.75 0 0 1 1.5 0v8.5a.75.75 0 0 1-1.5 0v-3.5h-4v3.5a.75.75 0 0 1-1.5 0v-8.5A.75.75 0 0 1 1.75 3"/></svg>'))
            ->iconAlias('forms:components.rich-editor.toolbar.h4');
    }

    public static function make(?string $name = 'h4'): static
    {
        $static = app(static::class, ['name' => $name]);

        $static->configure();

        return $static;
    }
}
