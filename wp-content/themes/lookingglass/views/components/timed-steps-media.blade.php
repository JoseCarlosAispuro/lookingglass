@php
    $heading = $heading ?? '';
    $steps = $steps ?? [];
    $cta_heading = $cta_heading ?? '';
    $cta_description = $cta_description ?? '';
    $cta_link = $cta_link ?? null;

    $background_color = $background_color ?? 'black';

    $class_name = "overflow-hidden bg-{$background_color} text-(--app-fg-color)";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
<div class="max-md:h-dvh flex flex-col" data-timed-steps-media data-step-duration="5000">
    {{-- Image area with heading, steps, and CTA overlaid --}}
    <div class="h-full relative">
        {{-- Background images (one per step, stacked, toggled by JS) --}}
        <div class="relative w-full h-full md:max-h-dvh md:aspect-[16/9] overflow-hidden">
            @foreach($steps as $index => $step)
                @if(!empty($step['image']))
                    @php
                        $imageId = is_array($step['image']) ? ($step['image']['ID'] ?? $step['image']['id'] ?? null) : $step['image'];
                    @endphp
                    @if($imageId)
                        <div class="absolute inset-0 transition-opacity duration-700 ease-in-out {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}"
                             data-step-image="{{ $index }}">
                            {!! wp_get_attachment_image(
                                $imageId,
                                'full',
                                false,
                                ['class' => 'w-full h-full object-cover object-center', 'loading' => 'lazy']
                            ) !!}
                        </div>
                    @endif
                @endif
            @endforeach

            {{-- Gradient overlays --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent pointer-events-none"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-black/40 to-transparent pointer-events-none hidden md:block"></div>
        </div>

        {{-- Heading (overlaid at top of image) --}}
        <div class="absolute top-0 left-0 right-0 z-1 container-ultrawide pt-sm">
            <h2 class="headline-3 uppercase md:flex! md:justify-between"
                data-word-animate
                data-animate-preset="wordUp"
                data-animate-stagger="0.1">
                {{ $heading }}
            </h2>
        </div>

        {{-- Steps list + CTA panel (overlaid at bottom) --}}
        <div class="max-h-[calc(100%-120px)] md:max-h-full overflow-auto absolute bottom-0 left-0 right-0 container-ultrawide pb-sm md:pb-md z-10" data-steps-list>
            <div class="grid md:grid-cols-12 gap-x-sm">
                {{-- Steps (left) --}}
                <div class="flex flex-col justify-end gap-y-4 md:col-span-5">
                    @foreach($steps as $index => $step)
                        @include('partials.timed-step-item', [
                            'index' => $index,
                            'step' => $step,
                        ])
                    @endforeach
                </div>

                {{-- CTA Panel (right, desktop only) --}}
                <div class="hidden md:flex col-span-4 col-start-9 flex-col items-start gap-y-md self-end" data-cta-panel>
                    @if($cta_heading)
                        <p class="headline-6 uppercase font-bold">{{ $cta_heading }}</p>
                    @endif
                    @if($cta_description)
                        <div class="body-sm text-white" data-rich-text>{!! $cta_description !!}</div>
                    @endif
                    @if($cta_link)
                        @include('components.button-block', [
                            'buttonLink' => $cta_link,
                            'variant' => 'secondary',
                            'size' => 'sm'
                        ])
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- CTA Panel: mobile stacked below --}}
    <div class="md:hidden px-sm py-md" data-cta-panel-mobile>
        <div class="flex flex-col gap-y-md">
            @if($cta_heading)
                <p class="headline-6 uppercase font-bold">{{ $cta_heading }}</p>
            @endif
            @if($cta_description)
                <div class="body-sm text-white/80">{!! $cta_description !!}</div>
            @endif
            @if($cta_link)
                @include('components.button-block', [
                    'buttonLink' => $cta_link,
                    'variant' => 'secondary',
                    'size' => 'sm',
                    'additionalClasses' => 'w-full'
                ])
            @endif
        </div>
    </div>
</div>
@endcomponent
