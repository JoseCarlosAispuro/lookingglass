@php
    $background_color = $background_color ?? 'white';
    $text_color = $text_color ?? 'black';
    $hideHeading = $hideHeading ?? false;
    $class_name = "py-12 md:py-40 bg-{$background_color} text-(--app-fg-color)";
    $quotes = $quotes ?? [];
@endphp

@component('partials.block', compact('block_name', 'class_name'))
<div class="relative" aria-labelledby="section-title">
    @if(!$hideHeading)
        <h2 class="sr-only" id="section-title">{{$headingLeft}} {{$headingRight}}</h2>
    @endif

    <div class="relative container-ultrawide grid grid-cols-12 gap-2.5 md:gap-x-5 gap-y-12 md:gap-y-30">
        @if(!$hideHeading)
            <div class="col-span-12">
                <p
                    data-word-animate
                    data-animate-preset="wordUp"
                    data-animate-delay="0"
                    data-animate-duration="1.5"
                    data-animate-stagger="0.1"
                    class="uppercase headline-3 font-semibold md:!flex md:!justify-between transition-all ease-in-out text-center">{{$heading}}</p>
            </div>
        @endif

        @if(count($quotes) > 1)
            <div class="relative col-span-12" data-custom-cursor>
                <div class="swiper"
                    data-slides-per-view="1"
                    data-space-between-slides="0"
                    data-slides-per-view-mobile="1"
                    data-space-between-slides-mobile="16"
                    data-show-custom-cursor="true"
                    data-allow-touch-move="true"
                    data-free-mode="false"
                >
                    <div class="swiper-wrapper" draggable="true">
                        @foreach ($quotes as $index => $quote)
                            <div class="swiper-slide !grid grid-cols-12">
                                <div class="col-span-12 md:col-span-10 md:col-start-2">
                                    <div class="!flex justify-center">
                                        <div class="flex flex-col items-center gap-10">
                                            <p class="display-sm font-weight-light text-center">{{$quote['quote']}}</p>
                                            <p class="headline-6 font-bold text-center uppercase">{{$quote['author']}} {{ $quote['authors_role'] ? ", {$quote['authors_role']}" : ''  }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @include('partials.slider-pagination')
                </div>

                @include('partials.custom-cursor', ['text' => 'Drag'])
            </div>
        @else
            <div class="col-span-12 md:col-span-10 md:col-start-2">
                <div class="!flex justify-center">
                    <div class="flex flex-col items-center gap-10">
                        <p class="display-sm font-weight-light text-center">{{$quotes[0]['quote']}}</p>
                        <p class="headline-6 font-bold text-center uppercase">{{$quotes[0]['author']}} {{ $quotes[0]['authors_role'] ? ", {$quotes[0]['authors_role']}" : ''  }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endcomponent

@unset($background_color)
@unset($text_color)
@unset($headingLeft)
@unset($headingRight)
@unset($class_name)
