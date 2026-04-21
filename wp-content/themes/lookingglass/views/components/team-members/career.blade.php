@php
    $block_name = 'team-member-career';
    $class_name = 'py-12 md:py-40';
    $anchor = $anchor ?? '';
    $background_color = 'white';

    $career = $career ?? [];
@endphp

@component('partials.block', compact('block_name', 'class_name', 'background_color', 'anchor'))
<div class="relative container-ultrawide">
    <div class="grid grid-cols-12 gap-y-20">
        @foreach($career as $milestone)
            <div class="col-span-12 md:col-span-10 md:col-start-2 flex flex-col gap-y-12 md:gap-y-20">
                <div>
                    <h2 data-word-animate data-animate-preset="wordUp"
                    data-animate-delay="0" data-animate-duration="1.5" data-animate-stagger="0.1" class="headline-3 uppercase md:flex! flex-wrap justify-between!">
                            {{$milestone['section_title']}}
                    </h2>
                </div>
                <div class="flex flex-col bg-(--app-fg-color)/10 p-4 md:pb-20 rounded-lg">
                    @foreach($milestone['items'] as $item)
                        <div class="grid grid-cols-10 gap-y-10 py-4 border-b border-solid border-(--app-fg-color)/20 first-of-type:pt-0 last-of-type:pb-0! last-of-type:border-0!">
                            <div class="col-span-12 md:col-span-3">
                                <p class="display-xs">{{ $item['title'] }}</p>
                            </div>
                            <div class="col-span-12 md:col-span-6 md:col-start-5 {{ count($milestone['items']) === 1 ? 'content-end' : '' }}">
                                <div class="body-sm no-ul-spacing" data-rich-text>{!! $item['content'] !!}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endcomponent