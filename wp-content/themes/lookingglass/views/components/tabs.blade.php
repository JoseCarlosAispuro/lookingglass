@php
    $background_color = $background_color ?? 'white';
    
    $class_name = "overflow-hidden py-20 md:py-40 bg-{$background_color} text-(--app-fg-color)";
@endphp 

@component('partials.block', compact('block_name', 'class_name'))
<div class="relative" aria-labelledby="section-title">
    <h2 class="sr-only" id="section-title">{{$heading_left}} {{$heading_right}}</h2>

    <div class="relative container-ultrawide grid grid-cols-12 gap-x-2.5 gap-y-12 md:gap-x-5 md:gap-y-30">
        <div class="col-span-12 md:col-span-10 md:col-start-2 grid grid-cols-10">
            <div class="hidden md:block col-span-12 md:col-span-3">
                <div data-word-animate
                data-animate-preset="wordUp"
                data-animate-delay="0"
                data-animate-duration="1.5"
                data-animate-stagger="0.1" class="headline-2 font-semibold uppercase md:whitespace-pre-line">{{$heading_left}}</div>
            </div>
            <div class="hidden md:block col-span-6 col-start-5">
                <div data-word-animate
                data-animate-preset="wordUp"
                data-animate-delay="0.4"
                data-animate-duration="1.5"
                data-animate-stagger="0.1" class="headline-2 font-semibold uppercase md:whitespace-pre-line w-full">{{$heading_right}}</div>
            </div>
            <div class="col-span-12 flex md:hidden">
                <p data-word-animate
                data-animate-preset="wordUp"
                data-animate-delay="0"
                data-animate-duration="1.5"
                data-animate-stagger="0.1" class="headline-2 uppercase font-semibold">{{$heading_left}} {{$heading_right}}</p>
            </div>
        </div>

        <div class="col-span-12 md:col-span-10 md:col-start-2">
            <div data-tabs-wrapper>
                @foreach ($tabs as $index => $tab)
                    @include('components.cards.tab', compact('tab', 'index'))
                @endforeach
            </div>
        </div>
    </div>
</div>
@endcomponent

@unset($background_color)
@unset($class_name)
@unset($heading_left)
@unset($heading_right)
@unset($tabs)