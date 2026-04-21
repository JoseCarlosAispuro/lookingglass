@php
    $isSmall = $isSmall ?? false;
    $link_size = $link_size ?? ($isSmall ? 'sm' : 'lg');

    $link_class = match ($link_size) {
        'sm' => 'button-sm',
        'md' => 'button-md',
        'lg' => 'button-lg',
        default => 'button-lg',
    };
    
    $arrowSize = match ($link_size) {
        'sm' => 'icon-opsz-16!',
        'md' => 'icon-opsz-20!',
        'lg' => 'icon-opsz-24!',
        default => 'icon-opsz-24!',
    };
    
    $underlineOffset = match ($link_size) {
        'sm' => 'after:bottom-px',
        'md' => 'after:bottom-[3px]',
        'lg' => 'after:bottom-[2px] md:after:bottom-[3px]',
        default => 'after:bottom-[2px] md:after:bottom-[3px]',
    };

    $containerClass = $containerClass ?? '';
    $linkDefaultClass = "animated-underline flex text-inherit font-medium h-fit";
    $insideAnchor = $insideAnchor ?? false;
@endphp

<div class="w-full flex {{$containerClass}}" data-no-custom-cursor="true">
    @if($insideAnchor)
        <p class="{{$link_class}} {{$linkDefaultClass}} {{$underlineOffset}}" data-no-custom-cursor="true"><span class="relative z-1">{{$label}}</span></p>
    @else
        <a class="{{$link_class}} {{$linkDefaultClass}} {{$underlineOffset}}" href="{{$url}}" target="{{$target}}"><span class="relative z-1">{{$label}}</span></a>
    @endif
    @include('partials.material-icon', ['name' => 'arrow_outward', 'class' => $arrowSize])
</div>
