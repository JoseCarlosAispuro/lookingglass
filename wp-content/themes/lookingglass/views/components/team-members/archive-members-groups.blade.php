<section class="section section flex flex-col gap-y-20 md:gap-y-0" data-bg-color="{{get_palette_color_by_slug($backgroud_color ?? 'white')}}">
    @foreach($memberGroups as $memberGroup)
        @php 
            $teamMembers = get_members_by_taxonomy($memberGroup->slug);
        @endphp
        
        <div id="{{$memberGroup->slug}}" class="grid grid-cols-8 auto-rows-[minmax(0,auto)] pt-0 md:pt-30 pb-0 md:pb-4 gap-y-0 md:gap-y-20 {{$loop->first ? 'pt-0! scroll-mt-40' : ''}}" data-mobile-accordion>
            <div class="col-span-8 group pt-4 border-t border-solid border-(--app-fg-color)/20 flex justify-between gap-y-4 sticky md:relative top-24 md:top-0 group-data-[nav=hidden]/nav:top-0 bg-(--app-bg-color) z-10 transition-all duration-600 ease-out" data-mobile-accordion-title>
                <p class="headline-3 uppercase font-semibold">{{$memberGroup->name}}</p>
                <div class="md:hidden w-min group-data-[expanded=true]:hidden">
                    @include('partials.material-icon', ['name' => 'add', 'class' => 'icon-opsz-48!'])
                </div>
                <div class="md:hidden w-min hidden group-data-[expanded=true]:block">
                    @include('partials.material-icon', ['name' => 'remove', 'class' => 'icon-opsz-48!'])
                </div>
            </div>

            @if(!empty($teamMembers))
                <div class="col-span-8 overflow-hidden transition-all duration-300 ease-in-out" data-mobile-accordion-content>
                    <div class="grid grid-cols-8 gap-x-4 gap-y-12 md:gap-y-20 pt-12 md:pt-0">
                        @foreach($teamMembers as $teamMember)
                            @php
                                $ID = $teamMember->ID;
                                $name = get_field('first_name', $teamMember->ID).' '.get_field('last_name', $teamMember->ID);
                                $positions = get_field('roles', $teamMember->ID);
                                $imageID = get_field('headshot_image', $teamMember->ID);
                                $fullWidth = true;
                            @endphp
    
                            <div class="col-span-4 md:col-span-2">    
                                @include('components.team-member-card', compact('ID','name','positions', 'imageID', 'fullWidth'))
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endforeach
</section>