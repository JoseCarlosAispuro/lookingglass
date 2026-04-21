@php
    $background_color = $background_color ?? 'white';
    $class_name = "py-20 md:py-40 bg-{$background_color} text-(--app-fg-color)";
@endphp 

@component('partials.block', compact('block_name', 'class_name'))
<div class="relative container-ultrawide flex flex-col gap-y-6 md:gap-y-12 md:gap-y-20">
    <div class="grid grid-cols-12 gap-y-6 md:gap-y-30">
        <div class="col-span-12">
            <h2
                data-word-animate
                data-animate-preset="wordUp"
                data-animate-delay="0"
                data-animate-duration="1.5"
                data-animate-stagger="0.15"
                class="headline-2 uppercase font-semibold md:!flex md:!justify-between break-all md:break-normal"
            >
                {{$heading}}
            </h2>
        </div>

        @if($image)
            <div class="col-span-12 md:col-span-5">
                <img src="{{$image['url']}}" alt="{{$image['alt']}}" class="" loading="lazy">
            </div>
        @endif

        <div class="col-span-12 flex flex-col gap-y-6 md:gap-y-20 md:col-span-6 {{ $image ? 'md:col-start-7' : 'md:col-start-4' }}">
            <div class="body-lg flex flex-col gap-y-8" data-rich-text>
                {!! $content !!}
            </div>

            @if($support_text)
                <div class="body-lg pt-4 border-t border-solid border-(--app-fg-color)/40" data-rich-text>
                    {!! $support_text !!}
                </div>
            @endif
        </div>
    </div>
</div>
@endcomponent

@unset($background_color)
@unset($class_name)