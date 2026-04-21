@php
    $cards = get_field('cards');
    $background_color = $background_color ?? 'black';
    $text_color = $text_color ?? 'white';

    $class_name = "w-full bg-{$background_color} text-{$text_color}";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
    <div class="container-ultrawide p-0!">
        <div class="relative flex flex-col md:flex-row w-full">
            @foreach($cards as $card)
                <a class="group relative w-full md:w-1/2 aspect-3/2" href="{{$card['link_action']['url']}}" target="{{$card['link_action']['target'] ?? '_self'}}">
                    <img class="absolute inset-0 w-full h-full object-cover z-0" src="{{$card['background_image']['url']}}" alt="{{$card['background_image']['alt']}}" loading="lazy">

                    <div class="absolute top-0 md:top-4 right-0 md:right-4 p-4 opacity-100 md:opacity-0 group-hover:opacity-100 group-hover:translate-x-4 group-hover:-translate-y-4 transition-translate ease-in-out duration-300">
                        @include('partials.material-icon', ['name' => 'arrow_outward', 'class' => "icon-opsz-48! md:icon-opsz-56! text-{$text_color}"])
                    </div>

                    <div class="relative w-full h-full z-1">
                        <div class="flex flex-col justify-between h-full">
                            <p class="headline-5 font-semibold uppercase p-sm">{{$card['title']}}</p>
                            
                            @if($card['description'])
                                <div class="p-sm opacity-100 md:opacity-0 group-hover:opacity-100 w-full h-fit bg-gradient-to-b from-transparent via-{{$background_color}}/0 via-50% to-{{$background_color}} transition-opacity ease-in-out duration-300">
                                    <p class="body-lg p-sm">{{$card['description']}}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endcomponent

@unset($cards)
@unset($class_name)
