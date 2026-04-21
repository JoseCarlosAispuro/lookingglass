<div class="flex md:hidden swiper-navigation justify-between mt-lg">
    <button class="swiper-button-prev relative! w-8! h-8! top-0! left-0! mt-0!">
        @include('partials.material-icon', ['name' => 'chevron_left', 'class' => 'icon-opsz-20 text-(--app-fg-color)'])
    </button>
    
    <div class="swiper-pagination !w-fit text-(--app-fg-color)! relative! top-0! left-0! flex! items-center font-saans button-sm"></div>

    <button class="swiper-button-next !relative !w-[32px] !h-[32px] !top-0 !left-0 !mt-0">
        @include('partials.material-icon', ['name' => 'chevron_right', 'class' => 'icon-opsz-20 text-(--app-fg-color)'])
    </button>
</div>