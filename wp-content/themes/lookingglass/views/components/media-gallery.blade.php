@php
    $gallery_items = $galleryItems ?? get_field('gallery_items');
    $background_color = $background_color ?? 'white';

    if(isset($block)) {
        $anchor = $block['anchor'] ?? $block['id'] ?? 'gallery';
    }

    $class_name = "py-xl md:py-40 bg-{$background_color} text-(--app-fg-color)";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
    @if($gallery_items)
        <div>
            <div class="relative" data-media-gallery data-custom-cursor>
                <div class="swiper"
                     data-slides-per-view="1.2"
                     data-slides-per-view-mobile="1"
                     data-space-between-slides="16"
                     data-space-between-slides-mobile="16"
                     data-offset-before="135"
                     data-offset-before-mobile="16"
                     data-offset-after="135"
                     data-offset-after-mobile="16"
                     data-show-custom-cursor="true"
                     data-free-mode="true"
                     data-free-mode-mobile="false"
                     data-breakpoint="1024">
                    <div class="swiper-wrapper" draggable="true">
                        @foreach($gallery_items as $item)
                            @php
                                $media_type = $item['media_type'] ?? 'image';
                                $is_video = $media_type === 'video';
                            @endphp

                            @if($is_video)
                                @php
                                    $video_url = $item['video_url'] ?? '';
                                    $poster = $item['video_poster'] ?? null;
                                    $poster_id = $poster ? ($poster['ID'] ?? ($poster['id'] ?? null)) : null;
                                    $poster_url = $poster['url'] ?? '';
                                    $video_title = $item['video_title'] ?? 'Video';
                                @endphp
                                @if($video_url)
                                <div class="swiper-slide md:!w-auto" data-media-type="video">
                                    <a href="{{ $video_url }}"
                                       data-fancybox="media-gallery-{{ $anchor }}"
                                       @if($poster_url) data-poster="{{ $poster_url }}" @endif
                                       aria-label="Play {{ $video_title }}"
                                       class="relative block overflow-hidden h-[220px] md:h-[520px]">
                                        @if($poster_id)
                                            {!! wp_get_attachment_image($poster_id, 'large', false, [
                                                'class' => 'h-full w-full md:w-auto object-contain md:object-cover',
                                                'loading' => 'lazy',
                                            ]) !!}
                                        @else
                                            <div class="h-full w-[390px] md:w-[920px] bg-black/10 flex items-center justify-center">
                                                <span class="text-(--app-fg-color)/40">@include('partials.icons', ['name' => 'play'])</span>
                                            </div>
                                        @endif
                                        <span class="absolute top-sm left-sm text-(--app-fg-color)">
                                            @include('partials.icons', ['name' => 'play'])
                                        </span>
                                    </a>
                                </div>
                                @endif
                            @else
                                @php
                                    $image = $item['image'] ?? null;
                                    $image_id = $image ? ($image['ID'] ?? ($image['id'] ?? null)) : null;
                                    $image_url = $image['url'] ?? '';
                                    $image_alt = $image['alt'] ?? '';
                                @endphp
                                @if($image_id)
                                    <div class="swiper-slide md:!w-auto" data-media-type="image">
                                        <a href="{{ $image_url }}"
                                           data-fancybox="media-gallery-{{ $anchor }}"
                                           aria-label="{{ $image_alt ?: 'View image' }}"
                                           class="block overflow-hidden h-[220px] md:h-[520px]">
                                            {!! wp_get_attachment_image($image_id, 'large', false, [
                                                'class' => 'h-full w-full md:w-auto object-contain md:object-cover',
                                                'loading' => 'lazy',
                                            ]) !!}
                                        </a>
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>

                    @include('partials.slider-pagination')
                </div>

                @include('partials.custom-cursor', ['text' => 'Drag'])
            </div>
        </div>
    @endif
@endcomponent

@unset($class_name, $gallery_items, $anchor)
