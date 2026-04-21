@php
    $headingPrimary = get_field('heading_primary');
    $headingSecondary = get_field('heading_secondary');
    $headingLayout = get_field('heading_layout') ?: 'split';
    $supportingCopy = get_field('supporting_copy');

    $heroImage = get_field('hero_image');
    if (is_array($heroImage)) $heroImage = $heroImage['ID'] ?? ($heroImage['id'] ?? null);

    $hasHeading = is_string($headingPrimary) && trim($headingPrimary) !== '';
    $hasSecondary = is_string($headingSecondary) && trim($headingSecondary) !== '';

    $background_color = 'black';
    $text_color = 'white';

    $class_name = 'bg-{--app-bg-color} text-(--app-fg-color)';
@endphp

@component('partials.block', compact('block_name', 'class_name'))
    <div class="container-ultrawide py-4">
        {{-- Heading --}}
        @if($hasHeading)
            @if($headingLayout === 'split' && $hasSecondary)
                {{-- Below lg: single line --}}
                <h1 class="lg:!hidden headline-3 md:headline-1 uppercase md:w-1/2"
                    data-word-animate
                    data-animate-preset="wordUp"
                    data-animate-delay="0"
                    data-animate-duration="1.5"
                >
                    {{ $headingPrimary }} {{ $headingSecondary }}
                </h1>
                {{-- lg+: split grid --}}
                <div class="hidden lg:grid grid-cols-12" aria-hidden="true">
                    <h1 class="sr-only">{{ $headingPrimary }} {{ $headingSecondary }}</h1>
                    <span class="col-span-4 headline-1 uppercase"
                        data-word-animate
                        data-animate-preset="wordUp"
                        data-animate-delay="0"
                        data-animate-duration="1.5"
                    >
                        {{ $headingPrimary }}
                    </span>
                    <span class="col-span-8 headline-1 uppercase"
                        data-word-animate
                        data-animate-preset="wordUp"
                        data-animate-delay="0.1"
                        data-animate-duration="1.5"
                    >
                        {{ $headingSecondary }}
                    </span>
                </div>
            @elseif($headingLayout === 'word_split')
                <h1 class="headline-1 uppercase md:flex! md:justify-between!"
                    data-word-animate
                    data-animate-preset="wordUp"
                    data-animate-delay="0"
                    data-animate-duration="1.5"
                >
                    {{ $headingPrimary }}@if($hasSecondary) {{ $headingSecondary }}@endif
                </h1>
            @else
                <h1 class="headline-1 uppercase"
                    data-word-animate
                    data-animate-preset="wordUp"
                    data-animate-delay="0"
                    data-animate-duration="1.5"
                >
                    {{ $headingPrimary }}@if($hasSecondary) {{ $headingSecondary }}@endif
                </h1>
            @endif
        @endif

        {{-- Image --}}
        @if($heroImage)
            <div class="relative mt-4 aspect-[3/4] md:aspect-[16/9] overflow-hidden">
                {!! wp_get_attachment_image(
                    $heroImage,
                    'full',
                    false,
                    [
                        'class' => 'absolute inset-0 w-full h-full object-cover object-center'
                    ]
                ) !!}

                {{-- Gradient overlay: mobile full-height, desktop bottom strip --}}
                <div class="absolute inset-0 bg-gradient-to-b from-transparent from-50% to-black md:hidden pointer-events-none"></div>
                <div class="hidden md:block absolute bottom-0 left-0 right-0 h-[160px] bg-gradient-to-t from-black to-transparent pointer-events-none"></div>

                {{-- Supporting copy --}}
                @if($supportingCopy)
                    <div class="absolute bottom-4 right-4 md:bottom-8 md:right-8 border-t border-white pt-4">
                        <p class="headline-6 uppercase text-white max-w-[324px]">
                            {{ $supportingCopy }}
                        </p>
                    </div>
                @endif
            </div>
        @endif
    </div>
@endcomponent
