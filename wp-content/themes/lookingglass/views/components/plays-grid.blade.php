@php
    $headingLeft = get_field('heading_left');
    $headingRight = get_field('heading_right');
    $exceptionCategory = get_field('exception_category');
    $tagFilter = get_field('tag_filter');
    $plays = get_plays(11, 0, true, $exceptionCategory, $tagFilter);
    $gridPlays = get_plays_grid($plays['posts']);
    $class_name = "relative pb-[80px] md:pb-[160px] bg-{$background_color} text-(--app-fg-color)";
@endphp

@if(count($plays['posts']) > 0)
    @component('partials.block', compact('block_name', 'class_name'))
        <div class="container-ultrawid relative">
            <div class="grid grid-cols-12" data-plays-grid data-category-id="{{$exceptionCategory}}" data-tag-id="{{$tagFilter}}">
                <div class="col-span-10 col-start-2">
                    <div
                        class="sticky top-[24px] md:top-0 left-0 header flex justify-start md:justify-center pt-[80px] md:pt-[160px] overflow-clip">
                        <h2 class="flex gap-[16px] md:gap-[67px] headline-1 font-semibold uppercase transition-opacity motion-reduce:transition-none duration-500 ease-out"
                            data-sticky-heading>
                            <span class="relative left-[-120%]" data-heading-left>{{$headingLeft}}</span>
                            <span class="relative left-[200%] md:left-[120%]"
                                  data-heading-right>{{$headingRight}}</span>
                        </h2>
                    </div>
                    <div class="gap-md md:gap-xl flex flex-col mt-[48px] md:mt-[160px] z-2 relative"
                         data-plays-grid-results>
                        @foreach($gridPlays as $rowIndex => $rowPlays)
                            @include('components.plays-grid-row', [
                                'rowIndex' => $rowIndex,
                                'plays' => $rowPlays,
                                'reversed' => false
                            ])
                        @endforeach
                    </div>
                </div>
                @if($plays['maxPosts'] > 11)
                    <div class="col-span-12 col-start-1 flex justify-center mt-xl">
                        <button
                            class="button-lg py-[18px] px-sm border-1 border-(--app-fg-color) text-(--app-fg-color) w-fit hover:text-black hover:bg-orange hover:border-orange active:text-black active:bg-orange-600 active:border-orange-600"
                            data-load-more-button>Load more
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endcomponent
@endif
