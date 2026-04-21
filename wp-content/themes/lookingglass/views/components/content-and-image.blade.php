@php
    $background_color = $background_color ?? 'white';
    $headingPrimarySplit = explode("\n", $heading_primary);

    $class_name = "py-20 md:py-40 bg-{$background_color} text-(--app-fg-color)";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
<div class="relative container-ultrawide flex flex-col gap-y-6 md:gap-y-12 md:gap-y-20" aria-labelledby="section-title">
    <h2 class="sr-only" id="section-title">{{$heading_primary}} {{$heading_secondary}}</h2>

    <div class="hidden md:grid grid-cols-12">
        <div class="col-span-12 md:col-span-8 md:col-start-2">
            @foreach($headingPrimarySplit as $index => $headingPart)
                <p data-word-animate
                    data-animate-preset="wordUp"
                    data-animate-delay="{{ $index * 0.4}}"
                    data-animate-duration="1.5"
                    data-animate-stagger="0.1" class="headline-2 uppercase font-semibold md:whitespace-break-spaces" data-slide-title>{{$headingPart}}</p>
            @endforeach
        </div>
        <div class="col-span-7 col-start-6">
            <p data-word-animate
            data-animate-preset="wordUp"
            data-animate-delay="0.8"
            data-animate-duration="1.5"
            data-animate-stagger="0.1" class="headline-2 uppercase font-semibold">{{$heading_secondary}}</p>
        </div>
    </div>

    <div class="grid md:hidden grid-cols-12">
        <div class="col-span-12">
            <p data-word-animate
            data-animate-preset="wordUp"
            data-animate-delay="0"
            data-animate-duration="1.5"
            data-animate-stagger="0.1" class="headline-2 uppercase font-semibold">{{$heading_primary}} {{$heading_secondary}}</p>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-y-6 md:gap-y-20">
        <div class="col-span-12 md:col-span-5 md:col-start-2">
            <div class="body-xl">
                {!! $content !!}
            </div>
        </div>
        <div class="col-span-12 md:col-span-4 md:col-start-8">
            <img src="{{$image['url']}}" alt="{{$image['alt']}}" class="" loading="lazy">
        </div>
        <div class="col-span-12 md:col-span-5 md:col-start-2 pt-md border-t border-black-100">
            <div class="body-md">
                {{ $support_text }}
            </div>
        </div>
        <div class="col-span-12 md:col-span-4 md:col-start-8 flex items-end">
            @include('components.button-block', [
                'buttonLink' => $link,
                'variant' => 'secondary',
                'ariaLabel' => 'Learn more',
            ])
        </div>
    </div>
</div>
@endcomponent

@unset($background_color)
@unset($text_color)
@unset($headingLeft)
@unset($headingRight)
@unset($class_name)
