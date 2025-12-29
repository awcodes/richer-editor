<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Extensions;

use Awcodes\RicherEditor\Support\Phiki\PrismDefenseTransformer;
use Phiki\Phiki;
use Phiki\Theme\Theme;
use Tiptap\Core\Node;

class PhikiCodeBlock extends Node
{
    public static $name = 'phikiCodeBlock';

    public static $marks = '';

    public function addOptions(): array
    {
        return [
            'languageClassPrefix' => 'language-',
            'HTMLAttributes' => [],
            'theme' => [
                'light' => Theme::GithubLight,
                'dark' => Theme::GithubDark,
            ],
        ];
    }

    public function parseHTML(): array
    {
        return [];
    }

    public function addAttributes(): array
    {
        return [];
    }

    public function renderHTML($node, $HTMLAttributes = []): array
    {
        $code = (new Phiki())->codeToHtml(
            code: $node->content[0]->text ?? '',
            grammar: $node->attrs->language ?? 'txt',
            theme: $this->options['theme'],
        )->transformer(new PrismDefenseTransformer());

        return [
            'content' => $code->toString(),
        ];
    }
}
