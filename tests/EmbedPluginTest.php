<?php

declare(strict_types=1);

use Awcodes\RicherEditor\Plugins\EmbedPlugin;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

function embedHtml(string $src, int $width = 16, int $height = 9): string
{
    return '<div class="embed"><iframe class="responsive" src="' . $src . '" width="' . $width . '" height="' . $height . '" '
        . 'allow="autoplay; fullscreen; picture-in-picture" style="aspect-ratio:' . $width . '/' . $height . '; width: 100%; height: auto;"></iframe></div>';
}

function renderEmbed(string $html): string
{
    return RichContentRenderer::make($html)
        ->plugins([EmbedPlugin::make()])
        ->toHtml();
}

it('is removed by the default sanitizer', function () {
    expect(renderEmbed(embedHtml('https://www.youtube.com/embed/N9qZFD1NkhI')))
        ->not->toContain('<iframe');
});

it('keeps embeds from the embed hosts once allowed', function (string $src) {
    app()->extend(HtmlSanitizerConfig::class, fn (HtmlSanitizerConfig $config): HtmlSanitizerConfig => EmbedPlugin::allowEmbedsIn($config));

    expect(renderEmbed(embedHtml($src)))
        ->toContain('<iframe')
        ->toContain('src="' . $src . '"')
        ->toContain('width="16"')
        ->toContain('height="9"')
        ->toContain('aspect-ratio:16/9');
})->with([
    'https://www.youtube.com/embed/N9qZFD1NkhI',
    'https://www.youtube-nocookie.com/embed/N9qZFD1NkhI',
    'https://player.vimeo.com/video/76979871',
]);

it('drops the source of an iframe from any other host', function (string $src) {
    app()->extend(HtmlSanitizerConfig::class, fn (HtmlSanitizerConfig $config): HtmlSanitizerConfig => EmbedPlugin::allowEmbedsIn($config));

    expect(renderEmbed(embedHtml($src)))
        ->not->toContain('src=');
})->with([
    'https://evil.example/embed/N9qZFD1NkhI',
    'http://www.youtube.com/embed/N9qZFD1NkhI',
    'javascript:alert(1)',
]);

it('accepts a custom list of hosts', function () {
    app()->extend(HtmlSanitizerConfig::class, fn (HtmlSanitizerConfig $config): HtmlSanitizerConfig => EmbedPlugin::allowEmbedsIn($config, ['Media.Example.com']));

    expect(renderEmbed(embedHtml('https://media.example.com/player/1')))
        ->toContain('src="https://media.example.com/player/1"')
        ->and(renderEmbed(embedHtml('https://www.youtube.com/embed/N9qZFD1NkhI')))
        ->not->toContain('src=');
});

it('keeps the stored size of a responsive embed across a round trip', function () {
    $html = RichContentRenderer::make(embedHtml('https://www.youtube.com/embed/N9qZFD1NkhI'))
        ->plugins([EmbedPlugin::make()])
        ->toUnsafeHtml();

    $roundTripped = RichContentRenderer::make($html)
        ->plugins([EmbedPlugin::make()])
        ->toUnsafeHtml();

    expect($roundTripped)
        ->toContain('width="16"')
        ->toContain('height="9"');
});
