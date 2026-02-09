<?php

declare(strict_types=1);

use Awcodes\RicherEditor\Support\RichContentFaker;
use Filament\Forms\Components\RichEditor\RichContentRenderer;

it('can be instantiated', function () {
    $faker = RichContentFaker::make();
    expect($faker)->toBeInstanceOf(RichContentFaker::class);
});

it('returns empty string by default', function () {
    expect(RichContentFaker::make()->asHtml())->toBe('');
});

it('can set a custom renderer', function () {
    $renderer = RichContentRenderer::make();
    $faker = RichContentFaker::make()->renderUsing($renderer);

    expect($faker->getRenderer())->toBe($renderer);
});

it('returns a default renderer when none is set', function () {
    $faker = RichContentFaker::make();
    expect($faker->getRenderer())->toBeInstanceOf(RichContentRenderer::class);
});

it('can output as json array', function () {
    $result = RichContentFaker::make()->paragraphs()->asJson();
    expect($result)->toBeArray();
});

it('can output as plain text', function () {
    $result = RichContentFaker::make()->paragraphs()->asText();
    expect($result)->toBeString()->not->toBeEmpty();
});

// Heading tests

it('generates a heading with default level 2', function () {
    $html = RichContentFaker::make()->heading()->asHtml();

    expect($html)
        ->toStartWith('<h2>')
        ->toEndWith('</h2>');
});

it('generates headings at specified levels', function (int $level) {
    $html = RichContentFaker::make()->heading($level)->asHtml();

    expect($html)
        ->toStartWith("<h{$level}>")
        ->toEndWith("</h{$level}>");
})->with([1, 2, 3, 4, 5, 6]);

// Paragraph tests

it('generates an empty paragraph', function () {
    $html = RichContentFaker::make()->emptyParagraph()->asHtml();
    expect($html)->toBe('<p></p>');
});

it('generates paragraphs', function () {
    $html = RichContentFaker::make()->paragraphs()->asHtml();

    expect($html)->toContain('<p>')->toContain('</p>');
});

it('generates multiple paragraphs', function () {
    $html = RichContentFaker::make()->paragraphs(3)->asHtml();

    expect(mb_substr_count($html, '<p>'))->toBeGreaterThanOrEqual(3);
});

it('generates paragraphs with links', function () {
    $html = RichContentFaker::make()->paragraphs(links: true)->asHtml();

    expect($html)->toContain('<a href="');
});

it('generates paragraphs with bold text', function () {
    $html = RichContentFaker::make()->paragraphs(bold: true)->asHtml();

    expect($html)->toContain('<strong>')->toContain('</strong>');
});

it('generates paragraphs with italic text', function () {
    $html = RichContentFaker::make()->paragraphs(italic: true)->asHtml();

    expect($html)->toContain('<em>')->toContain('</em>');
});

it('generates paragraphs with underline text', function () {
    $html = RichContentFaker::make()->paragraphs(underline: true)->asHtml();

    expect($html)->toContain('<u>')->toContain('</u>');
});

it('generates paragraphs with strikethrough text', function () {
    $html = RichContentFaker::make()->paragraphs(strike: true)->asHtml();

    expect($html)->toContain('<s>')->toContain('</s>');
});

it('generates paragraphs with subscript text', function () {
    $html = RichContentFaker::make()->paragraphs(subscript: true)->asHtml();

    expect($html)->toContain('<sub>')->toContain('</sub>');
});

it('generates paragraphs with superscript text', function () {
    $html = RichContentFaker::make()->paragraphs(superscript: true)->asHtml();

    expect($html)->toContain('<sup>')->toContain('</sup>');
});

it('generates paragraphs with inline code', function () {
    $html = RichContentFaker::make()->paragraphs(code: true)->asHtml();

    expect($html)->toContain('<code>')->toContain('</code>');
});

it('generates paragraphs with highlight', function () {
    $html = RichContentFaker::make()->paragraphs(highlight: true)->asHtml();

    expect($html)->toContain('<mark>')->toContain('</mark>');
});

it('generates paragraphs with merge tags', function () {
    $html = RichContentFaker::make()->paragraphs(mergeTags: ['firstName', 'lastName'])->asHtml();

    expect($html)
        ->toContain('{{ firstName }}')
        ->toContain('{{ lastName }}');
});

it('generates paragraphs with lead styling', function () {
    $html = RichContentFaker::make()->paragraphs(lead: true)->asHtml();

    expect($html)->toContain('class="lead"');
});

it('generates paragraphs with small styling', function () {
    $html = RichContentFaker::make()->paragraphs(small: true)->asHtml();

    expect($html)->toContain('<small>')->toContain('</small>');
});

// Link tests

it('generates a standalone link', function () {
    $html = RichContentFaker::make()->link()->asHtml();

    expect($html)
        ->toContain('<a href="')
        ->toContain('</a>');
});

// Lead and Small shortcut tests

it('generates lead paragraphs via shortcut', function () {
    $html = RichContentFaker::make()->lead()->asHtml();

    expect($html)->toContain('class="lead"');
});

it('generates small paragraphs via shortcut', function () {
    $html = RichContentFaker::make()->small()->asHtml();

    expect($html)->toContain('<small>')->toContain('</small>');
});

// List tests

it('generates an unordered list', function () {
    $html = RichContentFaker::make()->list()->asHtml();

    expect($html)
        ->toContain('<ul>')
        ->toContain('</ul>')
        ->toContain('<li>');
});

