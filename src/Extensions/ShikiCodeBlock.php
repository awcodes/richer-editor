<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Extensions;

use Awcodes\RicherEditor\Support\Phiki\PrismDefenseTransformer;
use Phiki\Phiki;
use Phiki\Theme\Theme;
use Tiptap\Nodes\CodeBlock;

/**
 * Renders `codeBlock` nodes with Phiki syntax highlighting when content is
 * rendered outside the editor, mirroring the themes used by the in-editor
 * Shiki extension. The `theme` option accepts a Phiki Theme enum case, a
 * bundled theme name, or a ['light' => ..., 'dark' => ...] pair.
 */
class ShikiCodeBlock extends CodeBlock
{
    public static $name = 'codeBlock';

    public function addOptions(): array
    {
        return array_merge(parent::addOptions(), [
            'theme' => [
                'light' => Theme::GithubLight,
                'dark' => Theme::GithubDark,
            ],
        ]);
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
