@php
    $background_color = $background_color ?? 'black';
    $no_bg_change = false;
    
    $class_name = "w-full text-(--app-fg-color) bg-".$background_color;
@endphp

@component('partials.block', compact('block_name', 'class_name', 'no_bg_change'))
    @if($text)
        <div class="overflow-hidden whitespace-nowrap bg-(--app-fg-color)/9" data-marquee data-marquee-speed="{{ $speed ?? '80'}}">
            <div class="inline-flex items-center will-change-transform motion-reduce:transform-none motion-reduce:!animate-none motion-reduce:flex-wrap motion-reduce:justify-center motion-reduce:gap-10" data-marquee-track>
                <div class="pr-8" data-marquee-text>
                    <span class="headline-6 uppercase font-bold">{{ $text }}</span>
                </div>
            </div>
        </div>
    @endif
@endcomponent