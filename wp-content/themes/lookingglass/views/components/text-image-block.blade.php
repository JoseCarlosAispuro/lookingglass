@php
    $sectionLabel = get_field('text_image_cta_section_label') ?? 'SUPPORT';
    $headingLeft  = get_field('text_image_cta_heading_left') ?? "WHAT'S";
    $headingRight = get_field('text_image_cta_heading_right') ?? 'NEXT';
    $body         = get_field('text_image_cta_body') ?? '';

    $btnLink      = get_field('text_image_cta_button_url') ?? null;

    $image = get_field('text_image_cta_image') ?? null;
    $imageHover = get_field('text_image_cta_image_hover') ?? null;

    if (is_array($image)) {
        $image = $image['ID'] ?? ($image['id'] ?? null);
    }

    $background_color = $background_color ?? 'white';
    $text_color = $text_color ?? 'black';

    $class_name = "bg-{$background_color} text-(--app-fg-color)";
@endphp

@component('partials.block', compact('block_name', 'class_name'))

    <div class="w-full py-20 md:py-40" data-text-image-cta>
        <div class="container-ultrawide">
            <div class="grid grid-cols-1 md:grid-cols-2 sm:gap-[24px] md:gap-[80px] items-start">
                {{-- Left column --}}
                <div>
                    @if($sectionLabel)
                        <p data-word-animate
                           data-animate-preset="wordUp"
                           data-animate-delay="0"
                           data-animate-duration="1.5"
                           data-animate-stagger="0.1"
                           class="headline-1 font-semibold uppercase leading-[0.95]">
                            {{ $sectionLabel }}
                        </p>
                    @endif

                    <div class="md:hidden">
                        @if($headingLeft)
                            <h2 data-word-animate
                                data-animate-preset="wordUp"
                                data-animate-delay="0"
                                data-animate-duration="1.5"
                                data-animate-stagger="0.1"
                                class="headline-1 font-semibold uppercase">{{ $headingLeft }}</h2>
                        @endif
                        @if($headingRight)
                            <h2 data-word-animate
                                data-animate-preset="wordUp"
                                data-animate-delay="0"
                                data-animate-duration="1.5"
                                data-animate-stagger="0.1"
                                class="headline-1 font-semibold uppercase">{{ $headingRight }}</h2>
                        @endif
                        @if($body)
                            <p class="body-lg text-black/80 mt-[24px]">
                                {{ $body }}
                            </p>
                        @endif
                    </div>

                    @if($image)
                        <div class="sm:mt-[24px] md:mt-[40px]">
                            <div class="group relative max-w-[577px] h-[320px] overflow-hidden">
                                {!! wp_get_attachment_image(
                                    $image,
                                    'large',
                                    false,
                                    [
                                        'class' => $imageHover ? 'absolute inset-0 w-full h-full object-cover object-center opacity-100 transition-opacity duration-300 group-hover:opacity-0' : 'absolute inset-0 w-full h-full object-cover object-center opacity-100',
                                        'loading' => 'lazy',
                                    ]
                                ) !!}
                                @if($imageHover)
                                    {!! wp_get_attachment_image(
                                        $imageHover,
                                        'large',
                                        false,
                                        [
                                            'class' => 'absolute inset-0 w-full h-full object-cover object-center opacity-0 transition-opacity duration-300 group-hover:opacity-100',
                                            'loading' => 'lazy',
                                        ]
                                    ) !!}
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Right column --}}
                <div>
                    <div class="flex flex-col leading-[0.95] sm:hidden md:block">
                        @if($headingLeft)
                            <h2 data-word-animate
                                data-animate-preset="wordUp"
                                data-animate-delay="0"
                                data-animate-duration="1.5"
                                data-animate-stagger="0.1"
                                class="headline-1 font-semibold uppercase">{{ $headingLeft }}</h2>
                        @endif
                        @if($headingRight)
                            <h2 data-word-animate
                                data-animate-preset="wordUp"
                                data-animate-delay="0"
                                data-animate-duration="1.5"
                                data-animate-stagger="0.1"
                                class="headline-1 font-semibold uppercase">{{ $headingRight }}</h2>
                        @endif
                    </div>

                    @if($body)
                        <p class="body-lg sm:hidden md:block text-black/80 mt-[60px]">
                            {{ $body }}
                        </p>
                    @endif

                    @if($btnLink)
                        <div class="md:mt-[32px]">
                            @include('components.button-block', [
                                'buttonLink' => $btnLink,
                                'variant' => 'secondary',
                            ])
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endcomponent
