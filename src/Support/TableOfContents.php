<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Support;

use Filament\Forms\Components\RichEditor\RichContentRenderer;

class TableOfContents extends RichContentRenderer
{
    public function asArray(int $maxDepth = 3): array
    {
        $editor = $this->getEditor();

        $this->processCustomBlocks($editor);
        $this->processMergeTags($editor);
        $this->processNodes($editor);

        $headings = $this->getHeadings($maxDepth);

        return $this->generateTocArray($headings);
    }

    public function asHtml(int $maxDepth = 3): string
    {
        $editor = $this->getEditor();

        $this->processCustomBlocks($editor);
        $this->processMergeTags($editor);
        $this->processNodes($editor);

        $headings = $this->getHeadings($maxDepth);

        return $this->generateTocHtml($headings, $headings[0]['level']);
    }

    public function getHeadings(int $maxDepth = 3): array
    {
        $editor = $this->getEditor();

        $this->processCustomBlocks($editor);
        $this->processMergeTags($editor);
        $this->processNodes($editor);

        $headings = [];

        $editor->descendants(function (&$node) use (&$headings, $maxDepth): void {
            if ($node->type !== 'heading') {
                return;
            }

            if ($node->attrs->level > $maxDepth) {
                return;
            }

            $text = collect($node->content)->map(fn ($node): mixed => $node->text ?? null)->implode(' ');

            if (! isset($node->attrs->id)) {
                $node->attrs->id = str($text)->slug()->toString();
            }

            $headings[] = [
                'level' => $node->attrs->level,
                'id' => $node->attrs->id,
                'text' => $text,
            ];
        });

        return $headings;
    }

    public function generateTocArray(array &$headings, int $parentLevel = 0): array
    {
        $toc = [];

        foreach ($headings as $key => &$value) {
            $currentLevel = $value['level'];
            $nextLevel = $headings[$key + 1]['level'] ?? 0;

            if ($parentLevel >= $currentLevel) {
                break;
            }

            unset($headings[$key]);

            $heading = [
                'id' => $value['id'],
                'text' => $value['text'],
                'depth' => $currentLevel,
            ];

            if ($nextLevel > $currentLevel) {
                $heading['subs'] = $this->generateTocArray($headings, $currentLevel);
                unset($headings[$key + 1]);
            }

            $toc[] = $heading;

        }

        return $toc;
    }

    public function generateTocHtml(array $headings, int $parentLevel = 0): string
    {
        $toc = '<ul>';
        $prev = $parentLevel;

        foreach ($headings as $item) {
            $prev <= $item['level'] ?: $toc .= str_repeat('</ul>', $prev - $item['level']);
            $prev >= $item['level'] ?: $toc .= '<ul>';

            $toc .= '<li><a href="#'.$item['id'].'">'.$item['text'].'</a></li>';

            $prev = $item['level'];
        }

        return $toc.'</ul>';
    }
}
