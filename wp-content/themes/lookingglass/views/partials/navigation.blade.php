<nav class="main-navigation fixed w-full z-100 transition-colors duration-600 ease-out border-b border-border-secondary" aria-label="Main Navigation" aria-expanded="false" data-component-id="main-navigation">
    <div class="container-ultrawide h-fit gap-sm py-sm">
        <div class="grid grid-cols-12">
            <div class="hidden md:block col-span-1">
                <button class="hover:bg-orange hover:text-black text-[16px] font-saans py-[14.5px] px-[16px] text-inherit font-medium flex h-fit transition-colors duration-300 ease-out" data-trigger-button>Menu</button>
            </div>
            <div class="relative col-span-6 md:col-span-2 col-start-1 md:col-start-6 flex justify-start md:justify-center">
                <a href="/" class="relative flex h-[64px] md:h-[80px]">
                    @include('partials.icons', ['name' => 'logo-white', 'classes' => 'h-full w-auto main-logo'])
                </a>
            </div>
            <div class="col-span-6 md:col-span-2 col-start-7 md:col-start-11 flex justify-end gap-xs">
                {!! get_nav_menu_by_location(NAV_MENUS_SUPPORT, ['menu_class' => 'flex flex-row-reverse gap-xs main-links body']) !!}
                <button class="flex md:hidden h-fit p-[12px]" data-trigger-button>@include('partials.material-icon', ['name' => 'menu', 'class' => 'icon-opsz-24'])</button>
            </div>
        </div>
    </div>
</nav>
<div class="fixed top-0 left-0 w-full h-full bg-orange z-100 py-sm -translate-y-full transition-transform duration-800 ease-fluid" data-navigation-modal>
    <div class="container-ultrawide h-full gap-sm">
        <div class="flex flex-col md:grid grid-cols-12 h-full">
            <div class="col-span-12 md:col-span-3 col-start-1 flex md:flex-col justify-end relative md:h-full">
                <button class="hover:bg-black hover:!text-orange !text-black text-[16px] font-medium font-saans py-[14.5px] px-[16px] text-inherit flex h-fit w-fit transition-colors duration-300 ease-out" data-trigger-button>Close</button>
                <div class="hidden md:block relative h-full">
                    <img class="absolute w-full h-auto bottom-0 hidden"
                         alt=""
                         data-navigation-gallery-element>
                </div>
            </div>
            <div class="h-full col-span-12 md:col-span-8 col-start-1 md:col-start-5 grid grid-cols-12 mt-sm md:mt-0 overflow-y-auto" data-menus-section>
                <div class="col-span-12 md:col-span-7 col-start-1 flex flex-col justify-between md:gap-y-20">
                    {!! get_nav_menu_by_location(NAV_MENUS_MAIN, ['menu_class' => 'flex flex-col gap-sm main-links body']) !!}
                </div>
                <div class="hidden md:block col-span-5">
                    <div class="flex flex-col gap-xs" data-navigation-modal-subitems></div>
                </div>
            </div>
        </div>
    </div>
</div>
