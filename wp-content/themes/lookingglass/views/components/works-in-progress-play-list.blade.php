@php
    $background_color = $background_color ?? 'black';
    $heading = $heading ?? '';
    $list = $list ?? [];
    $hasItems = count($list) > 0;
    if($hasItems) {
        usort($list, fn($a, $b) => $b['year_of_development'] <=> $a['year_of_development']);
    }
    $columns = $hasItems ? array_chunk($list, ceil(count($list) / 2)) : [];
    
    $class_name = "py-12 md:py-40 bg-{$background_color} text-(--app-fg-color)";
@endphp 

@component('partials.block', compact('block_name', 'class_name'))
<div class="relative flex flex-col gap-y-12 md:gap-y-20">
    <div class="relative container-ultrawide grid grid-cols-12 gap-2.5 md:gap-x-5 gap-y-12 md:gap-y-20">
        <div class="col-span-12 md:col-span-10 md:col-start-2">
            <h2 data-word-animate
                data-animate-preset="wordUp"
                data-animate-delay="0"
                data-animate-duration="1.5"
                data-animate-stagger="0.1" 
                class="headline-3 uppercase font-semibold md:flex! md:justify-between!"
            >
                {{$heading}}
            </h2>
        </div>

        @if(count($columns) > 0)
            <div class="col-span-12 md:col-span-10 md:col-start-2 grid md:grid-cols-2 border-y border-solid border-(--app-fg-color) md:border-(--app-fg-color)/60 md:pb-4 gap-x-4">
                @foreach($columns as $column)
                    <ul>
                        @foreach ($column as $item)
                            <li class="break-inside-avoid flex flex-row gap-4 py-4 border-b last-of-type:md:border-0 border-solid border-(--app-fg-color) md:border-(--app-fg-color)/30">
                                <div>
                                    <span class="headline-6 uppercase">{{ $item['year_of_development'] }}</span>
                                </div>
                                <div class="flex flex-col gap-y-2">
                                    <p class="headline-6 uppercase font-bold">{{ $item['title'] }}</p>
                                    <div class="body-lg inline-flex flex-wrap gap-2">
                                        by 
                                        
                                        @if(!empty($item['authors']) && is_array($item['authors']))
                                            @php $authorsCount = count($item['authors']); @endphp

                                            @foreach($item['authors'] as $author)
                                                {{ ($authorsCount > 1 && $loop->last) ? ' and ' : ($loop->first ? '' : ', ') }}

                                                @include('partials.link', [
                                                    'label' => $author->post_title,
                                                    'url' => get_permalink($author->ID),
                                                    'target' => "_self",
                                                    'link_size' => 'lg',
                                                    'containerClass' => 'w-fit! whitespace-nowrap'
                                                ])
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endcomponent

@unset($background_color)
@unset($list)
@unset($columns)
@unset($class_name)
