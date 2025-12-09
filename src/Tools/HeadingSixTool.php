<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Tools;

use Filament\Forms\Components\RichEditor\RichEditorTool;
use Illuminate\Support\HtmlString;

class HeadingSixTool extends RichEditorTool
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('richer-editor::richer-editor.tools.h6'))
            ->jsHandler('$getEditor()?.chain().focus().toggleHeading({ level: 6 }).run()')
            ->activeKey('heading')
            ->activeOptions(['level' => 6])
            ->icon(new HtmlString('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><!-- Icon from SidekickIcons by Andri Soone - https://github.com/ndri/sidekickicons/blob/master/LICENSE --><path fill="currentColor" d="M12.41 6.02c-.454.05-.866.197-1.215.474c-.698.554-.945 1.463-.945 2.5v1.84c0 .676.285 1.294.744 1.664c.46.37 1.026.502 1.578.502c.553 0 1.116-.142 1.543-.535c.428-.393.635-.991.635-1.631s-.207-1.238-.635-1.63c-.427-.394-.99-.536-1.543-.536c-.266 0-.52.128-.783.187c.033-.64.164-1.047.338-1.185c.195-.154.604-.257 1.42-.07a.75.75 0 0 0 .898-.563a.75.75 0 0 0-.564-.898c-.52-.12-1.017-.169-1.47-.12m.162 4.148c.305 0 .455.072.528.139c.072.067.15.176.15.527s-.078.46-.15.527c-.073.067-.223.139-.528.139c-.304 0-.525-.08-.636-.17c-.112-.09-.186-.18-.186-.496v-.283c0-.215.01-.186.11-.248s.366-.135.712-.135M1.75 3a.75.75 0 0 1 .75.75v3.5h4v-3.5a.75.75 0 0 1 1.5 0v8.5a.75.75 0 0 1-1.5 0v-3.5h-4v3.5a.75.75 0 0 1-1.5 0v-8.5A.75.75 0 0 1 1.75 3"/></svg>'))
            ->iconAlias('forms:components.rich-editor.toolbar.h6');
    }

    public static function make(?string $name = 'h6'): static
    {
        $static = app(static::class, ['name' => $name]);

        $static->configure();

        return $static;
    }
}
