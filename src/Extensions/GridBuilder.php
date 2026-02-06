<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Extensions;

use Tiptap\Core\Node;
use Tiptap\Utils\HTML;

class GridBuilder extends Node
{
    public static $name = 'gridBuilder';

    public function addOptions(): array
    {
        return [
            'HTMLAttributes' => [
                'class' => 'grid-builder',
            ],
        ];
    }

    public function addAttributes(): array
    {
        return [
            'data-cols' => [
                'default' => '2',
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-cols'),
                'renderHTML' => function ($attributes): array {
                    $attributes = (array) $attributes;

                    return [
                        'data-cols' => $attributes['data-cols'],
                        'style' => "grid-template-columns: repeat({$attributes['data-cols']}, minmax(0, 1fr));",
                    ];
                },
            ],
            'data-from-breakpoint' => [
                'default' => 'md',
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-from-breakpoint'),
            ],
        ];
    }

    public function parseHTML(): array
    {
        return [
            [
                'tag' => 'div',
                'getAttrs' => fn ($DOMNode): bool => in_array('grid-builder', explode(' ', (string) $DOMNode->getAttribute('class'))),
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
