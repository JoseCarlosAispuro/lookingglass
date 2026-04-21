@php
    $background_color = $background_color ?? 'white';
    
    $class_name = "py-20 md:py-40 bg-{$background_color} text-(--app-fg-color)";
@endphp 

@component('partials.block', compact('block_name', 'class_name'))
<div class="relative">
    <div class="relative container-ultrawide grid grid-cols-12 gap-2.5 md:gap-x-5 gap-y-12 md:gap-y-30">
        <h2 class="sr-only" id="section-title">{{$heading}} {{$heading_right}}</h2>

        <div class="hidden md:block col-span-12 md:col-span-3 md:col-start-2">
            <div data-word-animate
            data-animate-preset="wordUp"
            data-animate-delay="0"
            data-animate-duration="1.5"
            data-animate-stagger="0.1" class="headline-3 font-semibold uppercase md:whitespace-pre-line">{{$heading}}</div>
        </div>
        <div class="hidden md:block col-span-5 col-start-7">
            <div data-word-animate
            data-animate-preset="wordUp"
            data-animate-delay="0"
            data-animate-duration="1.5"
            data-animate-stagger="0.1" class="headline-3 font-semibold uppercase md:whitespace-pre-line w-full">{{$heading_right}}</div>
        </div>

        <div class="col-span-12 flex md:hidden">
            <p data-word-animate
            data-animate-preset="wordUp"
            data-animate-delay="0"
            data-animate-duration="1.5"
            data-animate-stagger="0.1" class="headline-3 uppercase font-semibold">{{$heading}} {{$heading_right}}</p>
        </div>
        
        <div class="col-span-12 md:col-span-10 md:col-start-2 flex flex-col gap-y-12 md:gap-y-40">
            <div class="flex flex-col gap-y-4" data-cards-wrapper>
                <div class="bg-(--app-fg-color)/10 rounded-lg p-4 flex flex-col gap-y-4 md:gap-y-22" data-parking-card>
                    <div class="w-full">
                        <p class="headline-4 font-semibold uppercase">{{$places['title']}}</p>
                    </div>

                    <div class="flex flex-col" data-parking-cards-wrapper>
                        @foreach ($places['restaurant_cards'] as $index => $restaurantCard)
                            @include('components.cards.parking-single-card', ['cardTitle' => $restaurantCard['restaurant']->post_title, 'parkingCard' => $restaurantCard, 'index' => $index])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="restaurant-modal" role="dialog" aria-modal="true" aria-hidden="true" data-state="closed" class="group fixed inset-0 z-50 container-ultrawide grid grid-cols-12 opacity-0 pointer-events-none transition-opacity duration-300 ease-fluid data-[state=open]:opacity-100 data-[state=open]:pointer-events-auto" data-modal-instance>
    <div data-modal-close class="fixed inset-0 bg-white/20 transition-opacity duration-400 ease-in opacity-0 group-data-[state=open]:opacity-100 backdrop-blur-xl"></div>
  
    <div role="document" class="col-span-12 md:col-span-10 md:col-start-2 absolute left-4 right-4 md:left-0 md:right-0 bottom-0 z-10 md:w-full h-[80vh] flex flex-col rounded-lg bg-black-100 p-4 shadow-xl transform transition-all duration-300 ease-fluid translate-y-full opacity-0 group-data-[state=open]:translate-y-0 group-data-[state=open]:opacity-100">
        <button type="button" data-modal-close aria-label="Close modal" class="absolute right-4 top-4 text-black focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-black z-10">
            @include('partials.material-icon', ['name' => 'close', 'class' => 'icon-opsz-32!'])
        </button>

        <div class="flex-1 overflow-y-auto no-scrollbar" data-modal-content></div>
    </div>
</div>

@endcomponent

@unset($background_color)
@unset($headingLeft)
@unset($headingRight)
@unset($class_name)