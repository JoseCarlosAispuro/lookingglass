@php
    $cta_position = $cta_position ?? false;
    $class_name = "overflow-hidden bg-black text-(--app-fg-color) [--app-bg-color:var(--color-black)] [--app-fg-color:var(--color-white)]";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
<div class="relative md:p-4 {{ $background_color === 'white' ? 'outline-white' : 'outline-black' }} outline-solid outline-16 -outline-offset-16 transition-all duration-300 ease-linear" aria-labelledby="section-title" data-ripple-banner
    @if($background_image)
        style="background-image: url('{{ $background_image }}'); background-size: cover; background-position: center;"
    @endif
    >
    <h2 class="sr-only" id="section-title">{{$heading_primary}} {{$heading_secondary}}</h2>

    <div class="container-ultrawide overflow-hidden">
        <div class="relative grid grid-cols-12 auto-rows-min gap-y-10 md:gap-y-20 px-4 md:px-0 py-14 aspect-auto content-center">
            {{-- cta_position true = right, false = left --}}
            <div class="col-span-12 {{$cta_position ? 'md:col-span-6' : 'md:col-span-4'}} md:col-start-2 flex flex-col justify-between md:order-1">
                <div class="headline-3 font-semibold uppercase md:whitespace-pre-line text-(--app-fg-color)">{{$heading_primary}}</div>
            </div>

            <div class="col-span-12 flex flex-col justify-between {{!empty($secondary_link) ? 'mt-10 md:mt-0' : ''}} {{$cta_position ? 'md:order-2 md:col-span-5 md:col-start-2' : 'md:order-3 md:col-span-4 md:col-start-9'}}">
                <div class="headline-3 font-semibold uppercase md:whitespace-pre-line w-full text-(--app-fg-color) text-right md:text-left">{{$heading_secondary}}</div>
            </div>

            <div class="col-span-12 md:col-span-4 flex flex-col self-end {{$cta_position ? 'md:order-3 md:col-start-8' : 'md:order-2 md:col-start-2'}}">
                <div class="flex flex-col md:flex-row items-center gap-4 w-full {{ $cta_position ? 'justify-end' : ''}}">
                    @include('components.button-block', [
                        'buttonLink' => $link,
                        'variant' => 'primary',
                    ])
                    
                    @if(!empty($secondary_link))
                        @include('components.button-block', [
                            'buttonLink' => $secondary_link,
                            'variant' => 'secondary',
                        ])
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endcomponent

@unset($class_name)
