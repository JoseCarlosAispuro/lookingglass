@php
    $background_color = $background_color ?? 'black';

    $class_name = "py-20 md:py-40 bg-{$background_color} text-(--app-fg-color)";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
<div class="relative container-ultrawide flex flex-col gap-y-6 md:gap-y-12 md:gap-y-20" aria-labelledby="section-title">
    <div class="grid grid-cols-12 gap-y-6 md:gap-0">
        <div class="col-span-12 md:col-span-6 flex flex-col gap-y-6 md:gap-y-20">
            <h2
                data-word-animate
                data-animate-preset="wordUp"
                data-animate-delay="0"
                data-animate-duration="1.5"
                data-animate-stagger="0.1"
                class="headline-3 uppercase font-semibold !hidden md:!block">{{$heading}}</h2>
            <div class="flex md:hidden">
                <p data-word-animate
                data-animate-preset="wordUp"
                data-animate-delay="0"
                data-animate-duration="1.5"
                data-animate-stagger="0.1" class="headline-2 uppercase font-semibold">{{$heading}}</p>
            </div>

            <div class="grid grid-cols-6">
                <div class="col-span-6 md:col-span-5 flex flex-col gap-y-8">
                    <div class="body-lg">
                        {!! $content !!}
                    </div>
                    <div class="hidden md:block">
                        @include('components.button-block', [
                            'buttonLink' => $link,
                            'variant' => 'secondary',
                            'ariaLabel' => 'Learn more',
                        ])
                    </div>
                </div>
            </div>
        </div>

        <div class="relative group col-span-12 md:col-span-5 md:col-start-8">
            <img class="h-full w-full object-cover opacity-100 transition-opacity duration-300 group-hover:opacity-0" src="{{$image['url']}}" alt="{{$image['alt']}}" loading="lazy">
            <img class="absolute inset-0 h-full w-full object-cover opacity-0 transition-opacity duration-300 group-hover:opacity-100" src="{{$image_on_hover['url']}}" alt="{{$image_on_hover['alt']}}" loading="lazy">
        </div>

        <div class="col-span-12 block md:hidden w-full">
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
