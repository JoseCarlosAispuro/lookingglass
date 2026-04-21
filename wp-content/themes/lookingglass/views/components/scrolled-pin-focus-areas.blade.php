@php
    $heading_left = $heading_left ?? '';
    $heading_center = $heading_center ?? '';
    $heading_right = $heading_right ?? '';
    $subheading = $subheading ?? '';
    $image = $image ?? null;
    $focus_areas = $focus_areas ?? [];

    $image_id = is_array($image) ? ($image['ID'] ?? $image['id'] ?? null) : $image;

    $background_color = $background_color ?? 'black';
    $focus_area_count = count($focus_areas);
    $scroll_height = 300 + ($focus_area_count * 25);

    $class_name = "bg-{$background_color} text-(--app-fg-color) scrolled-pin pb-12 md:pb-20";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
{{-- Heading row: desktop — each part animated separately --}}
<div class="container-ultrawide relative z-3 pt-12 md:pt-20 hidden lg:flex justify-between">
    <h2 class="headline-3 uppercase"
        data-word-animate data-animate-preset="wordUp"
        data-animate-delay="0">
        {{ $heading_left }}
    </h2>
    <h2 class="headline-3 uppercase"
        data-word-animate data-animate-preset="wordUp"
        data-animate-delay="0.15">
        {{ $heading_center }}
    </h2>
    <h2 class="headline-3 uppercase"
        data-word-animate data-animate-preset="wordUp"
        data-animate-delay="0.3">
        {{ $heading_right }}
    </h2>
</div>

{{-- Heading row: mobile — single concatenated heading --}}
<div class="container-ultrawide relative z-3 pt-12 lg:hidden">
    <h2 class="headline-3 uppercase"
        data-word-animate data-animate-preset="wordUp">
        {{ $heading_left }} {{ $heading_center }} {{ $heading_right }}
    </h2>
</div>

{{-- Subheading --}}
@if($subheading)
    <div class="container-ultrawide relative z-3 mt-sm md:mt-20">
        <div class="md:ml-[25%] md:w-1/2">
            <div class="body-lg text-(--app-fg-color)" data-rich-text>
                {!! $subheading !!}
            </div>
        </div>
    </div>
@endif

<div data-scrolled-pin class="mt-lg md:mt-xl" style="--scroll-height: {{ $scroll_height }}vh">
    <div class="scrolled-pin__sticky">
        {{-- Image: centered in sticky, width/height animated by JS --}}
        <div data-scrolled-pin-image class="scrolled-pin__image-area">
            @if($image_id)
                {!! wp_get_attachment_image($image_id, 'full', false, [
                    'class' => 'scrolled-pin__img',
                    'loading' => 'eager',
                ]) !!}
            @endif
        </div>

        {{-- Overlay + content: positioned over entire sticky viewport --}}
        <div data-scrolled-pin-overlay class="scrolled-pin__overlay"></div>
        <div data-scrolled-pin-content class="scrolled-pin__content">
            <div class="relative z-1 flex justify-end pb-sm md:pb-md">
                <div class="w-full md:w-1/2 flex flex-col gap-8">
                    @foreach($focus_areas as $index => $area)
                        @if($index > 0)
                            <hr class="border-white/20">
                        @endif
                        <div>
                            <h3 class="headline-6 font-bold uppercase text-white">{{ $area['heading'] }}</h3>
                            <div class="body-sm text-white mt-sm" data-rich-text>
                                {!! $area['body'] !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Mobile: static focus areas below image --}}
        <div class="md:hidden px-sm pt-12">
            @foreach($focus_areas as $index => $area)
                @if($index > 0)
                    <hr class="border-(--app-fg-color)/20 my-8">
                @endif
                <h3 class="headline-6 font-bold uppercase">{{ $area['heading'] }}</h3>
                <div class="body-sm text-(--app-fg-color) mt-8" data-rich-text>
                    {!! $area['body'] !!}
                </div>
            @endforeach
        </div>
    </div>
</div>
@endcomponent

@unset($heading_left)
@unset($heading_center)
@unset($heading_right)
@unset($subheading)
@unset($image)
@unset($image_id)
@unset($focus_areas)
@unset($focus_area_count)
@unset($scroll_height)
