@php
    $plays = get_field('plays');
    $heading = get_field('heading');
    $supportiveCopy = get_field('support_copy');
    $button = get_field('button_link');
    $quotes = get_field('quotes');
    $pastPlays = $plays ? get_past_plays($plays) : null;

    $background_color = $background_color ?? 'black';
    /* $text_color = $background_color === 'black' ? 'white' : 'black'; */

    $class_name = "py-xl md:py-[160px] bg-{$background_color} text-(--app-fg-color)";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
    <div class="container-ultrawide grid grid-cols-12 gap-y-[40px]">
        <div class="col-span-12 md:col-span-6 col-start-1 md:col-start-4 flex text-center justify-center">
            <h3
                data-word-animate
                data-animate-preset="wordUp"
                data-animate-delay="0"
                data-animate-duration="1.5"
                data-animate-stagger="0.1"
                class="headline-1 font-semibold !w-fit">{{$heading}}</h3>
        </div>
        <div class="col-span-12 md:col-span-6 col-start-1 md:col-start-4 flex text-center">
            <p class="body-lg">{{$supportiveCopy}}</p>
        </div>
    </div>
    <div class="relative w-full mt-lg md:mt-xl overflow-hidden" data-custom-cursor>
        <div class="swiper-custom">
            <div class="swiper-wrapper" draggable="true">
                @foreach($pastPlays as $play)
                    <div class="swiper-slide !w-[332px]">
                        @include('components.cards.play-card', [
                            'playId' => $play['play'][0],
                            'title' => get_the_title($play['play'][0]),
                            'url' => get_the_permalink($play['play'][0]),
                            'image' => get_the_post_thumbnail_url($play['play'][0]),
                            'subHeading' => $play['play_sub_heading'],
                            'pastPlaysStyle' => true
                        ])
                    </div>
                @endforeach
            </div>
            
            <div class="relative z-10 -translate-y-26 px-4">
                @include('partials.slider-pagination')
            </div>
        </div>
        @include('partials.custom-cursor', ['text' => 'Drag'])
    </div>
    @if($button['title'] && $button['url'])
        <div class="w-full flex justify-center mt-xl px-4 md:px-0">
            @include('components.button-block', [
                'buttonLink' => $button,
                'variant' => 'secondary',
                'ariaLabel' => get_field('button_aria_label'),
            ])
        </div>
    @endif
    @if(count($quotes) > 1)
        <div class="relative w-full mt-lg md:mt-[160px]" data-custom-cursor>
            <div class="swiper"
                 data-slides-per-view="1"
                 data-space-between-slides="0"
                 data-slides-per-view-mobile="1"
                 data-space-between-slides-mobile="16"
                 data-show-custom-cursor="true"
                 data-allow-touch-move="true"
                 data-free-mode="false">
                <div class="swiper-wrapper" draggable="true">
                    @foreach($quotes as $quote)
                        <div class="swiper-slide !flex justify-center">
                            <div class="max-w-[83%] flex flex-col items-center gap-[40px]">
                                <h3 class="display-sm font-weight-light text-center">{{$quote['quote']}}</h3>
                                <img src="{{$quote['quote_image']['url']}}" alt="{{$quote['quote_image']['alt']}}" loading="lazy">
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-9 px-4">
                    @include('partials.slider-pagination')
                </div>
            </div>
            @include('partials.custom-cursor', ['text' => 'Drag'])
        </div>
    @endif
@endcomponent

@unset($plays)
@unset($heading)
@unset($supportiveCopy)
@unset($pastPlays)
@unset($button)
