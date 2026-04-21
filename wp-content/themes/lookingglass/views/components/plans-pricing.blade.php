@php
    $background_color = $background_color ?? 'white';
    $heading = $heading ?? '';
    $plans = $plans ?? [];
    $sections = $sections ?? [];

    $class_name = "py-20 md:py-40 bg-{$background_color} text-(--app-fg-color)";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
<div class="container-ultrawide">
    {{-- Heading --}}
    @if($heading)
        <h2
            data-word-animate
            data-animate-preset="wordUp"
            data-animate-delay="0"
            data-animate-duration="1.5"
            data-animate-stagger="0.1"
            class="headline-2 uppercase font-semibold md:!flex md:!justify-between"
        >{{ $heading }}</h2>
    @endif

    {{-- Pricing Cards Grid --}}
    @if(!empty($plans))
        <div class="grid grid-cols-1 md:grid-cols-4 gap-sm mt-12 md:mt-20" role="list">
            @foreach($plans as $plan)
                <article
                    class="group/card flex flex-col border border-(--app-fg-color) rounded-sm p-sm transition-colors duration-300 md:hover:bg-orange md:hover:text-black md:hover:border-orange md:focus-within:bg-orange md:focus-within:text-black md:focus-within:border-black"
                    role="listitem"
                >
                    {{-- Plan Name --}}
                    <h3 class="headline-5 uppercase font-semibold text-center">{{ $plan['plan_name'] }}</h3>

                    {{-- Bullet List --}}
                    @if(!empty($plan['bullet_list']))
                        <ul class="list-disc list-inside text-center leading-[1.2] body-md mt-6 flex-grow space-y-1">
                            @foreach($plan['bullet_list'] as $bullet)
                                <li class="!mb-0">{{ $bullet['bullet_item'] }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="flex-grow"></div>
                    @endif

                    {{-- Price + CTA --}}
                    <div class="mt-auto pt-6 text-center">
                        {{-- Price --}}
                        <p class="mb-6 flex items-center justify-center gap-x-1">
                            <span class="headline-6 font-bold align-top">$</span>
                            <span class="headline-5 font-semibold">{{ $plan['price'] }}</span>
                        </p>

                        {{-- CTA Button --}}
                        @if(!empty($plan['cta_link']))
                            @include('components.button-block', [
                                'buttonLink' => $plan['cta_link'],
                                'variant' => 'secondary',
                                'size' => 'md',
                                'additionalClasses' => '!bg-black md:!bg-transparent text-white md:text-(--app-fg-color) hover:bg-black! hover:text-white! hover:border-black! w-full',
                            ])
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    {{-- Below-Grid Sections --}}
    @if(!empty($sections))
        <div class="mt-12 md:mt-20">
            @foreach($sections as $section)
                <div class="border-t border-border-secondary py-4 md:py-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <h3 class="headline-5 uppercase font-semibold">{{ $section['section_title'] }}</h3>
                    <div class="body-md normalized-line-height" data-rich-text>{!! $section['section_content'] !!}</div>
                </div>
            @endforeach
            <div class="border-t border-border-secondary"></div>
        </div>
    @endif
</div>
@endcomponent

@unset($background_color)
@unset($class_name)
