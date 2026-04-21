@php
    $background_color = $background_color ?? 'white';
    $class_name = "py-12 md:pt-40 md:pb-4 bg-{$background_color} text-(--app-fg-color)";

    $image_id = $image['ID'] ?? ($image['id'] ?? null);
    $hover_image_id = $image_on_hover['ID'] ?? ($image_on_hover['id'] ?? null);
@endphp

@component('partials.block', compact('block_name', 'class_name'))
<div class="container-ultrawide">
    <div class="grid md:grid-cols-12 gap-y-md gap-x-sm">
        <div class="grid md:grid-cols-subgrid gap-x-sm md:col-span-10 md:col-start-2">
            {{-- Heading: spans left column on desktop, first on mobile --}}
            <div class="md:col-span-4 flex flex-col gap-y-md md:gap-y-20">
                <h2 data-word-animate
                    data-animate-preset="wordUp"
                    data-animate-delay="0"
                    data-animate-duration="1.5"
                    data-animate-stagger="0.1"
                    class="headline-4 font-semibold uppercase">{{ $heading }}</h2>

                {{-- Rich text: below heading on desktop, below image on mobile --}}
                <div class="hidden md:block">
                    <div data-rich-text class="body-lg md-list heading-strong">
                        {!! $description !!}
                    </div>
                </div>
            </div>

            {{-- Image with hover swap --}}
            <div class="relative group max-md:mt-6 md:col-span-5 md:col-start-6">
                @if($image_id)
                    {!! wp_get_attachment_image($image_id, 'large', false, [
                        'class' => 'w-full aspect-square object-cover opacity-100 transition-opacity duration-200 group-hover:opacity-0',
                        'sizes' => '(min-width: 768px) 42vw, 100vw',
                        'loading' => 'lazy',
                    ]) !!}
                @endif
                @if($hover_image_id)
                    {!! wp_get_attachment_image($hover_image_id, 'large', false, [
                        'class' => 'absolute inset-0 h-full aspect-square object-cover opacity-0 transition-opacity duration-300 group-hover:opacity-100',
                        'sizes' => '(min-width: 768px) 42vw, 100vw',
                        'loading' => 'lazy',
                    ]) !!}
                @endif
            </div>

            {{-- Rich text on mobile: appears after image --}}
            <div class="md:hidden mt-6">
                <div data-rich-text class="body-lg md-list heading-strong">
                    {!! $description !!}
                </div>
            </div>
        </div>
        </div>
</div>
@endcomponent

@unset($background_color, $class_name, $image_id, $hover_image_id)
