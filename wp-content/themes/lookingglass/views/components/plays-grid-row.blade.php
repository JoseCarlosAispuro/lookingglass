@php
    $isSpacedRow = $reversed ? ($rowIndex % 2 === 0 ? true : '') : ($rowIndex % 2 !== 0 ? true : '')
@endphp

<div class="flex gap-sm flex-wrap md:flex-nowrap {{$isSpacedRow ?  'justify-between flex-col md:flex-row' : ''}}">
    @foreach($plays as $index => $play)
        <div class="w-[calc(50%-8px)] md:w-[19%] {{($isSpacedRow && $index === 1) ? 'self-end md:self-start' : ''}}" data-result-card>
            @include('components.cards.play-card', [
                'playId' => $play->ID,
                'title' => get_the_title($play->ID),
                'url' => get_the_permalink($play->ID),
                'image' => get_the_post_thumbnail_url($play->ID),
                'subHeading' => get_play_dates($play->ID),
                'pastPlaysStyle' => false,
                'noMinHeight' => true,
                'allUppercase' => true
            ])
        </div>
        @if($reversed)
            @if($rowIndex % 2 !== 0 && $index === 1)
                <div class="w-[19%] hidden md:block"></div>
            @endif
        @else
            @if($rowIndex % 2 === 0 && $index === 1)
                <div class="w-[19%] hidden md:block"></div>
            @endif
        @endif
    @endforeach
</div>
