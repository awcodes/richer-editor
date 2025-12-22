<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Support;

use Awcodes\RicherEditor\Concerns\CanInsertElements;
use Faker\Factory;
use Faker\Generator;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Support\Str;

class RichContentFaker
{
    use CanInsertElements;

    protected Generator $faker;

    protected string $output = '';

    protected ?RichContentRenderer $renderer = null;

    final public function __construct()
    {
        // Prevent direct instantiation
        // Use the static make() method instead
    }

    public static function make(): static
    {
        $static = new static;
        $static->faker = Factory::create();

        return $static;
    }

    public function renderUsing(RichContentRenderer $renderer): static
    {
        $this->renderer = $renderer;

        return $this;
    }

    public function getRenderer(): RichContentRenderer
    {
        return $this->renderer ?? RichContentRenderer::make();
    }

    public function asHtml(): string
    {
        return $this->output;
    }

    /**
     * @return array<string, mixed>
     */
    public function asJson(): array
    {
        return $this->getRenderer()
            ->content(content: $this->output)
            ->toArray();
    }

    public function asText(): string
    {
        return $this->getRenderer()
            ->content(content: $this->output)
            ->toText();
    }

    public function heading(int|string|null $level = 2): static
    {
        $this->output .= '<h'.(int) $level.'>'.Str::title(value: $this->faker->words(nb: mt_rand(3, 8), asText: true)).'</h'.(int) $level.'>';

        return $this;
    }

    public function emptyParagraph(): static
    {
        $this->output .= '<p></p>';

        return $this;
    }

    public function paragraphs(int $count = 1, bool $links = false, bool $lead = false, bool $small = false, bool $code = false): static
    {
        $this->output .= $this->generateParagraphs(count: $count, links: $links, lead: $lead, small: $small, code: $code);

        return $this;
    }

    public function link(): static
    {
        $this->output .= $this->generateLink();

        return $this;
    }

    public function lead(int $paragraphs = 1, bool $links = false): static
    {
        $this->output .= $this->generateParagraphs(count: $paragraphs, links: $links, lead: true);

        return $this;
    }

    public function small(int $paragraphs = 1, bool $links = false): static
    {
        $this->output .= $this->generateParagraphs(count: $paragraphs, links: $links, small: true);

        return $this;
    }

    public function list(int $count = 3, bool $links = false, bool $ordered = false): static
    {
        $type = $ordered ? 'ol' : 'ul';

        $items = collect(value: range(start: 0, end: $count))
            ->map(callback: function () use ($links) {
                return $this->wrapWithElement(element: 'li', content: $this->generateParagraphs(links: $links));
            })
            ->implode(value: '');

        $this->output .= '<'.$type.'>'.$items.'</'.$type.'>';

        return $this;
    }

    public function image(?string $source = null, ?int $width = 1280, ?int $height = 720): static
    {
        if (in_array(needle: $source, haystack: [null, '', '0'], strict: true)) {
            $source = 'https://picsum.photos/'.$width.'/'.$height;
        }

        $this->output .= '<img src="'.$source.'" alt="'.$this->faker->sentence.'" />';

        return $this;
    }

    public function details(bool $open = false): static
    {
        $content = $this->generateParagraphs(count: mt_rand(1, 3), links: true);

        $this->output .= '<details'.($open ? ' open' : null).'><summary>'.$this->faker->sentence().'</summary><div data-type="detailsContent">'.$content.'</div></details>';

        return $this;
    }

    public function codeBlock(string $language = 'sh', string $prefix = 'language-'): static
    {
        $this->output .= "<pre><code class=\"{$prefix}{$language}\">export default function testComponent({\n\tstate,\n}) {\n\treturn {\n\t\tstate,\n\t\tinit: function () {\n\t\t\t// Initialize the Alpine component here, if you need to.\n\t\t},\n\t}\n}</code></pre>";

        return $this;
    }

    public function blockquote(): static
    {
        $this->output .= '<blockquote><p>'.$this->faker->paragraph().'</p>'.'</blockquote>';

        return $this;
    }

    public function hr(): static
    {
        $this->output .= '<hr>';

        return $this;
    }

    public function br(): static
    {
        $this->output .= '<br>';

        return $this;
    }

    public function table(?int $cols = null): static
    {
        $cols ??= mt_rand(3, 8);

        $this->output .= '<table><thead><tr><th>'.collect($this->faker->words($cols))->implode('</th><th>').'</th></tr></thead><tbody><tr><td>'.collect($this->faker->words($cols))->implode('</td><td>').'</td></tr><tr><td>'.collect($this->faker->words($cols))->implode('</td><td>').'</td></tr></tbody></table>';

        return $this;
    }

    public function customBlock(string $id, ?array $config = null): static
    {
        $this->output .= '<div data-type="customBlock" data-config="'.htmlspecialchars(json_encode($config), ENT_QUOTES, 'UTF-8').'" data-id="'.$id.'"></div>';

        return $this;
    }

    /**
     * @param  array<int>  $cols
     */
    public function grid(array $cols = [1, 1, 1], string $breakpoint = 'md'): static
    {
        $this->output .= '<div class="grid-layout" data-cols="'.count(value: $cols).'" data-from-breakpoint="'.$breakpoint.'" style="grid-template-columns: repeat('.count(value: $cols).', 1fr);">';

        foreach ($cols as $col) {
            $this->output .= '<div class="grid-layout-col" data-col-span="'.$col.'" style="grid-column: span '.$col.';"><h2>'.Str::title(value: $this->faker->words(nb: mt_rand(3, 8), asText: true)).'</h2><p>'.$this->faker->paragraph().'</p></div>';
        }

        $this->output .= '</div>';

        return $this;
    }

    private function generateLink(): string
    {
        return '<a href="'.$this->faker->url().'">'.$this->faker->words(mt_rand(3, 8), true).'</a>';
    }

    private function generateParagraphs(int $count = 1, bool $links = false, bool $lead = false, bool $small = false, bool $code = false): string
    {
        $content = $this->faker->paragraphs($count);

        if ($links) {
            $content = collect($content)->map(function ($paragraph): string {
                return $this->insertRandomAnchorTags($paragraph, 0.1);
            });
        }

        if ($code) {
            $content = collect($content)->map(function ($paragraph): string {
                return $this->insertRandomCodeTags($paragraph, 0.1);
            });
        }

        return collect($content)
            ->map(function ($text) use ($lead, $small) {
                if ($small) {
                    $text = $this->wrapWithElement('small', $text);
                }

                if ($lead) {
                    return $this->wrapWithElement('div', $this->wrapWithElement('p', $text), ['class' => 'lead']);
                }

                return $this->wrapWithElement('p', $text);
            })->implode('');
    }

    private function wrapWithElement(string $element, string $content, array $attributes = []): string
    {
        $attrs = collect($attributes)->map(fn ($value, $key) => $key.'="'.$value.'"')->implode(' ');

        return '<'.$element.($attrs ? ' '.$attrs : '').'>'.$content.'</'.$element.'>';
    }
}
