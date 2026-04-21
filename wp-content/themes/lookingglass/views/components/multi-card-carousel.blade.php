@php
    $heading = get_field('heading');
    $supportText = get_field('support_text');
    $cta = get_field('cta');
    $cards = get_field('cards');
    $splitHeading = explode(" ", $heading);
    $background_color = $background_color ?? 'black';

    $class_name = "py-[80px] md:py-[160px] text-(--app-fg-color) bg-".$background_color;
@endphp

@component('partials.block', compact('block_name', 'class_name'))
    <div class="container-ultrawide">
        <p
            data-word-animate
            data-animate-preset="wordUp"
            data-animate-delay="0"
            data-animate-duration="1.5"
            data-animate-stagger="0.1"
            class="headline-3 uppercase font-medium md:!flex md:!justify-between">{{$heading}}</p>
        <div class="grid grid-cols-12 mt-[24px] md:mt-[160px] gap-x-sm">
            <div class="col-span-12 md:col-span-3 col-start-1 flex flex-col items-start gap-[24px] md:gap-[32px]">
                <p class="body-lg">{{$supportText}}</p>
                @include('components.button-block', [
                    'buttonLink' => $cta,
                    'variant' => 'primary',
                ])
            </div>
            <div class="relative col-span-12 md:col-span-8 col-start-1 md:col-start-5 mt-[48px] md:mt-0"
                 data-custom-cursor>
                <div class="swiper">
                    <div class="swiper-wrapper" draggable="true">
                        @foreach($cards as $card)
                            <div class="swiper-slide aspect-4/5">
                                <a class="group relative flex h-full" href="{{$card['link']['url']}}" target="{{$card['link']['target'] ?? '_self'}}">
                                    {!! wp_get_attachment_image($card['background_image']['ID'] ?? null, 'large', false, [
                                        'alt' => $card['background_image']['alt'] ?? '',
                                        'class' => 'h-full w-full object-cover',
                                        'sizes' => '(min-width: 768px) 33vw, 80vw',
                                        'loading' => 'lazy',
                                        ])
                                    !!}
                                    <div
                                        class="absolute bottom-0 left-0 w-full h-fit p-sm bg-gradient-to-t from-black to-transparent flex flex-col gap-sm opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity ease-in-out duration-500">
                                        <p class="headline-6 font-bold md:font-medium text-white">{{$card['title']}}</p>
                                        <p class="body-sm text-white font-light">{{$card['description']}}</p>
                                        <div class="[--app-bg-color:transparent]">
                                            @include('partials.link', [
                                                'insideAnchor' => true,
                                                'isSmall' => true,
                                                'target' => $card['link']['target'],
                                                'url' => $card['link']['url'],
                                                'label' => $card['link']['title']
                                            ])
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-9">
                        @include('partials.slider-pagination')
                    </div>
                </div>
                @include('partials.custom-cursor', ['text' => 'Drag'])
            </div>
        </div>
    </div>
@endcomponent

@unset($class_name)
