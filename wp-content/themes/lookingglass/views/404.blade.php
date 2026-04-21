@php
    $leftTitle = get_field('left_title', 'options');
    $rightTitle = get_field('right_title', 'options');
    $subHeading = get_field('sub_heading', 'options');
    $image = get_field('image', 'options');
    $buttons = get_field('buttons', 'options');
@endphp

@extends('layouts.master')

@section('content')
    <section class="not-found py-lg md:pt-[56px] md:pb-[170px] flex items-center" data-block-background-color="white"
             data-block-text-color="black"
             data-bg-color="#ffffff">
        <div class="container-ultrawide">
            <div class="grid grid-cols-12 gap-y-lg">
                <div class="col-span-12 md:col-span-4 col-start-1 md:col-start-2 flex flex-col gap-y-lg md:gap-y-sm">
                    @if(isset($leftTitle) && $leftTitle)
                        <h2
                            data-word-animate
                            data-animate-preset="wordUp"
                            data-animate-delay="0"
                            data-animate-duration="1.5"
                            data-animate-stagger="0.1"
                            class="headline-1 font-semibold !hidden md:!block">{{$leftTitle}}</h2>
                    @endif
                    @if($leftTitle || $rightTitle)
                        <h2
                            data-word-animate
                            data-animate-preset="wordUp"
                            data-animate-delay="0"
                            data-animate-duration="1.5"
                            data-animate-stagger="0.1"
                            class="headline-1 font-semibold !block md:!hidden">{{$leftTitle . ' ' . $rightTitle}}</h2>
                    @endif
                    @if(isset($image) && $image)
                        <div class="w-full md:w-[340px] h-auto">
                            {!! wp_get_attachment_image($image,'large', false, ['class' => 'w-full h-full object-cover object-center', 'loading' => 'lazy']) !!}
                        </div>
                    @endif
                </div>
                <div class="col-span-12 md:col-span-5 col-start-1 md:col-start-6 flex flex-col gap-y-md md:gap-y-8">
                    @if(isset($rightTitle) && $rightTitle)
                        <h2
                            data-word-animate
                            data-animate-preset="wordUp"
                            data-animate-delay="0.1"
                            data-animate-duration="1.5"
                            data-animate-stagger="0.1"
                            class="headline-1 font-semibold flex-wrap hidden md:flex">{{$rightTitle}}</h2>
                    @endif
                    @if(isset($subHeading) && $subHeading)
                        <p class="body-xl font-light">{{$subHeading}}</p>
                    @endif
                    @if(isset($buttons) && count($buttons) > 0 )
                        <div class="flex flex-col md:flex-row gap-sm">
                            @foreach($buttons as $index => $button)
                                @include('components.button-block', [
                                    'buttonLink' => $button['button_button_link'],
                                    'variant' => ($index % 2) ? 'secondary' : 'primary',
                                    'icon' => null,
                                    'iconPosition' => 'right',
                                    'disabled' => false,
                                    'ariaLabel' => $button['button_button_aria_label'],
                                    'additionalClasses' => ""
                                ])
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

@endsection
