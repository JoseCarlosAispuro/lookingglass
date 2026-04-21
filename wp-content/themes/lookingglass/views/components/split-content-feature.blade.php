@php
    $background_color = $background_color ?? 'white';
    $class_name = "relative bg-{$background_color} text-(--app-fg-color)";
@endphp

@component('partials.block', compact('block_name', 'class_name'))

<div class="container-ultrawide py-lg md:py-[160px] flex flex-col gap-lg md:relative">
    {{-- Image A: in-flow mobile (square), absolute top-right desktop (4:5) --}}
    @if(!empty($image_a))
        <div class="aspect-square md:aspect-[4/5] overflow-hidden md:absolute md:top-sm md:right-[var(--gutter-size)] md:w-[22%] lg:w-[15.3%] md:z-10">
            {!! wp_get_attachment_image($image_a['ID'], 'medium_large', false, [
                'class' => 'w-full h-full object-cover object-center',
                'sizes' => '(min-width: 1024px) 15vw, (min-width: 768px) 22vw, 100vw',
                'loading' => 'lazy',
            ]) !!}
        </div>
    @endif

    {{-- Centered content --}}
    <div class="md:grid md:grid-cols-12">
        <div class="md:col-span-6 md:col-start-4 flex flex-col gap-sm md:gap-8">
            @if(!empty($heading))
                <h2 data-word-animate
                    data-animate-preset="wordUp"
                    data-animate-delay="0"
                    data-animate-duration="1.5"
                    class="headline-3 uppercase md:max-w-min">
                    {{ $heading }}
                </h2>
            @endif

            @if(!empty($description))
                <div class="body-lg">
                    {!! $description !!}
                </div>
            @endif
        </div>
    </div>

    {{-- Image B: in-flow mobile (square), absolute bottom-left desktop (4:5) --}}
    @if(!empty($image_b))
        <div class="aspect-square md:aspect-[4/5] overflow-hidden md:absolute md:bottom-sm md:left-[var(--gutter-size)] md:w-[22%] lg:w-[15.3%] md:z-10">
            {!! wp_get_attachment_image($image_b['ID'], 'medium_large', false, [
                'class' => 'w-full h-full object-cover object-center',
                'sizes' => '(min-width: 1024px) 15vw, (min-width: 768px) 22vw, 100vw',
                'loading' => 'lazy',
            ]) !!}
        </div>
    @endif
</div>

@endcomponent

@unset($background_color)
@unset($class_name)
