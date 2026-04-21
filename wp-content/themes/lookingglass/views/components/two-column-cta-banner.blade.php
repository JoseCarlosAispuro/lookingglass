@php
    $background_color = $background_color ?? 'black';
    $text_color = $text_color ?? 'white';

    $class_name = 'bg-'.$background_color;
@endphp 

@component('partials.block', compact('block_name', 'class_name'))
<div class="relative py-4 md:p-10" aria-labelledby="section-title">
    <h2 class="sr-only" id="section-title">{{$heading_primary}} {{$heading_secondary}}</h2>

    <img class="absolute inset-0 w-full h-full object-cover" src="{{$background_image}}" loading="lazy">
    <div class="absolute top-0 left-0 w-full h-full md:h-1/2 pointer-events-none z-0 bg-gradient-to-b md:bg-gradient-to-t from-transparent to-black"></div>

    <div class="relative container-ultrawide overflow-hidden grid grid-cols-12 gap-y-6 md:gap-y-28 aspect-215/286 md:aspect-auto content-center content-end">
        <div class="col-span-12 md:col-span-6 md:col-start-2 flex">
            <div class="headline-3 font-semibold uppercase md:whitespace-pre-line text-{{$text_color}}">{{$heading_primary}} <span class="block md:hidden">{{$heading_secondary}}</span></div>
        </div>

        <div class="col-span-12 md:col-span-3 md:col-start-9">
            <div class="body-md w-full text-{{$text_color}}">{{$description}}</div>
        </div>

        <div class="hidden md:flex col-span-12 md:col-span-6 md:col-start-2 items-end">
            <div class="headline-3 font-semibold uppercase md:whitespace-pre-line w-full text-{{$text_color}}">{{$heading_secondary}}</div>
        </div>

        <div class="col-span-12 md:col-span-3 md:col-start-9">
            @include('components.button-block', [
                'buttonLink' => $link,
                'variant' => 'primary',
            ])
        </div>
    </div>
</div>
@endcomponent

@unset($background_color)
@unset($text_color)
@unset($backgroundImage)
@unset($headingPrimary)
@unset($headingSecondary)
@unset($link)
@unset($class_name)