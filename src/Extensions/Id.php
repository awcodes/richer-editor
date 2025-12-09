<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Extensions;

use Tiptap\Core\Extension;

class Id extends Extension
{
    public static $name = 'id';

    public function addGlobalAttributes(): array
    {
        return [
            [
                'types' => [
                    'heading',
                    'link',
                ],
                'attributes' => [
                    'id' => [
                        'default' => null,
                        'parseHTML' => fn ($DOMNode) => $DOMNode->hasAttribute('id') ? $DOMNode->getAttribute('id') : null,
                        'renderHTML' => function ($attributes): ?array {
                            if (! property_exists($attributes, 'id')) {
                                return null;
                            }

                            return [
                                'id' => $attributes->id,
                            ];
                        },
                    ],
                ],
            ],
        ];
    }
}
