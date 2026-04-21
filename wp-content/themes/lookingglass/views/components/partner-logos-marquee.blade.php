@php
    $headingWords = $heading ? preg_split('/\s+/', trim($heading)) : [];
    $supportingText = $supportingText ?? get_field('supporting_text');

    $ctaLink = $cta ?? get_field('primary_cta_url');
    $ctaLabel = $ctaLink ? $ctaLink['title'] : '';
    $ctaUrl = is_array($ctaLink) ? ($ctaLink['url'] ?? '') : ($ctaLink ?: '');
    $ctaTarget = is_array($ctaLink) ? ($ctaLink['target'] ?? '_self') : '_self';
    $hasCta = !empty($ctaUrl) && !empty($ctaLabel);

    $logos = ($logos ?? get_field('logos')) ?: [];

    $mobile_image = $mobile_image ?? null;
    $background_color = $background_color ?? 'black';
    $class_name = "w-full py-20 md:py-40 text-(--app-fg-color) bg-".$background_color;
@endphp

@component('partials.block', compact('block_name', 'class_name'))
    <div class="flex flex-col gap-y-20" data-partner-logos-marquee>
        <div class="container-ultrawide grid grid-cols-12 gap-6 md:gap-4 order-1">
            <div class="col-span-12 md:col-span-5 md:col-start-2">
                @if($heading)
                    <h2 class="headline-3 font-semibold uppercase whitespace-pre-line">{{$heading}}</h2>
                @endif
            </div>
            <div class="col-span-12 md:col-span-5 flex flex-col gap-8">
                @if($supportingText)
                    <p class="body-xl text-(--app-fg-color)/80">
                        {{ $supportingText }}
                    </p>
                @endif
                @if($hasCta)
                    <div class="hidden md:block">
                        <a href="{{ esc_url($ctaUrl) }}"
                            target="{{ esc_attr($ctaTarget) }}"
                            @if($ctaTarget === '_blank') rel="noopener noreferrer" @endif
                            class="inline-flex items-center justify-center px-4 py-4.5
                                border border-(--app-fg-color) text-(--app-fg-color) bg-transparent
                                hover:bg-orange hover:border-orange hover:text-black transition
                                button-lg font-medium">
                            {{ $ctaLabel }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Logo Marquee --}}
        @if(count($logos) > 0)
            <div class="overflow-hidden whitespace-nowrap order-2"
                 data-marquee
                 data-marquee-speed="50">
                <div class="inline-flex items-center will-change-transform motion-reduce:transform-none motion-reduce:!animate-none motion-reduce:flex-wrap motion-reduce:justify-center motion-reduce:gap-[40px]"
                     data-marquee-track>
                    {{-- Logo group - will be cloned by JS for seamless loop --}}
                    <div class="inline-flex items-center gap-15 md:gap-25 pr-15 md:pr-25" data-marquee-text>
                        @foreach($logos as $logo)
                            @php
                                $logoImage = $logo['logo_image'] ?? null;
                                $logoAlt = $logo['logo_alt'] ?? '';

                                if (is_array($logoImage)) {
                                    $logoImage = $logoImage['ID'] ?? ($logoImage['id'] ?? null);
                                }
                            @endphp
                            @if($logoImage)
                                <div class="shrink-0 h-10 md:h-15 flex items-center">
                                    {!! wp_get_attachment_image($logoImage, 'medium', false, [
                                        'class' => 'h-full w-auto max-w-[150px] md:max-w-[200px] object-contain',
                                        'alt' => esc_attr($logoAlt),
                                        'loading' => 'lazy',
                                    ]) !!}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        @if($image || $mobile_image)
            <div class="container-ultrawide order-0 md:order-3 aspect-16/9 md:aspect-auto">
                {!! wp_get_attachment_image($image['ID'] ?? ($image['id'] ?? null), 'full', false, [
                        'class' => 'w-full h-full object-cover'.($mobile_image ? ' hidden md:block' : ''),
                        'alt' => $image['alt'] ?? '',
                        'loading' => 'lazy'
                ]) !!}
                @if($mobile_image)
                    {!! wp_get_attachment_image($mobile_image['ID'] ?? ($mobile_image['id'] ?? null), 'large', false, [
                            'class' => 'md:hidden w-full h-full object-cover',
                            'alt' => $mobile_image['alt'] ?? '',
                            'sizes' => '100vw',
                            'loading' => 'lazy'
                    ]) !!}
                @endif
            </div>
        @endif

        {{-- CTA Button (mobile only) --}}
        @if($hasCta)
            <div class="container-ultrawide grid grid-cols-12 gap-2.5 md:gap-4 order-3 md:hidden">
                <div class="col-span-12">
                    <a href="{{ esc_url($ctaUrl) }}"
                       target="{{ esc_attr($ctaTarget) }}"
                       @if($ctaTarget === '_blank') rel="noopener noreferrer" @endif
                       class="inline-flex items-center justify-center px-4 py-4.5
                              border border-(--app-fg-color) text-(--app-fg-color) bg-transparent
                              hover:bg-orange hover:border-orange hover:text-black transition
                              button-lg font-medium
                              w-full">
                        {{ $ctaLabel }}
                    </a>
                </div>
            </div>
        @endif

    </div>
@endcomponent
