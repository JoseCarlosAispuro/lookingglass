@php
    $class_name = "relative bg-{$background_color} text-(--app-fg-color) h-dvh min-h-dvh";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
    <div class="relative w-full h-full container-ultrawide flex flex-col justify-between py-sm">
        <div class="absolute top-0 left-0 background-image-container w-full h-full">
            {!! wp_get_attachment_image($background_image['ID'],'full', false, ['class' => 'w-full h-full object-cover object-center', 'loading' => 'lazy']) !!}
        </div>
        <div class="grid grid-cols-12 md:gap-sm z-1">
            @if(!empty($heading))
                <div class="col-span-12 col-start 1">
                    <p
                            data-word-animate
                            data-animate-preset="wordUp"
                            data-animate-delay="0"
                            data-animate-duration="1.5"
                            data-animate-stagger="0.1"
                            class="headline-3 font-medium md:!flex md:!justify-between uppercase">{{$heading}}</p>
                </div>
            @endif
        </div>
        <div class="grid grid-cols-12 md:gap-sm z-1">
            @if(!empty($body_left))
                <div class="col-span-12 md:col-span-5 col-start-1">
                    <div class="body-xl" data-rich-text>{!! $body_left !!}</div>
                </div>
            @endif
            @if(!empty($body_right))
                <div class="col-span-12 md:col-span-5 col-start-1 md:col-start-8 flex items-end mt-sm md:mt-0">
                    <div class="body-md pt-sm border-t border-(--app-fg-color)" data-rich-text>{!! $body_right !!}</div>
                </div>
            @endif
        </div>
    </div>
@endcomponent
