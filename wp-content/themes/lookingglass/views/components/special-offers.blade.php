@php
    $background_color = $background_color ?? 'black';
    
    $class_name = "py-20 md:py-40 bg-{$background_color} text-(--app-fg-color)";
@endphp 

@component('partials.block', compact('block_name', 'class_name'))
<div class="relative flex flex-col gap-y-12 md:gap-y-20" aria-labelledby="section-title">
    <h2 class="sr-only" id="section-title">{{$heading_left}} {{$heading_right}}</h2>

    <div class="relative container-ultrawide grid grid-cols-12 gap-2.5 md:gap-x-5 gap-y-12 md:gap-y-30">
        <div class="col-span-12 md:col-span-5 md:col-start-2">
            <p class="headline-3 uppercase font-semibold" data-slide-title>{{$heading_left}} <span class="block md:hidden">{{$heading_right}}</span></p>
        </div>
        <div class="hidden md:block col-span-4 col-start-7">
            <p class="headline-3 uppercase font-semibold">{{$heading_right}}</p>
        </div>
    </div>

    <div class="relative px-4 md:px-0 grid grid-cols-12 gap-2.5 md:gap-x-5 gap-y-12 md:gap-y-30">
        @if($offers)
            <div class="relative col-span-12" data-custom-cursor>
                <div class="swiper !h-max md:!h-full" 
                    data-slides-per-view="2.2"
                    data-space-between-slides="16"
                    data-slides-per-view-mobile="1"
                    data-offset-before-from-element="#{{$anchor}} [data-slide-title]"
                    data-space-between-slides-mobile="16"
                    data-show-custom-cursor="true"
                    data-allow-touch-move="true"
                    data-free-mode="false"
                >
                    <div class="swiper-wrapper h-full" draggable="true">
                        @foreach ($offers as $index => $offer)
                            <div class="swiper-slide !h-full">
                                <div class="bg-(--app-fg-color) text-(--app-bg-color) rounded-lg p-4 flex flex-col gap-y-20 h-full">
                                    <p class="headline-5 font-semibold">{{$offer['title']}}</p>
                                    
                                    <div class="pt-0 md:pt-4 md:border-t border-solid border-(--app-bg-color)/30 flex flex-col gap-y-6 md:gap-y-8 justify-between h-full">
                                        <div class="body-lg">{!! $offer['description'] !!}</div>
                                        <div class="w-fit">
                                            @include('components.icon-cta', [
                                                'cta' => [
                                                    'text' => 'CODE: '.($offer['code'] && strlen($offer['code']) > 0 ? $offer['code'] : 'NO CODE NEEDED'),
                                                    'icon' => 'link',
                                                    'action' => 'copy',
                                                    'text_to_copy' => $offer['code'],
                                                ],
                                                'colorInverted' => true
                                            ])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex md:hidden swiper-navigation justify-between mt-[48px]">
                        <button class="swiper-button-prev !relative !w-[32px] !h-[32px] !top-0 !left-0 !mt-0">@include('partials.material-icon', ['name' => 'chevron_left', 'class' => 'icon-opsz-20 text-(--app-fg-color)'])</button>
                        <div
                            class="swiper-pagination !w-fit !text-(--app-fg-color) !relative !top-0 !left-0 !flex items-center"></div>
                        <button class="swiper-button-next !relative !w-[32px] !h-[32px] !top-0 !left-0 !mt-0">@include('partials.material-icon', ['name' => 'chevron_right', 'class' => 'icon-opsz-20 text-(--app-fg-color)'])</button>
                    </div>
                </div>

                @include('partials.custom-cursor', ['text' => 'Drag'])
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