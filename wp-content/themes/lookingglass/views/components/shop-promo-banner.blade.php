{{--
    Related TypeScript:
    - assets/typescript/components/inifinite-show-hide.ts (image rotation via [data-infinit-hide-show])
    - assets/typescript/components/custom-cursor.ts (animated "Shop" cursor via [data-custom-cursor])
--}}
@php
    $link = get_field('link');
    $layout = get_field('layouts');
    $textSegmentOne = get_field('text_segment_one');
    $imagesSegmentOne = get_field('images_segment_one');
    $textSegmentTwo = get_field('text_segment_two');
    $imagesSegmentTwo = get_field('images_segment_two');
    $textSegmentThree = get_field('text_segment_three');

    $background_color = $background_color ?? 'black';

    $class_name = "bg-{$background_color} text-(--app-fg-color)";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
<div class="relative py-xl" data-custom-cursor>
    @if(isset($link) && $link)
        <a class="z-1 absolute top-0 left-0 w-full h-full" href="{{$link['url']}}" target="{{$link['target']}}"></a>
        @include('partials.custom-cursor', ['text' => 'Shop'])
    @endif
    <div class="container-ultrawide flex flex-col md:flex-row justify-between items-center gap-md md:gap-0">
        <p class="headline-3 font-medium">{{$textSegmentOne}}</p>
        <div class="pointer-events-none relative w-[191px] md:w-[221px] aspect-square" data-infinit-hide-show>
            @foreach($imagesSegmentOne as $index => $imageOne)
                {!! wp_get_attachment_image($imageOne['ID'] ?? ($imageOne['id'] ?? null), 'medium', false, [
                    'class' => 'pointer-events-none absolute w-full h-full object-cover ' . ($index !== 0 ? 'invisible' : 'visible'),
                    'sizes' => '221px',
                    'loading' => 'lazy',
                ]) !!}
            @endforeach
        </div>
        <p class="headline-3 font-medium">{{$textSegmentTwo}}</p>
        @if($layout === 'two_images')
            <div class="pointer-events-none relative w-[191px] md:w-[221px] aspect-square" data-infinit-hide-show>
                @foreach($imagesSegmentTwo as $index => $imageTwo)
                    {!! wp_get_attachment_image($imageTwo['ID'] ?? ($imageTwo['id'] ?? null), 'medium', false, [
                        'class' => 'pointer-events-none absolute w-full h-full object-cover ' . ($index !== 0 ? 'invisible' : 'visible'),
                        'sizes' => '221px',
                        'loading' => 'lazy',
                    ]) !!}
                @endforeach
            </div>
            <p class="headline-3 font-medium">{{$textSegmentThree}}</p>
        @endif
    </div>
</div>
@endcomponent