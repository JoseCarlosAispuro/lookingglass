@php
    $plays = get_field('plays');
    $headingLeft = get_field('heading_left');
    $headingRight = get_field('heading_right');
    $supportiveCopy = get_field('support_copy');

    $background_color = $background_color ?? 'black';

    $class_name = "bg-{$background_color} text-(--app-fg-color)";

    // Normalize plays into the same shape as whatson items
    $items = [];
    if ($plays) {
        foreach ($plays as $play) {
            $playId = $play['play'][0];
            $thumbnailId = get_post_thumbnail_id($playId);
            $items[] = [
                'imageId' => $thumbnailId ?: null,
                'title' => get_the_title($playId),
                'meta' => str_replace([' - ', ' – '], ' — ', $play['sub_heading'] ?? ''),
                'url' => get_the_permalink($playId),
            ];
        }
    }

    $totalItems = count($items);
    $hasItems = $totalItems > 0;
@endphp

@component('partials.block', compact('block_name', 'class_name'))
    @if($hasItems)
        <div class="w-full py-20 md:py-40" data-whatson-block>
            <div class="container-ultrawide">
                {{-- Header --}}
                <div class="grid md:grid-cols-12 justify-between gap-x-4">
                    <div class="md:col-start-2 md:col-span-3">
                        <h2 class="headline-1 uppercase"
                            data-word-animate
                            data-animate-preset="wordUp"
                            data-animate-delay="0"
                            data-animate-duration="1.5"
                        >
                            {{ $headingLeft }}
                        </h2>
                    </div>

                    <div class="md:col-start-6 md:col-span-6">
                        <h2 class="headline-1 uppercase"
                            data-word-animate
                            data-animate-preset="wordUp"
                            data-animate-delay="0.1"
                            data-animate-duration="1.5"
                        >
                            {{ $headingRight }}
                        </h2>
                    </div>

                    @if($supportiveCopy)
                        <div class="mt-6 md:mt-20 md:row-start-2 md:col-span-6 md:col-start-6">
                            <p class="body-lg text-left">
                                {{ $supportiveCopy }}
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Carousel section --}}
                <div
                    class="mt-12 md:mt-20 relative md:grid grid-cols-12 gap-x-4"
                    data-whatson-carousel
                    data-total-items="{{ $totalItems }}"
                    data-drag-cursor data-drag-text="Drag"
                >
                    {{-- Custom drag cursor (only shown on desktop when overflow) --}}
                    <div
                        class="hidden md:block pointer-events-none fixed z-[9999] will-change-transform"
                        data-drag-cursor-element
                        style="opacity: 0; left: 0; top: 0;"
                    >
                        <div class="w-[80px] h-[80px] rounded-full bg-orange flex items-center justify-center -translate-x-1/2 -translate-y-1/2">
                            <span class="text-black font-saans font-semibold text-[16px]">Drag</span>
                        </div>
                    </div>

                    {{-- Swiper container --}}
                    <div class="swiper whatson-swiper md:col-span-10 md:col-start-2">
                        <div class="swiper-wrapper" data-whatson-wrapper>
                            @foreach($items as $index => $item)
                                <div class="swiper-slide !w-full md:!w-[18.9%]">
                                    @if($item['url'])
                                        <a href="{{ esc_url($item['url']) }}" class="block group" data-hide-cursor>
                                    @else
                                        <div class="block group">
                                    @endif

                                        {{-- Card image --}}
                                        <div class="w-full aspect-[2/3] min-h-[332px] overflow-hidden bg-neutral-800">
                                            @if($item['imageId'])
                                                {!! wp_get_attachment_image(
                                                    $item['imageId'],
                                                    'medium_large',
                                                    false,
                                                    [
                                                        'class' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-105',
                                                        'loading' => 'lazy',
                                                        'sizes' => '(max-width: 767px) 100vw, 19vw',
                                                    ]
                                                ) !!}
                                            @endif
                                        </div>

                                        {{-- Card info --}}
                                        <div class="mt-4">
                                            @if($item['title'])
                                                <h3 class="font-saans headline-6 uppercase">
                                                    {{ $item['title'] }}
                                                </h3>
                                            @endif

                                            @if($item['meta'])
                                                <p class="font-saans text-[16px] uppercase mt-1 text-(--app-fg-color)/60">
                                                    {{ $item['meta'] }}
                                                </p>
                                            @endif
                                        </div>

                                    @if($item['url'])
                                        </a>
                                    @else
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Mobile navigation --}}
                    <div class="flex md:hidden items-center justify-between mt-8">
                        <button
                            type="button"
                            class="w-10 h-10 flex items-center justify-center text-(--app-fg-color)/80 hover:text-(--app-fg-color) transition-colors disabled:opacity-30"
                            data-whatson-prev
                            aria-label="Previous slide"
                        >
                            @include('partials.material-icon', ['name' => 'chevron_left', 'class' => 'icon-opsz-24'])
                        </button>

                        <div class="font-saans text-[14px] tracking-wider" data-whatson-pagination>
                            <span data-current>01</span>—<span data-total>{{ str_pad($totalItems, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        <button
                            type="button"
                            class="w-10 h-10 flex items-center justify-center text-(--app-fg-color)/80 hover:text-(--app-fg-color) transition-colors disabled:opacity-30"
                            data-whatson-next
                            aria-label="Next slide"
                        >
                            @include('partials.material-icon', ['name' => 'chevron_right', 'class' => 'icon-opsz-24'])
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endcomponent
