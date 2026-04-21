@php
    $background_color = $background_color ?? 'white';
    
    $class_name = "py-4 md:pt-4 md:pb-40 bg-{$background_color} text-(--app-fg-color)";
@endphp 

@component('partials.block', compact('block_name', 'class_name'))
<div class="relative container-ultrawide flex flex-col gap-y-6 md:gap-y-4" aria-labelledby="section-title">
    <h2 class="sr-only" id="section-title">{{$heading_primary}} {{$heading_secondary ?? ''}}</h2>

    <div class="hidden md:grid grid-cols-12">
        <div class="col-span-12">
            <p class="headline-1 uppercase font-semibold md:whitespace-break-spaces {{$heading_secondary ? '' : 'flex! justify-between!'}}" data-word-animate data-animate-preset="wordUp" data-animate-delay="0" data-animate-duration="1.5" data-animate-stagger="0.1" data-slide-title>{{$heading_primary}}</p>
        </div>
        @if($heading_secondary)
            <div class="col-span-12">
                <p class="headline-1 uppercase font-semibold text-right" data-word-animate data-animate-preset="wordUp" data-animate-delay="0.8" data-animate-duration="1.5" data-animate-stagger="0.1">{{$heading_secondary}}</p>
            </div>
        @endif
    </div>
    
    <div class="grid md:hidden grid-cols-12">
        <div class="col-span-12">
            <p data-word-animate
            data-animate-preset="wordUp"
            data-animate-delay="0"
            data-animate-duration="1.5"
            data-animate-stagger="0.1" class="headline-1 uppercase font-semibold break-all md:break-normal">{{$heading_primary}} {{$heading_secondary ?? ''}}</p>
        </div>
    </div>
    
    <div class="grid grid-cols-12 gap-y-6 md:gap-y-20 pt-6 md:pt-4 border-t border-solid border-(--app-fg-color)/20">
        <div class="col-span-12 md:col-span-8">
            <div class="body-xl heading-strong" data-rich-text>
                {!! $content !!}
            </div>
        </div>
        @if($image && $image['ID'])
            <div class="col-span-12 md:col-span-3 md:col-start-10">
                {!! wp_get_attachment_image($image['ID'], 'medium_large', false, ['class' => 'w-full h-auto', 'sizes' => '(min-width: 768px) 25vw, 100vw']) !!}
            </div>
        @endif
    </div>
</div>
@endcomponent

@unset($background_color)
@unset($class_name)