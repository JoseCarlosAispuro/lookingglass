<div class="group bg-(--app-fg-color)/10 rounded-lg" data-address-card-wrapper>
    <div class="grid grid-cols-10 p-4 gap-y-4 md:gap-y-8">
        <div class="col-span-12">
            <p class="flex md:hidden headline-4 font-semibold uppercase">{{$address_card['title']}}</p>
        </div>
        <div class="col-span-12 md:col-span-6 flex flex-col justify-between order-1 md:order-0">
            <p class="hidden md:flex headline-4 font-semibold uppercase">{{$address_card['title']}}</p>
            <div class="py-4 border-y border-solid border-(--app-fg-color)/30 flex flex-col gap-y-4 md:gap-y-8">
                <p class="display-xs">{{$address_card['address_line']}}</p>
                <p class="body-lg font-regular">{{$address_card['support_text']}}</p>
            </div>
        </div>
        <div class="relative col-span-12 md:col-span-3 md:col-start-8 order-0 md:order-1">
            <img class="h-full w-full object-cover opacity-100 transition-opacity duration-300 group-hover:opacity-0" src="{{$address_card['image']['url']}}"
             alt="{{$address_card['image']['alt']}}" loading="lazy">
            <img class="absolute inset-0 h-full w-full object-cover opacity-0 transition-opacity duration-300 group-hover:opacity-100" src="{{$address_card['image_on_hover']['url']}}"
             alt="{{$address_card['image_on_hover']['alt']}}" loading="lazy">
        </div>
        <div class="col-span-10 order-2 [--app-bg-color:var(--color-black-100)]">
            @if($address_card['link'])
                @include('partials.link', [
                    'label' => $address_card['link']['title'],
                    'url' => $address_card['link']['url'],
                    'target' => $address_card['link']['target']
                ])
            @endif
        </div>
    </div>
</div>