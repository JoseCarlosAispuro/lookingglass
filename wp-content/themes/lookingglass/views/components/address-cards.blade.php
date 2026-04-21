@php
    $background_color = $background_color ?? 'white';
    
    $class_name = "py-20 md:py-40 bg-{$background_color} text-(--app-fg-color)";
@endphp 

@component('partials.block', compact('block_name', 'class_name'))
<div class="relative">
    <div class="relative container-ultrawide grid grid-cols-12 gap-2.5 md:gap-x-5 gap-y-12 md:gap-y-30">
        <div class="col-span-12 md:col-span-10 md:col-start-2 flex flex-col gap-y-12 md:gap-y-40">
            <h2
                data-word-animate
                data-animate-preset="wordUp"
                data-animate-delay="0"
                data-animate-duration="1.5"
                data-animate-stagger="0.1"
                class="uppercase headline-1 font-semibold md:!flex md:!justify-between transition-all ease-in-out">{{$heading}}</h2>

            <div class="flex flex-col gap-y-4" data-cards-wrapper>
                @include('components.cards.address-card', ['address_card' => $address_card])

                <div class="bg-(--app-fg-color)/10 rounded-lg p-4 flex flex-col gap-y-4 md:gap-y-22" data-parking-card>
                    <div class="w-full">
                        <p class="headline-4 font-semibold uppercase">{{$parking['title']}}</p>
                    </div>

                    <div class="flex flex-col" data-parking-cards-wrapper>
                        @foreach ($parking['parking_cards'] as $parkingCard)
                            @include('components.cards.parking-single-card', ['parkingCard' => $parkingCard])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endcomponent

@unset($background_color)
@unset($headingLeft)
@unset($headingRight)
@unset($class_name)