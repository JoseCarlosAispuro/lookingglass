@php
    $cards = get_field('cards');
    $scrollableHeight = 100 * count($cards);

    $background_color = $background_color ?? 'black';
    $text_color = $text_color ?? 'white';

    $class_name = "bg-{$background_color} text-{$text_color}";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
    <div data-stacking-cards>
        <div class="relative container-ultrawide" style="height: {{$scrollableHeight}}vh">
            <div class="sticky h-[100vh] top-0 w-[calc(100%+48px)] md:w-[calc(100%+32px)] ml-[-24px] md:ml-[-16px]">
                @foreach($cards as $index => $card)
                    <div data-stacking-card class="w-full h-[100vh] absolute top-0 left-0"
                         style="z-index: {{$index}}; transform: translateY({{$index === 0 ? '0' : '100%'}})">
                        <img
                            class="z-0 absolute top-0 left-0 w-full h-full object-cover {{$card['background_image_mobile'] ? 'hidden md:block' : 'block'}}"
                            src="{{$card['background_image']['url']}}" alt="{{$card['background_image']['alt']}}">
                        @if($card['background_image_mobile'])
                            <img class="z-0 block md:hidden absolute top-0 left-0 w-full h-full object-cover"
                                 src="{{$card['background_image_mobile']['url']}}"
                                 alt="{{$card['background_image_mobile']['alt']}}">
                        @endif
                        <span
                            class="z-1 block md:hidden absolute top-0 left-0 w-full h-full {{$card['expanded_mode'] ? 'bg-gradient-to-b from-transparent from-50% to-black' : 'bg-gradient-to-b from-black/50 via-black/25 to-transparent'}}"></span>
                        <span
                            class="z-1 hidden md:block absolute top-0 left-0 md:h-[50%] w-full bg-gradient-to-b from-black to-transparent"></span>
                        <div class="z-2 p-sm relative z-1 flex flex-col h-full md:h-fit md:grid grid-cols-12 gap-sm">
                            <div class="col-span-12 md:col-span-3 col-start-1">
                                <p class="headline-5 text-white">{{$card['title']}}</p>
                            </div>
                            <div class="col-span-12 col-start-1 md:col-start-5 flex-grow-1 flex flex-col gap-y-8 {{$card['expanded_mode'] ? 'md:col-span-7 justify-end' : 'md:col-span-4'}}">
                                @if($card['expanded_mode'])
                                    <div class="body-md font-normal" data-rich-text>{!! $card['expanded_description'] !!}</div>
                                    <div class="flex flex-col gap-y-2">
                                        @if(count($card['feature_list']) > 0)
                                            @php
                                                $chunksNumber = count($card['feature_list']) > 3 ? ceil(count($card['feature_list']) / 2) : ceil(count($card['feature_list']) / 1);
                                                $chunks = array_chunk($card['feature_list'], $chunksNumber);
                                            @endphp
                                            <hr class="text-disabled">
                                            <div class="flex">
                                                @foreach($chunks as $chunk)
                                                    <div class="flex flex-1 flex-col gap-y-2">
                                                        @foreach($chunk as $feature)
                                                            <div class="flex gap-sm">
                                                                <div class="w-6 h-6">
                                                                    {!! wp_get_attachment_image($feature['feature_icon'], 'small', false, [
                                                                        'class' => '',
                                                                        'loading' => 'lazy',
                                                                    ]) !!}
                                                                </div>
                                                                <p class="body-sm font-noemal">{{$feature['feature_label']}}</p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endforeach

                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <p class="body-lg text-white">{{$card['description']}}</p>
                                @endif
                            </div>
                            <div class="col-span-12 md:col-span-2 col-start-1 md:col-start-10 {{$card['expanded_mode'] ? 'hidden' : ''}}">
                                @if(isset($card['link']) && $card['link'])
                                    @include('partials.link', [
                                        'target' => $card['link']['target'],
                                        'url' => $card['link']['url'],
                                        'label' => $card['link']['title']
                                    ])
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endcomponent
