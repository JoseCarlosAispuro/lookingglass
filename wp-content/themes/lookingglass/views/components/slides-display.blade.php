@php
    $heading = $heading ?? '';
    $slides = $slides ?? [];
    $footer_description = $footer_description ?? '';
    $step_duration = $step_duration ?? '5000';

    $background_color = $background_color ?? 'black';

    $class_name = "bg-{$background_color} text-(--app-fg-color) pt-12 md:pt-40 pb-12 md:pb-4";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
<div data-slides-display data-step-duration="{{ $step_duration }}">
    {{-- Heading --}}
    <div class="container-ultrawide">
        <div class="md:grid md:grid-cols-12 md:gap-x-md">
            <div class="md:col-span-12 lg:col-span-10 lg:col-start-2">
                <h2 class="headline-3 uppercase md:flex! md:justify-between"
                    data-word-animate
                    data-animate-preset="wordUp"
                    data-animate-stagger="0.1">
                    {{ $heading }}
                </h2>
            </div>
        </div>
    </div>

    {{-- Bordered section with steps and image --}}
    <div class="container-ultrawide mt-12 md:mt-20">
        <div class="md:grid md:grid-cols-12 md:gap-x-md">
            <div class="md:col-span-10 md:col-start-2 md:border-t md:border-b border-(--app-fg-color) md:py-sm">
                <div class="flex flex-col-reverse md:grid md:grid-cols-10 md:gap-x-md">
                    {{-- Steps (mobile second, desktop left) --}}
                    <div class="md:col-span-5 flex flex-col gap-y-4 max-md:border-t max-md:border-b border-(--app-fg-color) max-md:py-sm">
                        @foreach($slides as $index => $slide)
                            @include('partials.timed-step-item', [
                                'index' => $index,
                                'step' => $slide,
                            ])
                        @endforeach
                    </div>

                    {{-- Image (mobile first, desktop right) --}}
                    <div class="md:col-span-4 md:col-start-7">
                        <div class="relative aspect-square overflow-hidden max-md:mb-sm">
                            @foreach($slides as $index => $slide)
                                @if(!empty($slide['image']))
                                    @php
                                        $imageId = is_array($slide['image']) ? ($slide['image']['ID'] ?? $slide['image']['id'] ?? null) : $slide['image'];
                                    @endphp
                                    @if($imageId)
                                        <div class="absolute inset-0 transition-opacity duration-700 ease-in-out {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}"
                                             data-step-image="{{ $index }}">
                                            {!! wp_get_attachment_image(
                                                $imageId,
                                                'large',
                                                false,
                                                [
                                                    'class' => 'w-full h-full object-cover object-center',
                                                    'loading' => 'lazy',
                                                ]
                                            ) !!}
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer description --}}
    @if($footer_description)
        <div class="container-ultrawide mt-4 md:mt-8">
            <div class="md:grid md:grid-cols-12 md:gap-x-md">
                <div class="md:col-span-10 md:col-start-2">
                    <div class="body-lg text-white" data-rich-text>
                        {!! $footer_description !!}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endcomponent

@unset($heading, $slides, $footer_description, $step_duration, $background_color, $class_name)
