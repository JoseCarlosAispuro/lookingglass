@php
    $backgroundVideo = get_field('background_video');
    $backgroundVideoMobile = get_field('background_video_mobile');
    $videoPosterImage = get_field('video_poster_image');
    $videoPosterImageMobile = get_field('video_poster_image_mobile');
    $slides = get_field('slides');
@endphp

@component('partials.block', compact('block_name', 'class_name'))
    <div class="relative bg-black">
        <div class="h-dvh max-md:max-h-[855px] container-ultrawide overflow-hidden">
            <div class="w-full h-full absolute top-0 left-0 overflow-hidden">
                <span class="w-full h-full absolute top-0 left-0 z-1"></span>
                <div class="absolute top-0 left-0 w-full h-full">
                    <video class="hidden md:block object-cover w-full h-full"
                           data-hero-video="desktop"
                           data-src="{{$backgroundVideo['url']}}"
                           playsinline muted autoplay loop
                           poster="{{$videoPosterImage['url']}}"></video>
                    <video class="block md:hidden object-cover w-full h-full"
                           data-hero-video="mobile"
                           data-src="{{$backgroundVideoMobile['url']}}"
                           playsinline muted autoplay loop
                           poster="{{$videoPosterImageMobile['url']}}"></video>
                </div>
            </div>
            <div class="relative z-1 h-full">
                <div class="hidden md:flex gap-xs pb-[16px] flex-col justify-end h-full">
                    @foreach($slides as $slide)
                        <a class="w-fit relative display-md font-light text-white opacity-65 hover:opacity-100 transition-opacity duration-300 ease-in-out"
                           href="{{$slide['link']['url']}}" target="{{$slide['link']['target']}}">
                            {{$slide['title']}}
                            <span
                                class="button-md absolute top-0 left-full whitespace-nowrap">{{$slide['status_label']}}</span>
                        </a>
                    @endforeach
                </div>
                <div class="swiper h-full flex !pb-[90px]"
                     data-slides-per-view="1"
                     data-slides-per-view-mobile="1"
                     data-space-between-slides="24"
                     data-space-between-slides-mobile="16"
                     data-show-custom-cursor="false"
                     data-handle-drag="false"
                     data-autoplay-delay="5000"
                     data-free-mode="false"
                     data-effect="fade">
                    <div class="swiper-wrapper">
                        @foreach($slides as $index => $slide)
                            <div class="swiper-slide !flex items-end @if($index > 0) opacity-0 @endif">
                                <a class="relative flex flex-col w-3/4 gap-sm" href="{{$slide['link']['url']}}"
                                   target="{{$slide['link']['target'] ?? '_self'}}">
                                    <span class="button-sm text-white font-medium">{{$slide['status_label']}}</span>
                                    <span class="relative display-md text-white font-light">
                                    {{$slide['title']}}
                                    <span
                                        class="absolute inline-flex ml-3">@include('partials.material-icon', ['name' => 'arrow_outward', 'class' => 'icon-opsz-32!'])</span></span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div
                        class="swiper-pagination !w-fit !h-fit !text-white !flex items-center button-sm !absolute !left-auto !right-0 !bottom-[90px]"></div>
                    @include('partials.progress-bar')
                </div>
            </div>
        </div>
    </div>
@endcomponent
