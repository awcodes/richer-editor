<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Tools;

use Closure;
use Exception;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\RichEditor\RichEditorTool;

class SlashMenu extends RichEditorTool
{
    protected string $view = 'richer-editor::components.rich-editor-slash-menu';

    protected array|Closure|null $items = null;

    protected string|Closure|null $noResultsMessage = null;

    public function items(array|Closure|null $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function noResultsMessage(string $message): static
    {
        $this->noResultsMessage = $message;

        return $this;
    }

    public function getItems(): ?array
    {
        $items = $this->evaluate($this->items);

        return collect($items)->map(function (string $item): array {
            if (is_subclass_of($item, RichContentCustomBlock::class)) {
                return [
                    'type' => 'customBlock',
                    'id' => $item::getId(),
                    'label' => $item::getLabel(),
                    'schemaComponent' => $this->getEditor()->getKey(),
                    'action' => 'isLoading = true; $wire.mountAction(\'customBlock\', { editorSelection, id: \''.$item::getId().'\', mode: \'insert\' }, { schemaComponent: \''.$this->getEditor()->getKey().'\' },)',
                    'icon' => $this->getEditor()->getTools()['customBlocks']->getIcon(),
                ];
            }

            $instance = $this->getEditor()->getTools()[$item] ?? null;

            if (! $instance) {
                throw new Exception('SlashMenu item "'.$item.'" is not a valid tool or custom block.');
            }

            $action = is_string($instance->jsHandler)
                ? $instance->jsHandler
                : 'isLoading = true; $wire.mountAction(\''.$item.'\', { editorSelection, id: \''.$instance->getName().'\', mode: \'insert\' }, { schemaComponent: \''.$instance->getEditor()->getKey().'\' },)';

            return [
                'type' => 'tool',
                'id' => $instance->getName(),
                'label' => $instance->getLabel(),
                'schemaComponent' => $this->getEditor()->getKey(),
                'action' => $action,
                'icon' => $instance->getIcon(),
            ];
        })->toArray();
    }

    public function getNoResultsMessage(): ?string
    {
        return $this->evaluate($this->noResultsMessage) ?? __('richer-editor::richer-editor.tools.slash_menu.no_results');
    }
}
