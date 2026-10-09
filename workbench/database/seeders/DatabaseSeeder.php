<?php

declare(strict_types=1);

namespace Workbench\Database\Seeders;

use Awcodes\RicherEditor\Blocks\HighlightedCodeBlock;
use Awcodes\RicherEditor\Plugins\CodeBlockShikiPlugin;
use Awcodes\RicherEditor\Plugins\EmbedPlugin;
use Awcodes\RicherEditor\Plugins\IdPlugin;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Workbench\App\Models\Post;
use Workbench\Database\Factories\UserFactory;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        UserFactory::new()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Fixed content at a fixed date, so the documentation screenshots are the same on every build.
        // The factory stays random for the tests.
        $post = new Post([
            'title' => 'Developing with Richer Editor',
            'content' => RichContentRenderer::make($this->content())
                ->plugins([
                    CodeBlockShikiPlugin::make(),
                    EmbedPlugin::make(),
                    IdPlugin::make(),
                ])
                ->customBlocks([HighlightedCodeBlock::class])
                ->toArray(),
        ]);

        $post->created_at = $post->updated_at = Carbon::parse('2026-01-01 09:00:00');
        $post->save();
    }

    private function content(): string
    {
        return <<<'HTML'
            <h2>Getting started</h2>
            <p>Richer Editor adds <strong>plugins</strong>, <em>tools</em> and rendering helpers to Filament's Rich Editor. Register only the pieces you need, then <a href="https://filamentphp.com/docs">place their buttons</a> in the toolbar.</p>
            <h3>Highlighted code</h3>
            <p>Code blocks are highlighted as you type, in the theme that matches the panel.</p>
            <pre><code class="language-php">RichEditor::make('content')
                ->plugins([
                    CodeBlockShikiPlugin::make(),
                    SlashMenuPlugin::make(),
                ]);</code></pre>
            <h3>Embedded media</h3>
            <p>Paste a video link into the embed dialog to drop a responsive player into the content.</p>
            <div class="embed"><iframe class="responsive" src="https://www.youtube.com/embed/aqz-KE-bpKQ" width="16" height="9" allow="autoplay; fullscreen; picture-in-picture"></iframe></div>
            <h2>Rendering</h2>
            <blockquote><p>Linked headings, Markdown and a table of contents come from the same stored content.</p></blockquote>
            HTML;
    }
}
