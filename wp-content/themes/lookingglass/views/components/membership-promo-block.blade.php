@php
    $headingPrimary = get_field('heading_primary');
    $headingWords = $headingPrimary ? preg_split('/\s+/', trim($headingPrimary)) : [];
    $firstWord = $headingWords[0] ?? '';
    $restWords = array_slice($headingWords, 1);

    $bodyText = get_field('body_text');

    $ctaLabel = get_field('primary_cta_label');
    $ctaLink = get_field('primary_cta_url');
    $ctaUrl = is_array($ctaLink) ? ($ctaLink['url'] ?? '') : ($ctaLink ?: '');
    $ctaTarget = is_array($ctaLink) ? ($ctaLink['target'] ?? '_self') : '_self';
    $hasCta = !empty($ctaUrl) && !empty($ctaLabel);

    $featureImage = get_field('feature_image');
    if (is_array($featureImage)) {
        $featureImage = $featureImage['ID'] ?? ($featureImage['id'] ?? null);
    }

    $quotes = get_field('quotes') ?: [];
@endphp

@component('partials.block', compact('block_name', 'class_name'))
    <section class="w-full py-[80px]" data-membership-promo>
        <div class="container-ultrawide gap-y-md md:gap-y-20 flex flex-col">
            <div class="grid grid-cols-12 gap-sm">
                <div class="col-span-12 md:col-span-5 col-start-1 md:col-start-2">
                    @if($firstWord)
                        <h2
                            data-word-animate
                            data-animate-preset="wordUp"
                            data-animate-delay="0"
                            data-animate-duration="1.5"
                            data-animate-stagger="0.1"
                            class="headline-1 uppercase font-medium !hidden md:!block">{{$firstWord}}</h2>
                    @endif
                    @if($headingPrimary)
                        <h2
                            data-word-animate
                            data-animate-preset="wordUp"
                            data-animate-delay="0"
                            data-animate-duration="1.5"
                            data-animate-stagger="0.1"
                            class="headline-1 uppercase font-medium !block md:!hidden">{{$headingPrimary}}</h2>
                    @endif
                </div>
                <div class="col-span-12 md:col-span-4 col-start-1 md:col-start-8 hidden md:block">
                    @if(count($restWords) > 0)
                        <h2
                            data-word-animate
                            data-animate-preset="wordUp"
                            data-animate-delay="0.1"
                            data-animate-duration="1.5"
                            data-animate-stagger="0.1"
                            class="headline-1 uppercase font-medium">
                            @foreach($restWords as $word)
                                <span class="headline-2 font-semibold uppercase leading-[0.95] block">{{ $word }}</span>
                            @endforeach
                        </h2>
                    @endif
                </div>
            </div>
            <div class="grid grid-cols-12 gap-y-md md:gap-sm">
                <div class="col-span-12 md:col-span-5 col-start-1 md:col-start-2 flex flex-col gap-y-8">
                    @if($bodyText)
                        <p class="body-lg">
                            {{ $bodyText }}
                        </p>
                    @endif
                    @if($hasCta)
                        <div class="hidden md:block">
                            @include('components.button-block', [
                                'buttonLink' => $ctaLink,
                                'variant' => 'secondary',
                                'size' => 'lg',
                                'ariaLabel' => $ctaLabel,
                            ])
                        </div>
                    @endif
                </div>
                <div class="col-span-12 md:col-span-4 col-start-1 md:col-start-8 flex flex-col gap-y-md">
                    @if($featureImage)
                        <div class="flex-1">
                            <div class="relative overflow-hidden">
                                {!! wp_get_attachment_image($featureImage, 'large', false, [
                                    'class' => 'w-full object-cover',
                                    'loading' => 'lazy',
                                ]) !!}
                            </div>
                        </div>
                    @endif
                    @if($hasCta)
                        <div class="block md:hidden">
                            @include('components.button-block', [
                                'buttonLink' => $ctaLink,
                                'variant' => 'secondary',
                                'size' => 'lg',
                                'ariaLabel' => $ctaLabel,
                            ])
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Quotes Slider --}}
        @if(count($quotes) > 0)
            <div class="grid grid-cols-12 gap-y-md md:gap-sm mt-[160px] md:mt-[80px]">
                <div class="col-span-12 md:col-span-10 md:col-start-2">
                    <div class="container-ultrawide">
                        <div class="relative {{ count($quotes) > 1 ? 'md:cursor-none' : ''}}" @if(count($quotes) > 1)data-custom-cursor @endif>
                            <div class="swiper"
                                data-slides-per-view="1"
                                data-slides-per-view-mobile="1"
                                data-space-between-slides="0"
                                data-space-between-slides-mobile="0"
                                data-effect="fade"
                                data-free-mode="false"
                                @if(count($quotes) > 1) data-show-custom-cursor="true" @endif
                                >
                                <div class="swiper-wrapper">
                                    @foreach($quotes as $quote)
                                        <div class="swiper-slide">
                                            <blockquote class="text-center">
                                                <p class="font-cambon-condensed display-sm not-italic">
                                                    {{ $quote['quote_text'] }}
                                                </p>
                                                <footer class="mt-[24px] md:mt-[32px]">
                                                    <cite
                                                        class="not-italic headline-6 font-semibold uppercase tracking-wide">
                                                        {{ $quote['quote_author'] }}@if(!empty($quote['quote_author_role']))
                                                            , {{ $quote['quote_author_role'] }}
                                                        @endif
                                                    </cite>
                                                </footer>
                                            </blockquote>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Mobile Navigation --}}
                                @if(count($quotes) > 1)
                                    <div class="mt-9">
                                        @include('partials.slider-pagination')
                                    </div>
                                @endif
                            </div>

                            {{-- Desktop Custom Cursor --}}
                            @if(count($quotes) > 1)
                                @include('partials.custom-cursor', ['text' => 'Drag'])
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endcomponent
