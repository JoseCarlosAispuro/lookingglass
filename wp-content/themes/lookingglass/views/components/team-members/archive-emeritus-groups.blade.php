<section id="{{$emeritusSlug}}" class="section flex flex-col gap-y-20 md:gap-y-0" data-bg-color="{{get_palette_color_by_slug($backgroud_color ?? 'gray')}}">
    @foreach($emeritusSections as $section)
        <div class="grid grid-cols-8 md:pt-30 md:pb-4 gap-y-0 md:gap-y-20" data-mobile-accordion>
            <div class="col-span-8 group pt-4 border-t border-solid border-(--app-fg-color)/20 flex justify-between gap-y-4 sticky md:relative top-24 md:top-0 group-data-[nav=hidden]/nav:top-0 bg-(--app-bg-color) z-10 transition-all duration-600 ease-out" data-mobile-accordion-title>
                <p class="headline-3 uppercase font-semibold">{{$section['section_title']}}</p>

                <div class="md:hidden w-min group-data-[expanded=true]:hidden">
                    @include('partials.material-icon', ['name' => 'add', 'class' => 'icon-opsz-48!'])
                </div>
                <div class="md:hidden w-min hidden group-data-[expanded=true]:block">
                    @include('partials.material-icon', ['name' => 'remove', 'class' => 'icon-opsz-48!'])
                </div>
            </div>
            
            @if(!empty($section['members']))
                <div class="col-span-8 md:col-span-7 overflow-hidden transition-all duration-300 ease-in-out" data-mobile-accordion-content>
                    <div class="grid md:grid-cols-2 gap-x-4 pt-12 md:pt-0">
                        @php
                            $hasItems = count($section['members']) > 0;
                            $columns = $hasItems ? array_chunk($section['members'], ceil(count($section['members']) / 2)) : [];
                        @endphp
                        @foreach($columns as $index => $column)
                            <ul class="">
                                @foreach($column as $mIndex => $member)
                                    <li class="py-4 {{($index > 0 && $loop->first) ? 'border-t-0! md:border-t!' : ''}} border-t border-solid border-(--app-fg-color)/20 last-of-type:border-b">
                                        <span class="headline-6 uppercase font-bold">{{$member['full_name']}}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endforeach
</section>