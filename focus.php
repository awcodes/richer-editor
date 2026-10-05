<?php

declare(strict_types=1);

use Awcodes\Focus\Card;
use Awcodes\Focus\Enums\Size;
use Awcodes\Focus\Screenshot;
use Awcodes\Focus\ScreenshotSuite;
use Playwright\Page\PageInterface;

/*
 * Documentation screenshots for Richer Editor, generated with awcodes/focus from the Workbench (run `composer build`
 * first). The Workbench seeds one fixed post. Every flow closes without saving, so the post keeps its content.
 */

$editor = '[data-focus="content-editor"]';

// The fields around the editor are hidden rather than removed, so the layout does not move.
$neighbours = ['[data-focus="title-field"]', '.fi-sc-actions:has([type="submit"])'];

// The embed is a cross-origin player iframe. It is answered with a neutral poster page, so no real player loads.
$embedPoster = __DIR__ . '/workbench/fixtures/embed/aqz-KE-bpKQ.html';

// Selects the link text in the first paragraph, as an author would before opening the link dialog.
$selectLink = function (PageInterface $page): void {
    $page->evaluate(<<<'JS'
        () => {
            const editor = document.querySelector('[data-focus="content-editor"] .tiptap').editor;
            let range = null;
            editor.state.doc.descendants((node, pos) => {
                if (range === null && node.isText && node.text === 'place their buttons') {
                    range = { from: pos, to: pos + node.nodeSize };
                }
            });
            editor.chain().focus().setTextSelection(range).run();
        }
        JS);
};

// Opens an empty paragraph after the first one, so the slash menu opens near the top of the editor.
$newParagraph = function (PageInterface $page): void {
    $page->evaluate(<<<'JS'
        () => {
            const editor = document.querySelector('[data-focus="content-editor"] .tiptap').editor;
            const paragraph = editor.state.doc.child(0).nodeSize + editor.state.doc.child(1).nodeSize;
            editor.chain().focus().setTextSelection(paragraph - 1).splitBlock().run();
        }
        JS);
};

// The awcodes card templates frame each screenshot at 1400x816.
$card = [1400, 816];

// composer.json's description runs to four lines on the cards and meets the light screenshot, so the cards carry a
// shorter form of it.
$cardDescription = "Embeds, slash commands, source editing and syntax highlighting for Filament's Rich Editor.";

return ScreenshotSuite::make()
    ->fixture('https://www.youtube.com/embed/**', $embedPoster)
    ->screenshots([
        Screenshot::make('editor')
            ->visit('/admin/posts/1/edit')
            ->hide(...$neighbours)
            ->focus($editor),

        Screenshot::make('tool-group')
            ->visit('/admin/posts/1/edit')
            ->click("{$editor} [data-focus-action=\"developer-tools\"]")
            ->waitFor("{$editor} [x-ref=\"panel\"]:visible")
            ->keepInteractionState()
            ->hide(...$neighbours)
            ->focus($editor),

        Screenshot::make('slash-menu')
            ->visit('/admin/posts/1/edit')
            ->ready($newParagraph)
            ->press('/')
            ->waitFor("{$editor} .fi-dropdown-panel:not([x-ref])")
            ->keepInteractionState()
            ->hide(...$neighbours)
            ->focus($editor),

        Screenshot::make('link-dialog')
            ->visit('/admin/posts/1/edit')
            ->ready($selectLink)
            ->click("{$editor} button[aria-label=\"Link\"]")
            ->waitFor('.fi-modal-window:visible')
            ->focus('.fi-modal-window:visible'),

        Screenshot::make('source-code')
            ->visit('/admin/posts/1/edit')
            ->click("{$editor} [data-focus-action=\"developer-tools\"]")
            ->click("{$editor} [x-ref=\"panel\"]:visible button[x-on\\:click*=\"'sourceCode'\"]")
            ->waitFor('.fi-modal-window:visible')
            ->focus('.fi-modal-window:visible'),

        Screenshot::make('embed-dialog')
            ->visit('/admin/posts/1/edit')
            ->click("{$editor} button[aria-label=\"Embed\"]")
            ->waitFor('.fi-modal-window:visible')
            ->fill('.fi-modal-window:visible input[wire\\:model\\.live$=".src"]', 'https://www.youtube.com/watch?v=aqz-KE-bpKQ')
            ->waitFor('.fi-modal-window:visible input[type="checkbox"][value="controls"]')
            ->focus('.fi-modal-window:visible'),

        Screenshot::make('code-block')
            ->visit('/admin/posts/1/edit')
            ->focus("{$editor} .richer-shiki-code-block")
            // The paragraph above sits just outside this padding, so no line of it is cut in half.
            ->padding(16),

        // The rendered post is taller than the default viewport. The taller viewport keeps all of it below the
        // sticky top bar.
        Screenshot::make('rendered')
            ->viewportSize(1440, 1400)
            ->visit('/admin/posts/1')
            ->within('[data-focus="rendered-content"] .embed iframe', fn (Screenshot $screenshot): Screenshot => $screenshot
                ->waitFor('[data-focus="poster"]'))
            ->focus('[data-focus="rendered-post"]'),

        // The share-image source, shaped to the card templates' screenshot slots. The two-up templates show it dark
        // in slot 1 and light in slot 2, so it is captured in both themes.
        Screenshot::make('card-editor')
            ->viewportSize(...$card)
            ->visit('/admin/posts/1/edit')
            ->viewport(),
    ])
    ->cardTemplates('https://github.com/awcodes/focus-templates/tree/v2.0.0/dist')
    ->cards([
        // Open Graph and the GitHub social preview share one 2400x1260 template; GitHub crops 30px top and bottom.
        Card::make('social')
            ->template('two-up-wide')
            ->title('Richer Editor')
            ->description($cardDescription)
            ->screenshots(['card-editor', 'card-editor'])
            ->sizes([Size::OpenGraph, Size::GitHubSocial]),

        // The Filament plugin directory's 2560x1440 thumbnail.
        Card::make('thumbnail')
            ->template('two-up')
            ->title('Richer Editor')
            ->description($cardDescription)
            ->screenshots(['card-editor', 'card-editor'])
            ->sizes([Size::Filament]),
    ]);
