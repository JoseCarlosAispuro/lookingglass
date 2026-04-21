@php
    $cta = $cta ?? [
        "text" => $text,
        "icon" => $icon,
        "action" => $action,
        "link" => $link ?? '',
        "text_to_copy" => $text_to_copy ?? ''
    ];
    $textClass = $textClass ?? '';
    $fontWeight = $fontWeight ?? 'font-bold!';
    $colorInverted = $colorInverted ?? false;
@endphp

<div class="relative flex gap-4 items-center">
    <div class="flex-1 overflow-x-auto md:overflow-clip no-scrollbar">
        <div class="whitespace-nowrap">
            <p class="button-lg {{$textClass}} {{$fontWeight}}">{{ $cta['text'] }}</p>
        </div>
    </div>

    {{-- TODO: Get this complete --}}
    {{-- <div class="block md:hidden pointer-events-none absolute right-12 top-0 h-full w-8 bg-gradient-to-l from-white to-transparent"></div> --}}

    @if($cta['action'] === 'copy')
        @if($cta['text_to_copy'])
            <button class="relative group w-10 h-10 rounded-full border border-solid flex items-center justify-center transition-all ease-in-out aspect-square {{ $colorInverted ? 'border-(--app-bg-color)/20 hover:bg-(--app-bg-color)/10' : 'border-(--app-fg-color)/20 hover:bg-(--app-fg-color)/10'}}" data-icon-cta data-no-custom-cursor data-copy-this-text="{{$cta['text_to_copy']}}">
                @include('partials.material-icon', ['name' => $cta['icon'], 'class' => 'icon-opsz-24' . ($colorInverted ? ' text-(--app-bg-color)' : '')])
                <span id="tooltip-info" role="tooltip" data-cta-tooltip>
                    Copied
                </span>
            </button>
        @endif
    @else
        <a href="{{ $cta['link'] }}" class="w-10 h-10 rounded-full border border-solid flex items-center justify-center transition-all ease-in-out aspect-square {{ $colorInverted ? 'border-(--app-bg-color)/20 hover:bg-(--app-bg-color)/10' : 'border-(--app-fg-color)/20 hover:bg-(--app-fg-color)/10'}}" data-no-custom-cursor>
            @include('partials.material-icon', ['name' => $cta['icon'], 'class' => 'icon-opsz-24' . ($colorInverted ? ' text-(--app-bg-color)' : '')])
        </a>
    @endif
</div>

@unset($cta)
