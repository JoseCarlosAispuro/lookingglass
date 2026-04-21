@php
    $background_color = $background_color ?? 'black';
    $heading_left = $heading_left ?? '';
    $heading_right = $heading_right ?? '';
    $intro_text = $intro_text ?? '';
    $bullets = $bullets ?? [];
    $cta = $cta ?? null;
    $image = $image ?? null;
    $image_id = $image['ID'] ?? ($image['id'] ?? null);

    $class_name = "py-20 md:py-40 bg-{$background_color} text-(--app-fg-color)";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
<div class="container-ultrawide flex flex-col gap-y-12 md:gap-y-20">
    <h2 class="sr-only">{{ $heading_left }} {{ $heading_right }}</h2>

    {{-- Desktop heading --}}
    <div class="hidden lg:grid grid-cols-12 gap-x-md">
        <div class="col-span-5 col-start-2">
            <p data-word-animate
                data-animate-preset="wordUp"
                data-animate-delay="0"
                data-animate-duration="1.5"
                data-animate-stagger="0.1"
                class="headline-1 uppercase font-semibold"
                aria-hidden="true">{{ $heading_left }}</p>
        </div>
        <div class="col-span-4 col-start-8">
            <p data-word-animate
                data-animate-preset="wordUp"
                data-animate-delay="0.4"
                data-animate-duration="1.5"
                data-animate-stagger="0.1"
                class="headline-1 uppercase font-semibold"
                aria-hidden="true">{{ $heading_right }}</p>
        </div>
    </div>

    {{-- Mobile heading --}}
    <div class="lg:hidden md:grid md:grid-cols-12">
        <p data-word-animate
            data-animate-preset="wordUp"
            data-animate-delay="0"
            data-animate-duration="1.5"
            data-animate-stagger="0.1"
            class="headline-1 uppercase font-semibold md:col-span-10 md:col-start-2 text-balance"
            aria-hidden="true">{{ $heading_left }} {{ $heading_right }}</p>
    </div>

    {{-- Content grid --}}
    <div class="grid grid-cols-12 gap-x-md gap-y-12">
        {{-- Left column: body + bullets + CTA --}}
        <div class="col-span-12 md:col-span-5 md:col-start-2 flex flex-col gap-y-10 md:gap-y-20 order-2 md:order-none">
            <p class="body-xl">{{ $intro_text }}</p>

            @if(!empty($bullets))
                <ul class="flex flex-col gap-y-2">
                    @foreach($bullets as $bullet)
                        <li class="body-lg flex items-start gap-x-3">
                            <span class="shrink-0 mt-2 w-2 h-2 rounded-full bg-(--app-fg-color)" aria-hidden="true"></span>
                            {{ $bullet['bullet_text'] }}
                        </li>
                    @endforeach
                </ul>
            @endif

            @if($cta)
                <div>
                    @include('components.button-block', [
                        'buttonLink' => $cta,
                        'variant' => 'primary',
                        'ariaLabel' => $cta['title'] ?? null,
                    ])
                </div>
            @endif
        </div>

        {{-- Image (mobile: appears first, desktop: right column) --}}
        <div class="col-span-12 md:col-span-4 md:col-start-8">
            @if($image_id)
                {!! wp_get_attachment_image($image_id, 'large', false, [
                    'class' => 'w-full h-auto object-cover aspect-[4/5]',
                    'sizes' => '(max-width: 1024px) 100vw, 458px',
                    'loading' => 'lazy',
                ]) !!}
            @endif
        </div>
    </div>
</div>
@endcomponent

@unset($background_color, $heading_left, $heading_right, $intro_text, $bullets, $cta, $image, $image_id, $class_name)
