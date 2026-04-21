@php
    $background_color = $background_color ?? 'white';
    $iconOnTop = $icon_position === 'top';

    $class_name = "py-20 md:py-40 bg-{$background_color} text-(--app-fg-color)";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
<div class="relative flex flex-col gap-y-12 md:gap-y-20" aria-labelledby="section-title">
    <h2 class="sr-only" id="section-title">{{$heading_left}} {{$heading_right}}</h2>

    <div class="relative container-ultrawide grid grid-cols-12 gap-2.5 md:gap-x-5 gap-y-12 md:gap-y-20">
        <div class="col-span-12 md:col-span-5 md:col-start-2">
            <p class="headline-3 uppercase font-semibold" data-slide-title>{{$heading_left}} <span class="block md:hidden">{{$heading_right}}</span></p>
        </div>
        <div class="hidden md:block col-span-12 md:col-span-5 col-start-5 text-right">
            <p class="headline-3 uppercase font-semibold">{{$heading_right}}</p>
        </div>

        @if($list)
            <div class="col-span-12 md:col-span-10 md:col-start-2 md:py-4 {{$columns == 2 ? 'md:columns-2 gap-4 md:border-y pt-4' : 'grid md:grid-cols-3 gap-8 border-y py-4'}} border-solid border-(--app-fg-color)/30">
                @foreach ($list as $item)
                    <div class="break-inside-avoid flex flex-col {{ $iconOnTop ? 'gap-y-8' : 'md:flex-row mb-8 md:mb-4 last:mb-0 gap-4' }}">
                        <div>
                            @if (str_starts_with($item['icon'], 'custom-'))
                                <div class="{{$iconOnTop ? 'h-6 w-6 p-[2px] md:p-0 md:h-12 md:w-12 flex items-center' : ''}}">
                                    @include('partials.icons', ['name' => str_replace('custom-', '', $item['icon'])])
                                </div>
                            @else
                                @include('partials.material-icon', ['name' => $item['icon'], 'class' => ($iconOnTop ? 'md:icon-opsz-48!' : '' )])
                            @endif
                        </div>
                        <div class="flex flex-col {{$columns == 2 ?: 'gap-y-4'}}">
                            <p class="headline-6 uppercase font-bold">{{ $item['title'] }}</p>
                            <div class="body-lg font-regular flex flex-col gap-y-4 text-balance">
                                {!! $item['description'] !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endcomponent

@unset($background_color)
@unset($headingLeft)
@unset($headingRight)
@unset($class_name)
