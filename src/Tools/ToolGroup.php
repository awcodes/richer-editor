<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Tools;

use Closure;
use Filament\Forms\Components\RichEditor\RichEditorTool;

class ToolGroup extends RichEditorTool
{
    protected string $view = 'richer-editor::components.rich-editor-tool-group';

    protected array|Closure|null $items = null;

    protected bool|Closure|null $displayAsLabel = null;

    public function displayAsLabel(bool|Closure|null $displayAsLabel = true): static
    {
        $this->displayAsLabel = $displayAsLabel;

        return $this;
    }

    public function items(array|Closure|null $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function getItems(): ?array
    {
        $items = $this->evaluate($this->items);

        return collect($items)->map(function ($item) {
            if (is_string($item)) {
                return $this->getEditor()->getTools()[$item] ?? null;
            }

            return $item;
        })->toArray();
    }

    public function shouldDisplayAsLabel(): bool
    {
        return $this->evaluate($this->displayAsLabel) ?? false;
    }
}
