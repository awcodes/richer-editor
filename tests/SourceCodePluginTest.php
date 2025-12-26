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

it('formats UTF-8 and non UTF-8 HTML source code in fill form', function () {
    $plugin = SourceCodePlugin::make();
    $action = $plugin->getEditorActions()[0];

    $reflection = new ReflectionClass($action);

    if ($reflection->hasProperty('mountUsing')) {
        $property = $reflection->getProperty('mountUsing');
        $property->setAccessible(true);
        $mountUsingClosure = $property->getValue($action);

        expect($mountUsingClosure)->toBeCallable();

        // Try to extract the original closure from static variables
        $reflectionFunction = new ReflectionFunction($mountUsingClosure);
        $staticVariables = $reflectionFunction->getStaticVariables();

        $originalClosure = null;
        foreach ($staticVariables as $variable) {
            if ($variable instanceof Closure) {
                $originalClosure = $variable;
                break;
            }
        }

        if ($originalClosure) {
            // Test with empty source
            $result = $originalClosure(['source' => null]);
            expect($result)->toBe(['source' => '<p></p>']);

            // Test with UTF-8 HTML source
            $utf8html = '<div><p>á ñ ! € å ç ä œ</p></div>';
            $result = $originalClosure(['source' => $utf8html]);

            expect($result['source'])->toContain('<p>á ñ ! € å ç ä œ</p>');
            // Test with non UTF-8 HTML source
            $utf8html = '<div><p>Hello World</p></div>';
            $result = $originalClosure(['source' => $utf8html]);

            expect($result['source'])->toContain('<p>Hello World</p>');
        } else {
            // If we can't find it, fail the test so we know
            throw new Exception('Could not find original closure in mountUsing static variables.');
        }
    } else {
        throw new Exception('mountUsing property not found on Action.');
    }
});
