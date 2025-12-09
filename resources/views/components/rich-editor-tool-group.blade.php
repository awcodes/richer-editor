@php
    use function Filament\Support\generate_icon_html;

    $displayAsLabel = $shouldDisplayAsLabel();

    $attributes = $getExtraAttributeBag()
        ->merge([
            'tabindex' => -1,
            'type' => 'button',
            'x-tooltip' => filled($label = $getLabel()) && ! $displayAsLabel
                ? '{ content: ' . Js::from($label) . ', theme: $store.theme }'
                : null,
        ], escape: false)
        ->class(['fi-fo-rich-editor-tool']);
@endphp

<x-filament::dropdown
    class="fi-fo-rich-editor-tool"
    width="auto"
>
    <x-slot:trigger>
        <button {!! $attributes->toHtml() !!}>
            @if ($displayAsLabel)
                <div class="display-as-label">
                    <span>{{ $getLabel() }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>
            @else
                {!! generate_icon_html($getIcon(), alias: $getIconAlias())->toHtml() !!}
            @endif
        </button>
    </x-slot:trigger>

    <x-filament::dropdown.list>
    @foreach ($getItems() as $button)
        @php
            $activeJsExpression = $button->getActiveJsExpression();

            if (filled($activeJsExpression)) {
                $activeJsExpression = "editorUpdatedAt && ({$activeJsExpression})";
            } else {
                $activeJsExpression = 'editorUpdatedAt && $getEditor()?.isActive(' . Js::from($button->getActiveKey())->toHtml() . ', ' . Js::from($button->getActiveOptions()) . ')';
            }

            $attributes = $button->getExtraAttributeBag()
                ->merge([
                    'tabindex' => -1,
                    'type' => 'button',
                    'x-bind:class' => '{ \'fi-active\': ' . ($button->hasActiveStyling() ? $activeJsExpression : 'false') . ' }',
                    'x-bind:disabled' => $button->isDisabledWhenNotActive() ? '!(' . $activeJsExpression . ')' : null,
                    'x-on:click' => $button->getJsHandler(),
                ], escape: false)
                ->class(['fi-fo-rich-editor-tool']);
        @endphp

        <x-filament::dropdown.list.item
            :icon="$button->getIcon()"
            :icon-alias="$button->getIconAlias()"
            {{ $attributes }}
        >
            {{ $button->getLabel() }}
        </x-filament::dropdown.list.item>
    @endforeach
    </x-filament::dropdown.list>

</x-filament::dropdown>


