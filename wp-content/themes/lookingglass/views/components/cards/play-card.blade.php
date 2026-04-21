@php
    $noMinHeight = $noMinHeight ?? false;
    $allUppercase = $allUppercase ?? false;
@endphp
<a href="{{$url}}" target="_self">
    <div class="flex flex-col {{$pastPlaysStyle ? 'gap-[40]' : 'gap-sm'}}">
        <img class="{{$noMinHeight ? '' : '!min-h-[332px]'}} object-cover {{$pastPlaysStyle ? 'aspect-[4/6]' : 'aspect-[0.66]'}}" src="{{$image}}" alt="" loading="lazy">
        <div class="content flex flex-col gap-xs {{$pastPlaysStyle ? 'text-center' : ''}}">
            @if($title)
                <p class="headline-6 font-bold {{$pastPlaysStyle ? 'uppercase' : ''}} {{$allUppercase ? 'uppercase' : ''}}">{{$title}}</p>
            @endif
            @if($subHeading)
                <p class="body-sm font-thin text-black-400 {{$allUppercase ? 'uppercase' : ''}}">{{ str_replace([' - ', ' – '], ' — ', $subHeading) }}</p>
            @endif
        </div>
    </div>
</a>
