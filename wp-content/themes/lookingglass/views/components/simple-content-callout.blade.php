@php
    $heading = get_field('heading');
    $image   = get_field('image');
    $hoveredImage   = get_field('hovered_image');
    $body    = get_field('body_text');
    $ctaLink  = get_field('primary_cta_url');

    $ctaUrl   = is_array($ctaLink) ? ($ctaLink['url'] ?? '') : '';
    $ctaTarget = is_array($ctaLink) ? ($ctaLink['target'] ?? '_self') : '_self';
    $ctaLabel = is_array($ctaLink) ? ($ctaLink['title'] ?? '') : '';


    // Normalize image id (ACF return_format is id, but keep safe)
    if (is_array($image)) {
        $image = $image['ID'] ?? ($image['id'] ?? null);
    } 

    $background_color = $background_color ?? 'black';
    $text_color = '(--app-fg-color)';

    $class_name = "w-full py-[80px] md:py-40 bg-{$background_color} text-{$text_color}";
@endphp

@component('partials.block', compact('block_name', 'class_name'))

    <div data-simple-content-callout>
        <div class="container-ultrawide">

            {{-- Heading --}}
            @if(trim($heading) !== '')
                <h2 data-word-animate
                    data-animate-preset="wordUp"
                    data-animate-delay="0"
                    data-animate-duration="1.5"
                    data-animate-stagger="0.1" class="headline-1 uppercase md:flex! md:flex-wrap md:justify-between!">
                    {{$heading}}
                </h2>
            @endif

            {{-- Image (centered, square crop) --}}
            @if(!empty($image))
                <div class="mt-[24px] md:mt-[80px] flex justify-center">
                    <div class="group relative w-[398px] md:w-[458px] aspect-square overflow-hidden">
                        @if($image)
                        {!! wp_get_attachment_image(
                            $image,
                            'medium_large',
                            false,
                            [
                                'class' => $hoveredImage ? 'opacity-100 group-hover:opacity-0 absolute top-0 left-0 w-full h-full object-cover transition-opacity duration-300' : 'opacity-100 absolute top-0 left-0 w-full h-full object-cover',
                                'sizes' => '458px',
                                'loading' => 'lazy'
                            ]
                        ) !!}
                        @endif
                        @if($hoveredImage)
                            {!! wp_get_attachment_image(
                                $hoveredImage,
                                'medium_large',
                                false,
                                [
                                    'class' => 'opacity-0 group-hover:opacity-100 absolute top-0 left-0 w-full h-full object-cover transition-opacity duration-300',
                                    'sizes' => '458px',
                                    'loading' => 'lazy'
                                ]
                            ) !!}
                        @endif
                    </div>
                </div>
            @endif

            {{-- Body text (centered) --}}
            @if(trim((string)$body) !== '')
                <div class="mt-[24px] md:mt-[40px] flex justify-center">
                    <div class="max-w-[760px] text-center">
                        <div class="body-lg" data-rich-text>
                            {!! $body !!}
                        </div>
                    </div>
                </div>
            @endif

            {{-- CTA button (centered) --}}
            @if($ctaLink)
                <div class="mt-[24px] md:mt-[40px] flex justify-center">
                    @include('components.button-block', [
                        'buttonLink' => $ctaLink,
                        'variant' => 'secondary',
                        'icon' => null,
                        'iconPosition' => 'right',
                        'disabled' => false
                    ])
                </div>
            @endif

        </div>
    </div>

@endcomponent
