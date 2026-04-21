<div class="group grid grid-cols-10 gap-4 py-6 md:py-4 last:pb-0 border-t border-solid border-(--app-fg-color)/30" data-parking-card-single>
    <div class="col-span-10 md:col-span-2">
        <p class="display-xs">{{$cardTitle ?? $parkingCard['parking_name']}}</p>
    </div>
    <div class="relative col-span-10 md:col-span-2 md:col-start-4">
        <img class="h-full w-full object-cover opacity-100 transition-opacity duration-300 group-hover:opacity-0" src="{{$parkingCard['image']['url']}}"
        alt="{{$parkingCard['image']['alt']}}" loading="lazy">
        <img class="absolute inset-0 h-full w-full object-cover opacity-100 md:opacity-0 transition-opacity duration-300 group-hover:opacity-100" src="{{$parkingCard['image_on_hover']['url']}}"
        alt="{{$parkingCard['image_on_hover']['alt']}}" loading="lazy">
    </div>
    <div class="col-span-10 md:col-span-4 md:col-start-7 flex flex-col gap-10 md:gap-8">
        <div class="body-lg sm-list no-icon-link" data-rich-text>
            {!! $parkingCard['description'] !!}
        </div>

        <div class="w-full flex gap-8 [--app-bg-color:var(--color-black-100)]">
            @if($parkingCard['link'])
                @if(is_array($parkingCard['link']))
                    @include('partials.link', [
                        'label' => $parkingCard['link']['title'],
                        'url' => $parkingCard['link']['url'],
                        'target' => $parkingCard['link']['target'],
                        'link_size' => 'md',
                        'containerClass' => 'w-fit!'
                    ])
                @endif
            @endif

            @if(!empty($parkingCard['show_menu']) && $parkingCard['show_menu'])
                @php
                    $restaurant_post_id = is_object($parkingCard['restaurant']) ? $parkingCard['restaurant']->ID : $parkingCard['restaurant'];
                    $acf_fields = get_fields($restaurant_post_id);
                @endphp

                <div class="w-full flex w-fit!">
                    <button
                        class="open-modal inline-flex gap-1"
                        data-modal-id="post-modal"
                        data-modal-target="modal-{{ $index }}"
                    >
                        <span class="animated-underline after:bottom-1 button-md flex font-medium text-nowrap h-full">View menu</span>
                        @include('partials.material-icon', ['name' => 'add', 'class' => 'icon-opsz-20'])
                    </button>
                </div>

                <template id="modal-{{ $index }}">
                    <div class="flex flex-col">
                        <div class="sticky top-0 pb-10 md:pb-22 bg-black-100 border-b border-solid border-border-secondary">
                            <p class="headline-4 uppercase font-semibold">Menu</p>
                        </div>

                        <div class="grid grid-cols-10 gap-x-4 gap-y-10 md:gap-y-0 pt-4 pb-22">
                            <div class="fixed bottom-0 left-0 w-full h-50 pointer-events-none z-100 bg-gradient-to-b from-transparent to-black-100 transition-opacity duration-300 ease-in opacity-100" data-content-scroll-indicator></div>
                            
                            <div class="col-span-10 md:col-span-2 md:sticky md:top-41 self-start h-fit bg-black-100">
                                <div class="">
                                    <p class="display-xs">
                                        {{ $parkingCard['restaurant']->post_title ?? '' }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-span-10 md:col-span-7 md:col-start-4">
                                @if(!empty($acf_fields['menu_content']) && is_array($acf_fields['menu_content']))
                                    @foreach($acf_fields['menu_content'] as $section)
                                        <div class="flex flex-col gap-y-4 md:gap-y-8 py-4 md:py-8 {{$loop->first ? 'pt-0' : ''}} {{(!empty($section['add_separator']) && $section['add_separator']) ? 'border-b border-solid border-border-secondary' : ''}} {{(!empty($section['add_content']) && $section['add_content']) ? '' : 'pb-0!'}}">
                                            <p class="headline-6 uppercase font-bold">{{ $section['title'] }}</p>

                                            @if(!empty($section['add_content']) && $section['add_content'])
                                                <div class="body-lg no-icon-link" data-rich-text>
                                                    {!! $section['content'] !!}
                                                </div>
                                            @endif
                                            
                                            @if(!empty($section['add_link']) && $section['add_link'] && !empty($section['link']))
                                                <div class="[--app-bg-color:var(--color-black-100)]">
                                                    @include('partials.link', [
                                                        'url' => $section['link']['url'],
                                                        'label' => $section['link']['title'],
                                                        'target' => $section['link']['target'],
                                                        'linkSize' => 'lg'
                                                    ])
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div class="body-lg">Menu not available</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </template>
            @endif
        </div>
    </div>
</div>