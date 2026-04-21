@php
    $background_color = $background_color ?? 'white';
    $class_name = "relative bg-{$background_color} text-(--app-fg-color)";
@endphp

@component('partials.block', compact('block_name', 'class_name'))

<div class="container-ultrawide py-4 pb-lg md:pb-4 flex flex-col gap-lg md:gap-y-20 justify-center relative">
    {{-- Primary image: in-flow mobile, absolute top-left desktop --}}
    @if(!empty($image_primary))
        <div class="aspect-video overflow-hidden md:w-[32%] lg:w-[27.2%] md:z-10">
            {!! wp_get_attachment_image($image_primary['ID'], 'large', false, [
                'class' => 'w-full h-full object-cover object-center',
                'sizes' => '(min-width: 1440px) 27.2vw, (min-width: 1024px) 32vw, 100vw'
            ]) !!}
        </div>
    @endif

    {{-- Text content --}}
    <div class="md:relative md:z-20">
        {{-- Headings --}}
        <div class="lg:grid lg:grid-cols-12">
            <h1 data-word-animate
                data-animate-preset="wordUp"
                data-animate-delay="0"
                data-animate-duration="1.5"
                class="inline headline-1 uppercase lg:col-span-4 lg:col-start-2">
                {{ $heading_primary }}
            </h1>
            <h1 data-word-animate
                data-animate-preset="wordUp"
                data-animate-delay="0.1"
                data-animate-duration="1.5"
                class="inline headline-1 uppercase lg:col-span-6">
                {{ $heading_secondary }}
            </h1>
        </div>

        {{-- Eyebrow + Description --}}
        <div class="mt-sm md:mt-md md:grid md:grid-cols-12">
            <div class="md:col-span-6 md:col-start-6 md:flex md:flex-col md:gap-md">
                @if(!empty($eyebrow))
                    <p class="hidden md:block headline-6 uppercase">{{ $eyebrow }}</p>
                @endif

                @if(!empty($description))
                    <div class="body-lg">
                        {!! $description !!}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Secondary image: in-flow mobile, absolute bottom-right desktop --}}
    @if(!empty($image_secondary))
        <div class="aspect-video overflow-hidden md:w-[32%] lg:w-[27.2%] md:z-10 md:self-end">
            {!! wp_get_attachment_image($image_secondary['ID'], 'large', false, [
                'class' => 'w-full h-full object-cover object-center',
                'sizes' => '(min-width: 1440px) 27.2vw, (min-width: 1024px) 32vw, 100vw'
            ]) !!}
        </div>
    @endif
</div>

@endcomponent

@unset($background_color)
@unset($class_name)
