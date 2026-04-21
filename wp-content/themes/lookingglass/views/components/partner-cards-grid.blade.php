@php
    $heading = get_field('heading');
    $introCopy = get_field('intro_copy');
    $partnerItems = get_field('partner_items');

    $class_name = "relative py-lg md:py-40 bg-{$background_color} text-(--app-fg-color)";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
    <div class="container-ultrawide">
        @if($heading || $introCopy)
            <div class="grid grid-cols-12 gap-sm">
                <div class="col-span-12 md:col-span-4 col-start-1 md:col-start-2">
                    @if(isset($heading) && $heading)
                        <p
                            data-word-animate
                            data-animate-preset="wordUp"
                            data-animate-delay="0"
                            data-animate-duration="1.5"
                            data-animate-stagger="0.1"
                            class="headline-3 font-medium">{{$heading}}</p>
                    @endif
                </div>
                <div class="col-span-12 md:col-span-5 col-start-1 md:col-start-7">
                    @if(isset($introCopy) && $introCopy)
                        <p class="body-xl font-thin">{{$introCopy}}</p>
                    @endif
                </div>
            </div>
        @endif
        @if(isset($partnerItems) && count($partnerItems) > 0 )
            <div class="grid grid-cols-12 gap-sm mt-xl">
                @foreach($partnerItems as $item)
                    <div class="col-span-12 md:col-span-4 min-h-56">
                        @if($item['partner_url'])
                            <a href="{{$item['partner_url']}}"
                               target="_blank"
                               class="relative group h-full w-full border border-white rounded-lg flex flex-col gap-lg md:gap-0 md:flex-row justify-between p-sm hover:bg-orange hover:border-orange transition-all duration-300 ease-out">
                        <span class="relative md:static max-w-full md:max-w-[151px]">
                            @if(isset($item['partner_logo']) && $item['partner_logo'])
                                {!! wp_get_attachment_image($item['partner_logo'], 'medium', false, ['class' => 'flex group-hover:invert', 'sizes' => '151px', 'loading' => 'lazy']) !!}
                            @endif
                            <div class="flex absolute right-0 md:right-sm top-0 md:top-auto md:bottom-3">
                                @include('partials.material-icon', ['name' => 'arrow_outward', 'class' => '!icon-opsz-48 md:!icon-opsz-40 text-white md:text-black opacity-100 md:opacity-0 group-hover:opacity-100 transition-colors duration-300 ease-out'])
                            </div>
                        </span>
                                <span class="group-hover:text-black max-w-full md:max-w-58 flex flex-col gap-sm">
                            @if(isset($item['partner_name']) && $item['partner_name'])
                                        <span
                                            class="headline-6 font-bold transition-colors duration-300 ease-out">{{$item['partner_name']}}</span>
                                    @endif
                                    @if(isset($item['partner_description']) && $item['partner_description'])
                                        <span
                                            class="body-md text-black-400 group-hover:text-black transition-colors duration-300 ease-out">{{$item['partner_description']}}</span>
                                    @endif
                        </span>
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endcomponent
