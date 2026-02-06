<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Extensions;

use Tiptap\Core\Node;
use Tiptap\Utils\HTML;

class GridBuilderColumn extends Node
{
    public static $name = 'gridBuilderColumn';

    public function addOptions(): array
    {
        return [
            'HTMLAttributes' => [
                'class' => 'grid-builder-col',
            ],
        ];
    }

    public function addAttributes(): array
    {
        return [
            'data-col-span' => [
                'default' => '1',
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-col-span'),
                'renderHTML' => function ($attributes): array {
                    $attributes = (array) $attributes;

                    return [
                        'data-col-span' => $attributes['data-col-span'],
                        'style' => "grid-column: span {$attributes['data-col-span']};",
                    ];
                },
            ],
        ];
    }

    public function parseHTML(): array
    {
        return [
            [
                'tag' => 'div',
                'getAttrs' => fn ($DOMNode): bool => in_array('grid-builder-col', explode(' ', (string) $DOMNode->getAttribute('class'))),
            ],
        ];
    }

    public function renderHTML($node, array $HTMLAttributes = []): array
    {
        return [
            'div',
            HTML::mergeAttributes($this->options['HTMLAttributes'], $HTMLAttributes),
            0,
        ];
    }
}