it('generates an ordered list', function () {
    $html = RichContentFaker::make()->list(ordered: true)->asHtml();

    expect($html)
        ->toContain('<ol>')
        ->toContain('</ol>')
        ->toContain('<li>');
});

it('generates a list with the specified number of items', function () {
    $html = RichContentFaker::make()->list(5)->asHtml();

    expect(mb_substr_count($html, '<li>'))->toBe(5);
});

it('generates a list with links', function () {
    $html = RichContentFaker::make()->list(links: true)->asHtml();

    expect($html)->toContain('<a href="');
});

// Image tests

it('generates an image with default picsum source', function () {
    $html = RichContentFaker::make()->image()->asHtml();

    expect($html)
        ->toContain('<img src="https://picsum.photos/1280/720"')
        ->toContain('alt="');
});

it('generates an image with custom dimensions', function () {
    $html = RichContentFaker::make()->image(width: 800, height: 600)->asHtml();

    expect($html)->toContain('https://picsum.photos/800/600');
});

it('generates an image with a custom source', function () {
    $html = RichContentFaker::make()->image('https://example.com/photo.jpg')->asHtml();

    expect($html)->toContain('src="https://example.com/photo.jpg"');
});

it('falls back to picsum for null or empty source', function (mixed $source) {
    $html = RichContentFaker::make()->image($source)->asHtml();

    expect($html)->toContain('https://picsum.photos/');
})->with([null, '', '0']);

// Details tests

it('generates a details element', function () {
    $html = RichContentFaker::make()->details()->asHtml();

    expect($html)
        ->toContain('<details>')
        ->toContain('<summary>')
        ->toContain('data-type="detailsContent"')
        ->toContain('</details>');
});

it('generates an open details element', function () {
    $html = RichContentFaker::make()->details(open: true)->asHtml();

    expect($html)->toContain('<details open>');
});

// Code block tests

it('generates a code block with default language', function () {
    $html = RichContentFaker::make()->codeBlock()->asHtml();

    expect($html)
        ->toContain('<pre><code class="language-sh">')
        ->toContain('</code></pre>');
});

it('generates a code block with custom language', function () {
    $html = RichContentFaker::make()->codeBlock('php')->asHtml();

    expect($html)->toContain('class="language-php"');
});

it('generates a code block with custom prefix', function () {
    $html = RichContentFaker::make()->codeBlock('js', 'lang-')->asHtml();

    expect($html)->toContain('class="lang-js"');
});

// Blockquote tests

it('generates a blockquote', function () {
    $html = RichContentFaker::make()->blockquote()->asHtml();

    expect($html)
        ->toContain('<blockquote>')
        ->toContain('</blockquote>');
});

// Horizontal rule and break tests

it('generates a horizontal rule', function () {
    $html = RichContentFaker::make()->hr()->asHtml();
    expect($html)->toBe('<hr>');
});

it('generates a line break', function () {
    $html = RichContentFaker::make()->br()->asHtml();
    expect($html)->toBe('<br>');
});

// Table tests

it('generates a table', function () {
    $html = RichContentFaker::make()->table()->asHtml();

    expect($html)
        ->toContain('<table>')
        ->toContain('<thead>')
        ->toContain('<tbody>')
        ->toContain('<th>')
        ->toContain('<td>')
        ->toContain('</table>');
});

it('generates a table with specified columns', function () {
    $html = RichContentFaker::make()->table(4)->asHtml();

    expect(mb_substr_count($html, '<th>'))->toBe(4);
});

// Custom block tests

it('generates a custom block', function () {
    $html = RichContentFaker::make()->customBlock('my-block')->asHtml();

    expect($html)
        ->toContain('data-type="customBlock"')
        ->toContain('data-id="my-block"');
});

it('generates a custom block with config', function () {
    $config = ['key' => 'value'];
    $html = RichContentFaker::make()->customBlock('my-block', $config)->asHtml();

    expect($html)
        ->toContain('data-config="')
        ->toContain('data-id="my-block"');
});

// Grid tests

it('generates a grid layout', function () {
    $html = RichContentFaker::make()->grid()->asHtml();

    expect($html)
        ->toContain('class="grid-layout"')
        ->toContain('data-cols="3"')
        ->toContain('class="grid-layout-col"');
});

it('generates a grid with custom columns', function () {
    $html = RichContentFaker::make()->grid([1, 2])->asHtml();

    expect($html)
        ->toContain('data-cols="2"')
        ->and(mb_substr_count($html, 'grid-layout-col'))->toBe(2);
});

it('generates a grid with custom breakpoint', function () {
    $html = RichContentFaker::make()->grid(breakpoint: 'lg')->asHtml();

    expect($html)->toContain('data-from-breakpoint="lg"');
});

// Chaining tests

it('supports fluent method chaining', function () {
    $html = RichContentFaker::make()
        ->heading()
        ->paragraphs(2)
        ->hr()
        ->heading(3)
        ->paragraphs()
        ->asHtml();

    expect($html)
        ->toContain('<h2>')
        ->toContain('<h3>')
        ->toContain('<hr>')
        ->toContain('<p>');
});

// Macroable tests

it('supports macros', function () {
    RichContentFaker::macro('customContent', function () {
        /** @var RichContentFaker $this */
        $this->heading()->paragraphs(2);

        return $this;
    });

    $html = RichContentFaker::make()->customContent()->asHtml();

    expect($html)
        ->toContain('<h2>')
        ->toContain('<p>');
});
