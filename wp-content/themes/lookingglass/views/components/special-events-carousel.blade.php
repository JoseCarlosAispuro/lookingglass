@php
    $supportingText = get_field('supporting_text');
    $events = get_field('events') ?? [];

    $background_color = $background_color ?? 'black';
    $class_name = "py-[48px] md:py-[160px] bg-{$background_color} text-(--app-fg-color)";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
    <div class="container-ultrawide">
        <div class="grid grid-cols-12 gap-x-sm">
            <div class="col-span-12 md:col-span-7 col-start-1 md:col-start-1">
                <h3 data-word-animate
                    data-animate-preset="wordUp"
                    data-animate-delay="0"
                    data-animate-duration="1.5"
                    data-animate-stagger="0.1" class="headline-2 uppercase flex! justify-between! flex-wrap!">
                    {{$heading}}                
                </h3>
            </div>
            <div class="col-span-12 md:col-span-4 col-start-1 md:col-start-9 mt-md md:mt-0">
                <p class="body-lg">{{$supportingText}}</p>
            </div>
            <div class="col-span-12 col-start-1 mt-lg md:mt-20 pt-sm md:py-sm border-t border-b-0 md:border-y border-border-secondary">
                <div class="relative w-full" {{count($events) <= 3 ?: 'data-custom-cursor'}}>
                    <div class="swiper"
                         data-slides-per-view="3"
                         data-space-between-slides="16"
                         data-slides-per-view-mobile="1"
                         data-space-between-slides-mobile="16"
                         data-show-custom-cursor="true"
                         data-allow-touch-move="true"
                         data-free-mode="true">
                        <div class="swiper-wrapper flex !h-auto" draggable="true">
                            @foreach($events as $event)
                                <div class="swiper-slide !h-auto">
                                    <div class="flex flex-col group !h-full">
                                        <div class="aspect-square w-full overflow-hidden relative pt-[100%]">
                                            <img
                                                src="{{$event['event_image']['url']}}"
                                                alt="{{$event['event_image']['alt']}}"
                                                class="w-full h-full scale-100 group-hover:scale-110 transition-all duration-300 ease-out object-cover absolute top-0 left-0"
                                                loading="lazy"
                                                >
                                        </div>
                                        <div class="mt-sm flex flex-col !h-full justify-between gap-md md:gap-16">
                                            <div class="flex flex-col gap-md md:gap-[32px]">
                                                <div class="flex flex-col gap-xs">
                                                    <p class="headline-6 font-bold">{{$event['title']}}</p>
                                                    <p class="body-sm opacity-40 font-weight-light">{{$event['subtitle']}}</p>
                                                </div>
                                                <p class="body-md">{{$event['description']}}</p>
                                            </div>
                                            @include('partials.link', [
                                                'label' => $event['cta']['title'],
                                                'url' => $event['cta']['url'],
                                                'isWhiteBg' => true
                                            ])
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="md:hidden mt-4 border-t border-border-secondary">
                            @include('partials.slider-pagination')
                        </div>
                    </div>
                    
                    @if(!empty($events) && count($events) > 3)
                        @include('partials.custom-cursor', ['text' => 'Drag'])
                    @endif
                </div>
            </div>
        </div>
    </div>
@endcomponent
