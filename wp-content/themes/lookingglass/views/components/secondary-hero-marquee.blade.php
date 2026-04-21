@php
    $marqueeHeading = get_field('marquee_heading');
    $supportingText = get_field('supporting_text');
    $activeVariant = get_field('active_variant');
    $stickyButtons = get_field('sticky_buttons');
    $supportTextFormat = get_field('support_text_format');

    $imgDesktop = get_field('hero_image_desktop');
    $imgMobile  = get_field('hero_image_mobile');

    if (is_array($imgDesktop)) $imgDesktop = $imgDesktop['ID'] ?? ($imgDesktop['id'] ?? null);
    if (is_array($imgMobile))  $imgMobile  = $imgMobile['ID'] ?? ($imgMobile['id'] ?? null);

    $mobileImageId = $imgMobile ?: $imgDesktop;
    $desktopImageId = $imgDesktop;

    $hasHeading = is_string($marqueeHeading) && trim($marqueeHeading) !== '';

    $background_color = $background_color ?? 'white';

    $class_name = "py-4 w-full bg-{$background_color} text-(--app-fg-color)";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
    <div class="container-ultrawide">
        @if($hasHeading)
            <div class="overflow-hidden whitespace-nowrap" data-marquee data-marquee-speed="100">
                <div class="inline-flex will-change-transform motion-reduce:transform-none motion-reduce:!animate-none"
                     data-marquee-track>
                    {{-- Single text element - JS will clone as needed for seamless infinite scroll --}}
                    <span class="headline-xl font-semibold uppercase shrink-0 pr-[56px]" data-marquee-text>
                        {{ $marqueeHeading }}
                    </span>
                </div>
            </div>
        @endif

        @if($supportingText && $supportTextFormat === 'one_column')
            <div class="mt-[24px] md:mt-[32px] text-center">
                <div class="body-xl text-(--app-fg-color)">
                    {!! $supportingText !!}
                </div>
            </div>
        @endif

        <div class="mt-[24px] md:mt-[80px]">
            @if($desktopImageId)
                <div class="hidden md:block">
                    <div
                        class="relative w-full {{$activeVariant === 'sticky' ? 'aspect-[1408/2000]' : 'overflow-hidden aspect-[16/9]'}}">
                        {!! wp_get_attachment_image(
                            $desktopImageId,
                            'full',
                            false,
                            [
                                'class' => 'absolute inset-0 w-full h-full object-cover object-center'
                            ]
                        ) !!}
                        @if($stickyButtons && count($stickyButtons) > 0)
                            <div class="flex flex-col sticky top-[calc(100%-200px)] left-0">
                                <div class="button-md text-white mb-4 mr-4 flex items-center justify-end">
                                    Scroll
                                    @include('partials.material-icon', ['name' => 'arrow_downward', 'class' => '!icon-opsz-20'])
                                </div>
                                <div class="flex w-full h-[200px] bg-white border border-black">
                                    @foreach($stickyButtons as $button)
                                        @include('components.button-block', [
                                            'buttonLink' => $button['button_link'],
                                            'variant' => $loop->first ? 'primary' : 'secondary',
                                            'ariaLabel' => $button['button_aria_label'],
                                            'additionalClasses' => 'headline-4 uppercase '.(count($stickyButtons) > 1 ? '!border-0 !w-1/2' : '!border-0 !w-full')
                                        ])
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if($mobileImageId)
                <div class="md:hidden">
                    <div class="relative w-full h-[520px] overflow-hidden bg-black/5">
                        {!! wp_get_attachment_image(
                            $mobileImageId,
                            'large',
                            false,
                            [
                                'class' => 'absolute inset-0 w-full h-full object-cover object-center',
                                'sizes' => '100vw',
                            ]
                        ) !!}
                        @if($stickyButtons && count($stickyButtons) > 0)
                            <div
                                class="flex flex-col w-full bg-white border border-black absolute bottom-0">
                                @foreach($stickyButtons as $button)
                                    @include('components.button-block', [
                                        'buttonLink' => $button['button_link'],
                                        'variant' => $loop->first ? 'primary' : 'secondary',
                                        'ariaLabel' => $button['button_aria_label'],
                                        'additionalClasses' => 'headline-4 uppercase !border-0 !w-full !text-[32px] !leading-[1] !tracking-[-0.02em]'
                                    ])
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

            @if($supportingText && $supportTextFormat === 'two_columns')
                <div class="mt-[24px] md:mt-sm">
                    <div  class="body-md text-(--app-fg-color) md:columns-2 space-y-4">
                        {!! $supportingText !!}
                    </div>
                </div>
            @endif
    </div>
@endcomponent
