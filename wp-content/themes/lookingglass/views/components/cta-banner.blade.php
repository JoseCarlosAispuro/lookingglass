@php
    $background_color = $background_color ?? 'black';
    $text_color = $background_color === 'black' ? 'white' : 'black';

    $backgroundImage = get_field('background_image');
    $backgroundImageMobile = get_field('background_image_mobile');
    $headingPrimary = get_field('heading_primary');
    $headingSecondary = get_field('heading_secondary');
    $headingLayout = get_field('heading_layout');
    $useMediaLogo = get_field('use_media_logo');
    $metaProps = get_field('meta_props');
    $metaLabel = get_field('meta_label');
    $metaIcon = get_field('meta_icon');

    $buttonProps = [
        'buttonLink' => $link,
        'variant' => 'primary',
    ];

    if($useMediaLogo) {
        $buttonProps['icon'] = 'play';
    }

    $class_name = 'bg-'.$background_color.' text-'.$text_color;
@endphp 

@component('partials.block', compact('block_name', 'class_name'))
<div class="relative bg-black md:p-4" aria-labelledby="section-title">
    <div class="absolute inset-0 z-0">
        {!! wp_get_attachment_image(is_array($backgroundImage) ? ($backgroundImage['ID'] ?? ($backgroundImage['id'] ?? null)) : $backgroundImage, 'full', false, [
                'class' => 'w-full h-full object-cover object-center'.($backgroundImageMobile ? ' hidden md:block' : ''),
                'loading' => 'lazy',
        ]) !!}
        @if($backgroundImageMobile)
            {!! wp_get_attachment_image(is_array($backgroundImageMobile) ? ($backgroundImageMobile['ID'] ?? ($backgroundImageMobile['id'] ?? null)) : $backgroundImageMobile, 'large', false, [
                    'class' => 'w-full h-full object-cover object-center md:hidden',
                    'sizes' => '100vw',
                    'loading' => 'lazy',
            ]) !!}
        @endif
    </div>

    <div class="absolute bottom-0 left-0 w-full h-full md:h-1/2 pointer-events-none z-0 bg-gradient-to-b from-transparent to-black"></div>
    
    @if($headingLayout == 'split')
        <h2 class="sr-only" id="section-title">{{$headingPrimary}} {{$headingSecondary}}</h2>
    @endif

    <div class="relative container-ultrawide overflow-hidden grid grid-cols-12 auto-rows-min py-4 md:py-10 aspect-215/286 md:aspect-auto content-end">
        @if($headingLayout == 'single')
            <div class="col-span-12 md:col-span-5 md:col-start-2 h-fit md:h-auto">
                <h2 class="headline-3 font-semibold uppercase whitespace-pre-line">{{$headingPrimary}}</h2>
            </div>
            <div class="hidden md:block col-span-6"></div>

            <div class="col-span-12 md:col-span-3 md:col-start-9 flex flex-col gap-6 md:gap-8 h-fit md:h-auto">
                <p class="body-lg mt-6 md:mt-0">{{$description}}</p>

                <div>
                    <a 
                        href="{{ $link['url'] }}" 
                        target="{{ $link['target'] ?? '_self' }}" 
                        class="inline-flex items-center justify-center gap-2 h-fit w-full md:w-fit button-lg text-nowrap text-center px-6 py-3 bg-white text-black font-semibold text-lg hover:bg-orange transition"
                    >
                        {{ $link['title'] }}
                    </a>
                </div>
            </div>
        @else 
            <div class="col-span-12 md:col-span-4 md:col-start-2 flex flex-col justify-between {{$description ? '' : 'gap-0 md:gap-48' }}">
                <div class="headline-3 font-semibold uppercase whitespace-pre-line">{{$headingPrimary}}</div>
                
                <div class="block md:hidden headline-3 font-semibold uppercase whitespace-pre-line w-full">{{$headingSecondary}}</div>
                @if($description)
                    <p class="block md:hidden body-lg mt-6 md:mt-0">{{$description}}</p>
                @endif

                <div class="flex items-center gap-6 md:gap-4 mt-6 md:mt-0 {{$description ? 'w-full md:w-65/100' : 'w-full' }}">
                    @include('components.button-block', $buttonProps)

                    @if($metaProps !== 'none')
                        <div class="w-px h-full bg-white"></div>
                    @endif
                    
                    @if($metaProps === 'icon')
                        {!! wp_get_attachment_image(is_array($metaIcon) ? ($metaIcon['ID'] ?? ($metaIcon['id'] ?? null)) : $metaIcon, 'medium', false, [
                                'class' => 'w-auto h-10 md:h-16 object-cover object-center',
                                'sizes' => '64px',
                                'loading' => 'lazy',
                        ]) !!}
                    @elseif($metaProps === 'label')
                        <span class="headline-6 uppercase font-bold">{{$metaLabel}}</span>
                    @endif
                </div>
            </div>

            @if($description)
                <div class="col-span-12 md:col-span-3 md:col-start-9 hidden md:flex flex-col gap-8">
                    <div class="headline-3 font-semibold uppercase whitespace-pre-line w-full">{{$headingSecondary}}</div>
                    <p class="body-lg">{{$description}}</p>
                </div>
            @else
                <div class="col-span-12 md:col-span-6 md:col-start-6 hidden md:flex items-end">
                    <div class="headline-3 font-semibold uppercase whitespace-pre-line w-full">{{$headingSecondary}}</div>
                </div>
            @endif
        @endif
    </div>
</div>
@endcomponent

@unset($class_name)