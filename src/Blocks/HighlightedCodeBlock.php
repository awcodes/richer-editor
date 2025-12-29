<?php

declare(strict_types=1);

namespace Awcodes\RicherEditor\Blocks;

use Awcodes\RicherEditor\Support\Phiki\PrismDefenseTransformer;
use Filament\Actions\Action;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Phiki\Grammar\Grammar;
use Phiki\Phiki;
use Phiki\Theme\Theme;

class HighlightedCodeBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'highlighted_code';
    }

    public static function getLabel(): string
    {
        return 'Highlighted Code Block';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->schema([
                Select::make('language')
                    ->live()
                    ->options(CodeEditor\Enums\Language::class),
                CodeEditor::make('code')
                    ->language(fn (Get $get): mixed => $get('language'))
                    ->wrap()
                    ->columnSpanFull(),
            ]);
    }

    public static function getPreviewLabel(array $config): string
    {
        $language = is_string($config['language']) ? $config['language'] : $config['language']->value;

        return 'Code Block ('.$language.')';
    }

    public static function toPreviewHtml(array $config): string
    {
        $code = (new Phiki)->codeToHtml(
            code: $config['code'],
            grammar: Grammar::tryFrom(is_string($config['language']) ? $config['language'] : $config['language']->value),
            theme: [
                'light' => Theme::GithubLight,
                'dark' => Theme::GithubDark,
            ]
        )->transformer(new PrismDefenseTransformer());

        return view('richer-editor::components.blocks.highlighted-code.index', [
            'code' => $code,
        ])->render();
    }

    public static function toHtml(array $config, array $data): string
    {
        $code = (new Phiki)->codeToHtml(
            code: $config['code'],
            grammar: Grammar::tryFrom(is_string($config['language']) ? $config['language'] : $config['language']->value),
            theme: $data['theme'] ?? [
                'light' => Theme::GithubLight,
                'dark' => Theme::GithubDark,
            ]
        )->transformer(new PrismDefenseTransformer());

        return view('richer-editor::components.blocks.highlighted-code.index', [
            'code' => $code,
        ])->render();
    }
}
