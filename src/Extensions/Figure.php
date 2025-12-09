<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Extensions;

use DOMElement;
use Tiptap\Core\Node;
use Tiptap\Utils\HTML;

class Figure extends Node
{
    public static $name = 'figure';

    public function addOptions(): array
    {
        return [
            'HTMLAttributes' => [],
        ];
    }

    public function addAttributes(): array
    {
        return [
            'src' => [
                'default' => null,
                'parseHTML' => fn ($DOMNode) => $DOMNode->firstChild->getAttribute('src'),
            ],
            'alt' => [
                'default' => null,
                'parseHTML' => fn ($DOMNode) => $DOMNode->firstChild->getAttribute('alt'),
            ],
            'title' => [
                'default' => null,
                'parseHTML' => fn ($DOMNode) => $DOMNode->firstChild->getAttribute('title'),
            ],
            'id' => [
                'parseHTML' => fn ($DOMNode) => $DOMNode->firstChild->getAttribute('data-id') ?: null,
                'renderHTML' => fn ($attributes): array => ['data-id' => $attributes->id ?? null],
            ],
        ];
    }

    public function parseHTML(): array
    {
        return [
            [
                'tag' => 'figure',
                'contentElement' => fn ($domNode): DOMElement => new DOMElement('figcaption', $domNode->lastChild->textContent),
            ],
        ];
    }

    public function renderHTML($node, $HTMLAttributes = []): array
    {
        return [
            'figure',
            $this->options['HTMLAttributes'],
            ['img', HTML::mergeAttributes($HTMLAttributes), null],
            ['figcaption', 0],
        ];
    }
}
