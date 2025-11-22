<?php

namespace Awcodes\RicherEditor\Tools;

use Closure;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Illuminate\Support\HtmlString;

class SlashMenu extends RichEditorTool
{
    protected string $view = 'richer-editor::components.rich-editor-slash-menu';

    protected array | Closure | null $items = null;

    public function items(array | Closure | null $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function getItems(): ?array
    {
        $items = $this->evaluate($this->items);

        return collect($items)->map(function ($item) {
            // add validation to ensure string and not instance of RichEditorTool

            if (class_exists($item)) {
                return [
                    'type' => 'customBlock',
                    'id' => $item::getId(),
                    'label' => $item::getLabel(),
                    'schemaComponent' => $this->getEditor()->getKey(),
                    'action' => 'isLoading = true; $wire.mountAction(\'customBlock\', { editorSelection, id: \'' . $item::getId() . '\', mode: \'insert\' }, { schemaComponent: \'' . $this->getEditor()->getKey() . '\' },)',
                    'icon' => $this->getEditor()->getTools()['customBlocks']->getIcon(),
                ];
            }

            $instance = $this->getEditor()->getTools()[$item] ?? null;

            return [
                'type' => 'tool',
                'id' => $instance->getName(),
                'label' => $instance->getLabel(),
                'schemaComponent' => $this->getEditor()->getKey(),
                'action' => $instance->jsHandler,
                'icon' => $instance->getIcon(),
            ];
        })->toArray();
    }
}
