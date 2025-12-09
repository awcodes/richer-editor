<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Extensions;

use Tiptap\Core\Node;
use Tiptap\Utils\HTML;

class Emoji extends Node
{
    public static $name = 'emoji';

    public function addOptions(): array
    {
        return [
            'HTMLAttributes' => [],
        ];
    }

    public function addAttributes(): array
    {
        return [
            'name' => [
                'default' => null,
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-emoji'),
            ],
        ];
    }

    public function parseHTML(): array
    {
        return [
            [
                'tag' => 'span[data-type="emoji"]',
            ],
        ];
    }

    public function renderHTML($node, $HTMLAttributes = []): array
    {
        return [
            'span',
            HTML::mergeAttributes($this->options['HTMLAttributes'], [
                'data-type' => 'emoji',
                'data-emoji' => $HTMLAttributes['name'] ?? '',
            ]),
            0,
        ];
    }
}
