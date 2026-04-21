@php
    $background_color = $background_color ?? 'black';
    
    $class_name = "py-20 md:py-40 bg-{$background_color} text-(--app-fg-color)";
@endphp 

@component('partials.block', compact('block_name', 'class_name'))
<div class="relative container-ultrawide">
    <div class="grid grid-cols-12 gap-y-6 md:gap-y-30">
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

        <div class="col-span-12 md:col-span-10 md:col-start-2">
            <div class="rounded-lg bg-(--app-fg-color)/10 p-4">
                <div class="grid grid-cols-10 gap-y-4">
                    <div class="col-span-10 md:col-span-7">
                        <p class="headline-4 font-semibold uppercase">{{$program['card_title']}}</p>
                    </div>
        
                    @if(isset($program['image']) && !empty($program['image']['ID']))
                        <div class="col-span-10 md:col-span-2 md:col-start-9 mb-4">
                            {!! wp_get_attachment_image($program['image']['ID'], 'medium', false, ['class' => 'object-cover aspect-square w-full', 'sizes' => '(min-width: 768px) 20vw, 100vw', 'loading' => 'lazy']) !!}
                        </div>
                    @endif
                </div>
                <div class="py-4 border-y border-solid border-black/20 flex flex-col gap-y-4 md:gap-y-14">
                    <div class="body-xl">{!!$program['description']!!}</div>
                    
                    <div class="flex flex-col gap-4">
                        <div class="body-lg font-semibold">
                            {{ $program['emphasis_line'] }}
                        </div>

                        <div class="w-full md:w-fit">
                            @include('components.button-block', [
                                'buttonLink' => $program['button_link'],
                                'variant' => 'secondary',
                                'icon' => null,
                                'iconPosition' => 'right',
                                'disabled' => false,
                                'additionalClasses' => ""
                            ])
                        </div>
                    </div>
                </div>
               
                <div class="mt-4 md:mt-16 flex items-end justify-between">
                    <div class="flex flex-col gap-y-4">
                        <p class="headline-6 font-bold uppercase">{{$location['label']}}</p>
                        @include('components.icon-cta', [
                            "text" => $location['text'],
                            "icon" => $location['icon'],
                            "action" => $location['action'],
                            "link" => $location['link'] ?? '#',
                            "text_to_copy" => $location['text_to_copy'],
                            "fontWeight" => "font-medium"
                        ])
                    </div>
                    <div class="hidden md:flex h-fit">
                        @include('partials.material-icon', ['name' => 'arrow_outward', 'class' => 'icon-opsz-56!'])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endcomponent

@unset($background_color)
@unset($class_name)