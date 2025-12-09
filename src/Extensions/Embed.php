<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Extensions;

use Tiptap\Core\Node;

class Embed extends Node
{
    public static $name = 'embed';

    public function addOptions(): array
    {
        return [
            'allow' => 'autoplay; fullscreen; picture-in-picture',
            'HTMLAttributes' => [
                'class' => 'embed',
            ],
            'width' => 640,
            'height' => 480,
        ];
    }

    public function addAttributes(): array
    {
        return [
            'style' => [
                'default' => null,
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('style'),
            ],
            'src' => [
                'default' => null,
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('src'),
            ],
            'allow' => [
                'default' => $this->options['allow'],
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('allow'),
            ],
            'width' => [
                'default' => $this->options['width'],
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('width'),
            ],
            'height' => [
                'default' => $this->options['height'],
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('height'),
            ],
            'responsive' => [
                'default' => true,
                'parseHTML' => fn ($DOMNode): bool => str_contains((string) $DOMNode->getAttribute('class'), 'responsive'),
            ],
        ];
    }

    public function parseHTML(): array
    {
        return [
            [
                'tag' => 'iframe',
            ],
        ];
    }

    public function renderHTML($node, $HTMLAttributes = []): array
    {
        return [
            'div',
            $this->options['HTMLAttributes'],
            [
                'iframe',
                [
                    'src' => $node->attrs->src,
                    'width' => $node->attrs->width ?? $this->options['width'],
                    'height' => $node->attrs->height ?? $this->options['height'],
                    'allow' => $this->options['allow'],
                    'class' => $node->attrs->responsive ? 'responsive' : null,
                    'style' => $node->attrs->responsive
                        ? "aspect-ratio:{$node->attrs->width}/{$node->attrs->height}; width: 100%; height: auto;"
                        : null,
                ],
            ],
        ];
    }
}
