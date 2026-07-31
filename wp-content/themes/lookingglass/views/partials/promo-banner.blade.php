@php
    $promo_active      = (bool) get_field('show_banner', 'option');
    $promo_campaign_id = get_option('promo_banner_campaign_id', 'default');
    $bannerEyebrow     = get_field('banner_eyebrow', 'option');
    $bannerTitle       = get_field('banner_title', 'option');
    $bannerBody        = get_field('banner_body', 'option');
    $bannerButton      = get_field('banner_button', 'option');
    $bannerImage       = get_field('banner_image', 'option');
@endphp

@if($promo_active && !is_singular('plays'))
{{-- Root — full-viewport overlay, hidden until JS removes the invisible class --}}
<div
    class="fixed inset-0 z-[200] flex items-center justify-center p-gutter invisible pointer-events-none"
    data-promo-banner
    data-campaign-id="{{ esc_attr($promo_campaign_id) }}"
    role="dialog"
    aria-modal="true"
    aria-label="{{ esc_attr($bannerTitle) ?: 'Promotion' }}"
    aria-hidden="true"
>
    {{-- Backdrop — style="opacity:0" ensures it starts transparent before motion animates it --}}
    <div
        class="absolute inset-0 bg-black/55"
        data-promo-backdrop
        aria-hidden="true"
        style="opacity: 0"
    ></div>

    {{-- Modal card — style="opacity:0" prevents flash before motion animates it --}}
    <div
        class="relative z-[1] bg-white flex flex-col md:gap-0 gap-[18px] w-full max-w-[480px] max-h-[90svh] overflow-y-auto shadow-2xl md:flex-row md:max-w-[609px] md:min-h-[380px] md:max-h-[80svh] md:overflow-y-visible rounded-[8px] py-md-[18px] px-4 pt-[45px] pb-4"
        data-promo-modal
        style="opacity: 0"
    >
        {{-- Close button --}}
        <button
            class="absolute top-sm right-sm z-[2] button-xs text-black hover:opacity-60 transition-opacity duration-200 ease-out"
            data-promo-close
            type="button"
            aria-label="Close promotion"
        >Close</button>

        {{-- Campaign image --}}
        @if($bannerImage)
        <div class="relative flex-shrink-0 w-full max-h-[200px] overflow-hidden md:w-[55%] md:max-h-none rounded-l-[8px] rounded-r-[8px] rounded-r-md-[0]">
            <img
                class="w-full h-full object-cover block"
                data-promo-image
                src="{{ esc_url($bannerImage['url']) }}"
                alt="{{ esc_attr($bannerImage['alt']) }}"
                loading="eager"
                decoding="async"
            >
            <div class="absolute inset-0 bg-[linear-gradient(108.13deg,rgba(0,0,0,0.6)_43.67%,rgba(0,0,0,0.12)_73.58%)] md:bg-[linear-gradient(13.98deg,rgba(255,146,23,0.1)_29.49%,rgba(0,0,0,0.5)_83.14%)]" aria-hidden="true"></div>
        </div>
        @endif

        {{-- Content --}}
        <div class="flex md:flex-col flex-row flex-1 justify-between md:items-end items-center">

            <div class="flex flex-col w-full md:relative absolute md:top-auto top-[40%] md:translate-y-0 -translate-y-1/2 left-0">
                @if($bannerEyebrow)
                    <p class="font-cambon-condensed text-[32px] leading-[1] tracking-[-0.02em] md:text-black text-white uppercase md:pt-[18px] pt-[30px] md:pl-[18px] pl-3 md:pr-[18px] pr-0 md:pb-[4px] pb-0 md:bg-white bg-transparent z-1 md:ml-[-46px] ml-4">
                        {{ esc_html($bannerEyebrow) }}
                    </p>
                @endif

                @if($bannerTitle)
                    <h2 class="font-cambon-condensed text-[36px] md:text-[64px] leading-[1] tracking-[-0.02em] text-black pl-3 pb-2 md:pr-[10px] pr-3 bg-orange md:w-[calc(100%+46px)] w-fit md:ml-[-46px] ml-4 z-1">
                        {{ esc_html($bannerTitle) }}
                    </h2>
                @endif
            </div>

            @if($bannerBody)
            <p class="md:text-[18px] text-[14px] leading-[1.2] font-saans text-black pl-3 max-w-[218px]">
                {{ esc_html($bannerBody) }}
            </p>
            @endif

            @if(isset($bannerButton) && $bannerButton['url'] && $bannerButton['title'])
                <a
                class="md:text-[20px] text-[14px] leading-[1.2] font-saans font-medium inline-flex items-center justify-center w-fit md:px-md px-[15px] md:py-sm py-3 bg-black text-white hover:bg-orange hover:text-black transition-colors duration-300 ease-out"
                data-promo-cta
                href="{{ esc_url($bannerButton['url']) }}"
                @if($bannerButton['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif
            >{{ esc_html($bannerButton['title']) }}</a>
            @endif

        </div>
    </div>
</div>
@endif
