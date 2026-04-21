<section id="{{$artisticPartnersSlug}}" class="section" data-bg-color="{{get_palette_color_by_slug($backgroud_color ?? 'gray')}}">
    <div class="flex flex-col gap-4 md:pt-30 gap-y-0 md:gap-y-20" data-mobile-accordion>
        <div class="group pt-4 border-t border-solid border-(--app-fg-color)/20 flex justify-between gap-y-4 sticky md:relative top-24 md:top-0 group-data-[nav=hidden]/nav:top-0 bg-(--app-bg-color) z-10 transition-all duration-600 ease-out" data-mobile-accordion-title>
            <p class="headline-3 uppercase font-semibold">{{$artisticPartnersTitle}}</p>

            <div class="md:hidden w-min group-data-[expanded=true]:hidden">
                @include('partials.material-icon', ['name' => 'add', 'class' => 'icon-opsz-48!'])
            </div>
            <div class="md:hidden w-min hidden group-data-[expanded=true]:block">
                @include('partials.material-icon', ['name' => 'remove', 'class' => 'icon-opsz-48!'])
            </div>
        </div>

        <div class="overflow-hidden transition-all duration-300 ease-in-out" data-mobile-accordion-content>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-12 pt-12 md:pt-0">
                @foreach($artisticPartners as $partner)
                    @include('components.team-members.artistic-partners-card', compact('partner'))
                @endforeach
            </div>
        </div>
    </div>
</section>