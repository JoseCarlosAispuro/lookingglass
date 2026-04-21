@php
    $background_color = $background_color ?? 'black';
    
    $class_name = "py-12 md:py-40 bg-{$background_color} text-(--app-fg-color)";
@endphp 

@component('partials.block', compact('block_name', 'class_name'))
<div class="relative container-ultrawide">
    <div class="grid grid-cols-12 gap-y-6 md:gap-y-30">
        <div class="col-span-12">
            <h2 data-word-animate
                data-animate-preset="wordUp"
                data-animate-delay="0"
                data-animate-duration="1.5"
                data-animate-stagger="0.1" 
                class="headline-3 uppercase font-semibold md:flex! md:justify-between!"
            >
                {{$heading}}
            </h2>
        </div>

        <div class="col-span-12 md:col-span-6 md:col-start-4">
            <div class="flex flex-col gap-y-10">
                <div class="relative group col-span-12 md:col-span-5 md:col-start-8 mb-2 md:mb-0">
                    {!! wp_get_attachment_image($image['ID'], 'large', false, [
                        'class' => 'h-full w-full object-cover opacity-100 transition-opacity duration-200 group-hover:opacity-0',
                        'sizes' => '(min-width: 768px) 50vw, 100vw',
                        'loading' => 'lazy',
                    ]) !!}
                    {!! wp_get_attachment_image($image_on_hover['ID'], 'large', false, [
                        'class' => 'absolute inset-0 h-full w-full object-cover opacity-0 transition-opacity duration-300 group-hover:opacity-100',
                        'sizes' => '(min-width: 768px) 50vw, 100vw',
                        'loading' => 'lazy',
                    ]) !!}
                </div>

                @if (!empty($content))
                    <div class="body-md font-regular flex flex-col gap-y-6 text-balance">
                        {!! $content !!}
                    </div>
                @endif
                
                @if(!empty($signature_name))
                    <div class="headline-6 text-balance">
                        {{ $signature_name }}@if(!empty($signature_title)), {{ $signature_title }} @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endcomponent

@unset($background_color)
@unset($class_name)