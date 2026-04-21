@php
    $tag = $ID ? 'a' : 'div';
@endphp

@if($ID)
    <a href="{{get_the_permalink($ID) ?? '#'}}" class="group {{(!empty($fullWidth) && $fullWidth) ? 'w-full' : 'w-[calc(50%-8px)] md:w-[calc(33.33%-11px)]'}} flex flex-col gap-sm" data-team-card>
        <div class="relative bg-black grayscale">
            @if(!empty($imageID))
                {!! wp_get_attachment_image($imageID, 'large', false, [
                    'class' => 'aspect-square object-cover w-full group-hover:contrast-[5] group-hover:brightness-[2] transition-all duration-10 ease-in-out',
                    'sizes' => '(min-width: 768px) 33vw, 50vw',
                    'loading' => 'lazy',
                ]) !!}
            @else
                {!! get_the_post_thumbnail($ID, 'post-thumbnail', ['class' => 'aspect-square object-cover w-full group-hover:contrast-[5] group-hover:brightness-[2] transition-all duration-300 ease-in-out', 'loading' => 'lazy']) !!}
            @endif
            <span
                class="mix-blend-multiply image-overlay opacity-0 group-hover:opacity-50 group-hover:brightness-[1] bg-black-400 absolute top-0 left-0 w-full h-full pointer-events-none transition-all duration-300 ease-in-out"></span>
        </div>
        <span class="flex flex-col gap-2">
            <span class="headline-6 uppercase font-bold">{{$name}}</span>
            @if($position && is_array($positions))
                <span class="body-sm uppercase font-normal text-black-400">{{$position ?? $positions[0]['title']}}</span>
            @endif
        </span>
    </a>
@else
    <div class="group {{(!empty($fullWidth) && $fullWidth) ? 'w-full' : 'w-[calc(50%-8px)] md:w-[calc(33.33%-11px)]'}} flex flex-col gap-sm" data-team-card>
        <span class="flex flex-col gap-2">
            <span class="headline-6 uppercase font-bold">{{$name}}</span>
            @if($position && is_array($positions))
                <span class="body-sm uppercase font-normal text-black-400">{{$position ?? $positions[0]['title']}}</span>
            @endif
        </span>
    </div>
@endif
