@php
    $block_name = 'team-member-productions';
    $anchor = $anchor ?? 'productions';
    $background_color = 'black';
    
    $slug = get_post_field('post_name');
    $postsPerPage = 15;
    
    $class_name = 'py-12 md:pt-4 md:pb-40';

    $lookingglassProductions = $lookingglassProductions ?? [];
    $playsResult = get_plays_by_ID($postsPerPage, 0, $lookingglassProductions);

    $plays = isset($playsResult['posts']) ? $playsResult['posts'] : [];
    $maxPosts = isset($playsResult['maxPosts']) ? $playsResult['maxPosts'] : 0;
    $playsCount = count($plays);
@endphp

@component('partials.block', compact('block_name', 'class_name', 'background_color', 'anchor'))
<div class="relative container-ultrawide">
    <div class="grid grid-cols-12 pb-4 gap-y-12">
        <div class="col-span-12 md:col-span-5 md:col-start-2">
            <h2 data-word-animate data-animate-preset="wordUp"
            data-animate-delay="0" data-animate-duration="1.5" data-animate-stagger="0.1" class="headline-3 uppercase sticky top-40">With Lookingglass</h2>
        </div>
        <div class="relative col-span-12 md:col-span-4 md:col-start-8 flex flex-col gap-y-20" data-plays-by-member data-team-member="{{$slug}}" data-plays-per-page="{{$postsPerPage}}">
            @if($plays)
                <ul data-plays-results>
                    @foreach($plays as $play)
                        @include('components.team-members.play-list-item', compact('play'))
                    @endforeach
                </ul>

                @if($maxPosts > $playsCount)
                    <button class="button-lg py-4.5 px-sm w-full md:w-fit border border-(--app-fg-color) text-(--app-fg-color) hover:text-black hover:bg-orange hover:border-orange active:text-black active:bg-orange-600 active:border-orange-600 transition-all ease-in-out duration-300" data-load-more-button>
                        Load more
                    </button>
                @endif
            @endif
        </div>
    </div>
</div>
@endcomponent