@php
    $heading = get_field('heading');
    $introCopy = get_field('intro_copy');
    $accordionList = get_field('accordion_list');
    $secondaryImageTop = get_field('secondary_image_top');
    $secondaryImageBottom = get_field('secondary_image_bottom');

    $class_name = "relative py-[80px] md:pt-[160px] md:pb-[16px] bg-{$background_color} text-(--app-fg-color)";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
    <div class="container-ultrawide">
        <div class="grid grid-cols-12 gap-0 md:gap-sm">
            @if($secondaryImageTop && $secondaryImageTop['url'])
                <div
                    class="col-span-12 relative md:absolute w-full md:w-[221px] md:aspect-square overflow-hidden top-auto md:top-[16px] right-auto md:right-[16px]">
                    <img class="w-full h-full object-cover object-center" src="{{$secondaryImageTop['url']}}"
                         alt="{{$secondaryImageTop['alt']}}" loading="lazy">
                </div>
            @endif
            <div class="col-span-12 md:col-span-6 col-start-1 md:col-start-4 mt-[46px] md:mt-0">
                @if($heading)
                    <h3
                        data-word-animate
                        data-animate-preset="wordUp"
                        data-animate-delay="0"
                        data-animate-duration="1.5"
                        data-animate-stagger="0.1"
                        class="uppercase headline-2 font-semibold">{{$heading}}</h3>
                @endif
                @if($introCopy)
                    <p class="body-lg font-light mt-[24px] md:mt-[32px]">{{$introCopy}}</p>
                @endif
                @if(isset($accordionList) && count($accordionList) > 0)
                    <div class="mt-[48px] md:mt-[80px] border-t border-black-100">
                        @foreach($accordionList as $accordionItem)
                            <div
                                class="accordion-item border-b border-black-100 transition-all duration-300 ease-out overflow-hidden"
                                data-accordion>
                                <button class="headline-6 font-bold py-sm w-full text-left"
                                        data-action-item>{{$accordionItem['title']}}</button>
                                <p class="body-md pb-sm" data-content>{{$accordionItem['description']}}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            @if($secondaryImageBottom && $secondaryImageBottom['url'])
                <div
                    class="mt-[48px] md:mt-0 col-span-12 relative md:absolute bottom-auto md:bottom-[16px] left-auto md:left-[16px] w-full md:w-[221px] md:aspect-square overflow-hidden">
                    <img class="w-full h-full object-cover object-center" src="{{$secondaryImageBottom['url']}}"
                         alt="{{$secondaryImageBottom['alt']}}" loading="lazy">
                </div>
            @endif
        </div>
    </div>
@endcomponent
