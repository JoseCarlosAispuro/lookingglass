@php
    $background_color = $background_color ?? 'white';
    $image_position = $image_position ?? 'right';
    $image_id = $image['ID'] ?? $image['id'] ?? null;
    $class_name = "py-20 md:py-40 bg-{$background_color} text-(--app-fg-color)";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
<div class="relative container-ultrawide flex flex-col gap-y-6 md:gap-y-20">
    {{-- Heading --}}
    <div class="col-span-12">
        <h2
            data-word-animate
            data-animate-preset="wordUp"
            data-animate-delay="0"
            data-animate-duration="1.5"
            data-animate-stagger="0.15"
            class="headline-3 uppercase font-semibold md:!flex md:!justify-between"
        >
            {{ $heading }}
        </h2>
    </div>

    {{-- Two-column layout --}}
    <div class="grid grid-cols-12 gap-y-8 md:gap-x-10">
        {{-- Image column --}}
        @if($image_id)
            <div class="col-span-12 md:col-span-5 {{ $image_position === 'left' ? 'md:order-first' : 'md:order-last md:col-start-7' }} md:sticky md:top-20 md:self-start">
                {!! wp_get_attachment_image($image_id, 'large', false, [
                    'class' => 'w-full h-auto',
                    'sizes' => '(min-width: 1024px) 40vw, 100vw',
                    'loading' => 'lazy',
                ]) !!}
            </div>
        @endif

        {{-- Text column --}}
        <div class="col-span-12 md:col-span-4 md:col-start-2 {{ $image_position === 'left' ? 'md:col-start-8' : '' }} flex flex-col gap-y-10 md:gap-y-20">
            {{-- Intro text --}}
            @if($intro_text)
                <div class="body-xl" data-rich-text>
                    {!! $intro_text !!}
                </div>
            @endif

            {{-- Editorial sections --}}
            @if(!empty($sections))
                @foreach($sections as $section)
                    <div class="flex flex-col gap-y-4 md:gap-y-8">
                        @if(!empty($section['section_title']))
                            <h3 class="headline-6 uppercase font-bold">
                                {{ $section['section_title'] }}
                            </h3>
                        @endif

                        @if(!empty($section['section_body']))
                            <div class="body-md" data-rich-text>
                                {!! $section['section_body'] !!}
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endcomponent

@unset($background_color)
@unset($class_name)
@unset($image_position)
@unset($image_id)
