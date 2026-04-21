@php
    $heading_left = $heading_left ?? '';
    $heading_right = $heading_right ?? '';
    $timeline_slides = $timeline_slides ?? [];
    $background_color = $background_color ?? 'black';

    $class_name = "overflow-hidden bg-{$background_color} text-(--app-fg-color) relative";

    $first_year = $timeline_slides[0]['year'] ?? '2000';
    $first_year_digits = str_split($first_year);

    // Extract ACF image ID from array or scalar field value
    $acf_img_id = function ($field) {
        if (is_array($field)) {
            return $field['ID'] ?? $field['id'] ?? null;
        }
        return $field ?: null;
    };
@endphp

@component('partials.block', compact('block_name', 'class_name'))
<div data-timeline data-slide-duration="5000" class="h-dvh md:h-[calc(100dvh+var(--spacing)*32)] flex flex-col gap-20 py-10 md:py-20 relative">

    {{-- Heading --}}
    <div class="container-ultrawide">
        {{-- Single merged heading (mobile through md) --}}
        <h2 class="lg:hidden headline-2 font-semibold uppercase"
            data-word-animate
            data-animate-preset="wordUp"
            data-animate-stagger="0.1">{{ $heading_left }} {{ $heading_right }}</h2>
        {{-- Split headings (lg+) --}}
        <div class="hidden lg:flex lg:justify-between gap-y-xs">
            <h2 class="headline-2 font-semibold uppercase"
                data-word-animate
                data-animate-preset="wordUp"
                data-animate-stagger="0.1">{{ $heading_left }}</h2>
            <h2 class="headline-2 font-semibold uppercase"
                data-word-animate
                data-animate-preset="wordUp"
                data-animate-stagger="0.1"
                data-animate-delay="0.3">{{ $heading_right }}</h2>
        </div>
    </div>

    {{-- Image Stage --}}
    <div class="relative flex-1 min-h-0 container-ultrawide" data-custom-cursor>
        {{-- Background Year (odometer) --}}
        <div class="absolute inset-0 flex items-center justify-center z-0 pointer-events-none select-none" aria-hidden="true" data-timeline-year>
            @foreach($first_year_digits as $digit)
                <span class="inline-block overflow-hidden h-[260px] md:h-[600px] py-[10px] md:py-[20px]" data-timeline-digit-wrapper>
                    <span data-timeline-digit data-digit="{{ $digit }}">
                        @for($d = 0; $d <= 9; $d++)
                            <span class="block h-[240px] md:h-[560px] leading-[240px] md:leading-[560px] text-[240px] md:text-[560px] font-cambon-condensed font-thin text-(--app-fg-color)/20 text-center">{{ $d }}</span>
                        @endfor
                    </span>
                </span>
            @endforeach
        </div>

        {{-- Main Swiper (all breakpoints) --}}
        <div class="swiper absolute inset-0 md:left-gutter md:right-gutter z-1 overflow-hidden" data-timeline-main-swiper>
            <div class="swiper-wrapper">
                @foreach($timeline_slides as $index => $slide)
                    @php
                        $template = $slide['slide_template'] ?? '1_image';
                        $img1 = $acf_img_id($slide['image_1'] ?? null);
                        $img2 = $acf_img_id($slide['image_2'] ?? null);
                        $img3 = $acf_img_id($slide['image_3'] ?? null);
                        $loading = $index === 0 ? 'eager' : 'lazy';
                    @endphp
                    <div class="swiper-slide">
                        <div class="grid grid-cols-4 grid-rows-1 gap-x-md h-full md:grid-cols-12 [&>*]:row-start-1">
                            @if($template === '1_image')
                                @if($img1)
                                    <div class="col-start-2 col-span-2 self-center md:col-start-5 md:col-span-3">
                                        {!! wp_get_attachment_image($img1, 'large', false, [
                                            'class' => 'w-full aspect-square object-cover',
                                            'sizes' => '(min-width: 1024px) 25vw, 50vw',
                                            'loading' => $loading,
                                        ]) !!}
                                    </div>
                                @endif
                            @elseif($template === '2_images')
                                @if($img2)
                                    <div class="col-start-3 col-span-2 self-start md:col-start-9 md:col-span-3">
                                        {!! wp_get_attachment_image($img2, 'large', false, [
                                            'class' => 'w-full aspect-square object-cover',
                                            'sizes' => '(min-width: 1024px) 25vw, 50vw',
                                            'loading' => $loading,
                                        ]) !!}
                                    </div>
                                @endif
                                @if($img1)
                                    <div class="col-start-1 col-span-2 self-end md:col-start-2 md:col-span-4">
                                        {!! wp_get_attachment_image($img1, 'large', false, [
                                            'class' => 'w-full aspect-[9/5] object-cover',
                                            'sizes' => '(min-width: 1024px) 33vw, 50vw',
                                            'loading' => $loading,
                                        ]) !!}
                                    </div>
                                @endif
                            @elseif($template === '3_images')
                                @if($img1)
                                    <div class="col-start-1 col-span-2 self-start md:col-start-2 md:col-span-4">
                                        {!! wp_get_attachment_image($img1, 'large', false, [
                                            'class' => 'w-full aspect-[9/5] object-cover',
                                            'sizes' => '(min-width: 1024px) 33vw, 50vw',
                                            'loading' => $loading,
                                        ]) !!}
                                    </div>
                                @endif
                                @if($img2)
                                    <div class="col-start-3 col-span-2 translate-y-[15%] self-center md:translate-y-0 md:col-start-9 md:col-span-3">
                                        {!! wp_get_attachment_image($img2, 'large', false, [
                                            'class' => 'w-full aspect-square object-cover',
                                            'sizes' => '(min-width: 1024px) 25vw, 50vw',
                                            'loading' => $loading,
                                        ]) !!}
                                    </div>
                                @endif
                                @if($img3)
                                    <div class="col-start-2 col-span-2 self-end md:col-start-5 md:col-span-3">
                                        {!! wp_get_attachment_image($img3, 'large', false, [
                                            'class' => 'w-full aspect-[7/4] object-cover',
                                            'sizes' => '(min-width: 1024px) 25vw, 50vw',
                                            'loading' => $loading,
                                        ]) !!}
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @include('partials.custom-cursor', ['text' => 'Drag'])
    </div>

    {{-- Timeline Rail (Desktop) - Swiper Thumbs --}}
    <div class="hidden md:block container-ultrawide relative overflow-visible" data-timeline-rail-container data-custom-cursor>
        <div class="swiper" data-timeline-thumbs-swiper>
            <div class="swiper-wrapper">
                @foreach($timeline_slides as $index => $slide)
                    <div class="swiper-slide timeline-card"
                         data-timeline-card="{{ $index }}"
                         data-full-desc="{{ esc_attr($slide['description']) }}">
                        {{-- Separator + progress --}}
                        <div class="relative">
                            <div class="h-px w-full bg-(--app-fg-color) opacity-20"
                                 data-timeline-separator="{{ $index }}"></div>
                            <div class="absolute top-0 left-0 h-px bg-(--app-fg-color) opacity-40"
                                 style="width: 0%"
                                 data-timeline-progress="{{ $index }}"></div>
                        </div>
                        {{-- Card content --}}
                        <div class="pt-sm">
                            <p class="text-[20px] font-semibold leading-tight" data-timeline-card-year>{{ $slide['year'] }}</p>
                            <p class="body-sm text-(--app-fg-color)/80 mt-xs line-clamp-2"
                               data-timeline-card-desc="{{ $index }}"><span data-timeline-card-text>{{ $slide['description'] }}</span><button class="font-medium underline cursor-pointer hidden ml-[0.3em]"
                                       data-timeline-more="{{ $index }}"
                                       data-no-custom-cursor
                                       type="button">more</button></p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @include('partials.custom-cursor', ['text' => 'Drag'])
    </div>

    {{-- Timeline Controls (Mobile) --}}
    <div class="md:hidden container-ultrawide" data-timeline-mobile>
        {{-- Progress bar (single line) --}}
        <div class="h-px w-full bg-(--app-fg-color)/10 mb-md relative">
            <div class="absolute top-0 left-0 h-full bg-(--app-fg-color)/40"
                 style="width: 0%"
                 data-timeline-mobile-progress></div>
        </div>
        {{-- Arrow controls + content --}}
        <div class="flex items-start gap-sm">
            <button class="w-8 h-8 shrink-0 flex items-center justify-center opacity-40"
                    data-timeline-prev
                    type="button"
                    aria-label="Previous milestone">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            <div class="flex-1 text-center min-w-0">
                <p class="text-[18px] font-medium" data-timeline-mobile-year>{{ $timeline_slides[0]['year'] ?? '' }}</p>
                <p class="body-sm text-(--app-fg-color)/80 mt-xs line-clamp-2 min-h-[2lh]"
                   data-timeline-mobile-desc><span data-timeline-mobile-text>{{ $timeline_slides[0]['description'] ?? '' }}</span><button class="font-medium underline cursor-pointer hidden ml-[0.3em]"
                        data-timeline-mobile-more
                        type="button">more</button></p>
            </div>
            <button class="w-8 h-8 shrink-0 flex items-center justify-center opacity-40"
                    data-timeline-next
                    type="button"
                    aria-label="Next milestone">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        </div>
    </div>

    {{-- Modal (shared desktop/mobile) --}}
    <div class="absolute z-50 left-gutter right-gutter md:left-auto md:right-4 md:w-[calc(33.333%-16px)] lg:w-[calc(25%-18px)] bg-white rounded-lg p-sm"
         data-timeline-modal
         data-no-custom-cursor
         data-state="closed"
         role="dialog"
         aria-modal="true"
         aria-label="Milestone details">
        <div class="flex justify-between items-start mb-xs">
            <p class="text-[20px] font-semibold text-black" data-timeline-modal-year></p>
            <button class="w-6 h-6 shrink-0 flex items-center justify-center text-black cursor-pointer"
                    data-timeline-modal-close
                    type="button"
                    aria-label="Close">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="text-black body-sm [&>p+p]:mt-xs" data-timeline-modal-body></div>
    </div>

</div>
@endcomponent

@unset($heading_left)
@unset($heading_right)
@unset($timeline_slides)
@unset($background_color)
@unset($first_year)
@unset($first_year_digits)
@unset($acf_img_id)
