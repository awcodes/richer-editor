<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Support\Phiki;

use Phiki\Phast\ClassList;
use Phiki\Phast\Element;
use Phiki\Transformers\AbstractTransformer;

/**
 * Transformer to prevent Prism.js from re-highlighting Phiki output.
 *
 * Removes 'language-*' and 'lang-*' classes that Prism uses to identify code blocks.
 * The original language is preserved in the data-language attribute.
 *
 * This addresses an issue where browser extensions (like 1Password) inject Prism.js
 * which strips server-side syntax highlighting and replaces it with class-based tokens.
 *
 * @see https://www.1password.community/discussions/developers/1password-chrome-extension-is-incorrectly-manipulating--blocks/165639
 */
class PrismDefenseTransformer extends AbstractTransformer
{
    /**
     * Modify the <pre> tag to remove Prism-triggering class names.
     */
    public function pre(Element $pre): Element
    {
        $classList = $pre->properties->get('class');

        if ($classList instanceof ClassList) {
            $newClasses = [];
            foreach ($classList->all() as $class) {
                // Remove language-* and lang-* classes that Prism targets
                if (! str_starts_with($class, 'language-') && ! str_starts_with($class, 'lang-')) {
                    $newClasses[] = $class;
                }
            }
            $pre->properties->set('class', new ClassList($newClasses));
        }

        return $pre;
    }

    /**
     * Post-process the HTML to ensure no language classes remain.
     * This is a backup in case the pre() method doesn't catch everything.
     */
    public function postprocess(string $html): string
    {
        // Remove language-* and lang-* classes from <pre> tags
        $html = preg_replace_callback(
            '/<pre\s+([^>]*?)class="([^"]*)"/',
            function ($matches): string {
                $beforeClass = $matches[1];
                $classes = $matches[2];

                // Remove language-* and lang-* classes
                $classes = preg_replace('/\b(language|lang)-\S+\s*/', '', $classes);
                $classes = mb_trim(preg_replace('/\s+/', ' ', $classes));

                return '<pre '.$beforeClass.'class="'.$classes.'"';
            },
            $html
        );

        return $html;
    }
}
