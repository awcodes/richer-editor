<?php

declare(strict_types=1);

use Awcodes\RicherEditor\Plugins\SourceCodePlugin;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

it('can be instantiated', function () {
    $plugin = SourceCodePlugin::make();
    expect($plugin)->toBeInstanceOf(SourceCodePlugin::class);
});

it('has no php extensions', function () {
    $plugin = SourceCodePlugin::make();
    expect($plugin->getTipTapPhpExtensions())->toBeEmpty();
});

it('has no js extensions', function () {
    $plugin = SourceCodePlugin::make();
    expect($plugin->getTipTapJsExtensions())->toBeEmpty();
});

it('has editor tools', function () {
    $plugin = SourceCodePlugin::make();
    $tools = $plugin->getEditorTools();

    expect($tools)
        ->toBeArray()
        ->toHaveCount(1)
        ->and($tools[0])->toBeInstanceOf(RichEditorTool::class)
        ->and($tools[0]->getName())->toBe('sourceCode')
        ->and($tools[0]->getIcon())->toBe(Heroicon::OutlinedCodeBracketSquare)
        ->and($tools[0]->getLabel())->toBe(__('richer-editor::richer-editor.source_code.label'));
});

it('has editor actions', function () {
    $plugin = SourceCodePlugin::make();
    $actions = $plugin->getEditorActions();

    expect($actions)
        ->toBeArray()
        ->toHaveCount(1)
        ->and($actions[0])->toBeInstanceOf(Action::class)
        ->and($actions[0]->getName())->toBe('sourceCode')
        ->and($actions[0]->getModalHeading())->toBe(__('richer-editor::richer-editor.source_code.label'));
});

it('can set modal width', function () {
    $plugin = SourceCodePlugin::make();
    expect($plugin->getModalWidth())->toBe(Width::FiveExtraLarge);
    $plugin->width(Width::Large);
    expect($plugin->getModalWidth())->toBe(Width::Large);
});

/**
 * The fill form callback is wrapped by Filament, so dig the original closure
 * back out of the action's mountUsing property to exercise it directly.
 */
function sourceCodeFillForm(): Closure
{
    $action = SourceCodePlugin::make()->getEditorActions()[0];

    $reflection = new ReflectionClass($action);

    if (! $reflection->hasProperty('mountUsing')) {
        throw new Exception('mountUsing property not found on Action.');
    }

    $property = $reflection->getProperty('mountUsing');
    $property->setAccessible(true);

    $staticVariables = (new ReflectionFunction($property->getValue($action)))->getStaticVariables();

    foreach ($staticVariables as $variable) {
        if ($variable instanceof Closure) {
            return $variable;
        }
    }

    throw new Exception('Could not find original closure in mountUsing static variables.');
}

it('formats UTF-8 and non UTF-8 HTML source code in fill form', function () {
    $fillForm = sourceCodeFillForm();

    // Test with empty source
    $result = $fillForm(['source' => null]);
    expect($result)->toBe(['source' => '<p></p>']);

    // Test with UTF-8 HTML source
    $utf8html = '<div><p>á ñ ! € å ç ä œ</p></div>';
    $result = $fillForm(['source' => $utf8html]);

    expect($result['source'])->toContain('<p>á ñ ! € å ç ä œ</p>');

    // Test with non UTF-8 HTML source
    $utf8html = '<div><p>Hello World</p></div>';
    $result = $fillForm(['source' => $utf8html]);

    expect($result['source'])->toContain('<p>Hello World</p>');

    // Test with HTML5 elements that are unknown to libxml's HTML4 DTD
    $html5 = '<p>This is <mark>highlighted</mark> text with a <time>timestamp</time>.</p>';
    $result = $fillForm(['source' => $html5]);

    expect($result['source'])
        ->toContain('<mark>highlighted</mark>')
        ->toContain('<time>timestamp</time>');
});

it('keeps multiple custom blocks as siblings', function () {
    $fillForm = sourceCodeFillForm();

    $result = $fillForm(['source' => '<div data-type="customBlock" data-config="{&quot;language&quot;:&quot;cpp&quot;,&quot;code&quot;:&quot;111&quot;}" data-id="highlighted_code"></div>'
        .'<div data-type="customBlock" data-config="{&quot;language&quot;:&quot;css&quot;,&quot;code&quot;:&quot;222&quot;}" data-id="highlighted_code"></div>']);

    expect($result['source'])
        ->toContain('&quot;code&quot;:&quot;111&quot;')
        ->toContain('&quot;code&quot;:&quot;222&quot;')
        ->and(mb_substr_count($result['source'], '<div data-type="customBlock"'))->toBe(2)
        ->and(mb_substr_count($result['source'], '</div>'))->toBe(2);
});

it('does not self close empty elements', function (string $source, string $expected) {
    $result = sourceCodeFillForm()(['source' => $source]);

    expect($result['source'])
        ->toContain($expected)
        ->toContain('<p>after</p>');
})->with([
    'custom block' => [
        '<div data-type="customBlock" data-id="highlighted_code"></div><p>after</p>',
        '<div data-type="customBlock" data-id="highlighted_code"></div>',
    ],
    'embed' => [
        '<div><iframe src="https://example.com/embed"></iframe></div><p>after</p>',
        '<iframe src="https://example.com/embed"></iframe>',
    ],
    'video' => [
        '<div data-native-video="true"><video src="https://example.com/video.mp4"></video></div><p>after</p>',
        '<video src="https://example.com/video.mp4"></video>',
    ],
    'empty paragraph' => [
        '<p></p><p>after</p>',
        '<p></p>',
    ],
]);

it('renders void elements without a closing slash', function () {
    $result = sourceCodeFillForm()(['source' => '<p>before<br></p><img src="image.png" alt="Image"><p>after</p>']);

    expect($result['source'])
        ->toContain('<br>')
        ->toContain('<img src="image.png" alt="Image">')
        ->not->toContain('<br/>')
        ->not->toContain('<br />');
});
