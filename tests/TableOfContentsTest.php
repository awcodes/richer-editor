<?php

declare(strict_types=1);

use Awcodes\RicherEditor\Support\TableOfContents;

it('extracts headings with their level, id and text', function () {
    $content = [
        'type' => 'doc',
        'content' => [
            ['type' => 'heading', 'attrs' => ['level' => 2], 'content' => [['type' => 'text', 'text' => 'Introduction']]],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Some text.']]],
            ['type' => 'heading', 'attrs' => ['level' => 3], 'content' => [['type' => 'text', 'text' => 'Sub Section']]],
        ],
    ];

    expect(TableOfContents::make($content)->getHeadings())->toBe([
        ['level' => 2, 'id' => 'introduction', 'text' => 'Introduction'],
        ['level' => 3, 'id' => 'sub-section', 'text' => 'Sub Section'],
    ]);
});

it('skips empty headings instead of failing', function () {
    $content = [
        'type' => 'doc',
        'content' => [
            ['type' => 'heading', 'attrs' => ['level' => 2], 'content' => [['type' => 'text', 'text' => 'Design']]],
            ['type' => 'heading', 'attrs' => ['level' => 2]],
            ['type' => 'heading', 'attrs' => ['level' => 3], 'content' => []],
            ['type' => 'heading', 'attrs' => ['level' => 2], 'content' => [['type' => 'text', 'text' => '   ']]],
            ['type' => 'heading', 'attrs' => ['level' => 2], 'content' => [['type' => 'text', 'text' => 'Pricing']]],
        ],
    ];

    $toc = TableOfContents::make($content);

    expect($toc->getHeadings())->toBe([
        ['level' => 2, 'id' => 'design', 'text' => 'Design'],
        ['level' => 2, 'id' => 'pricing', 'text' => 'Pricing'],
    ])
        ->and($toc->asHtml())->toBe('<ul><li><a href="#design">Design</a></li><li><a href="#pricing">Pricing</a></li></ul>');
});
