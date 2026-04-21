@php
    $section_heading = get_field('section_heading') ?: '';
    $section_intro = get_field('section_intro') ?: '';
    $courses = get_field('courses') ?: [];

    $background_color = $background_color ?? 'black';
    $text_color = '(--app-fg-color)';

    $class_name = "courses-listing w-full py-[60px] md:py-[100px] bg-{$background_color} text-{$text_color}";

    $metadata_icons = [
        ['field' => 'course_date',           'icon' => 'calendar_today', 'label' => 'DATE'],
        ['field' => 'course_time',           'icon' => 'schedule',       'label' => 'TIME'],
        ['field' => 'course_ages',           'icon' => 'face',           'label' => 'AGES'],
        ['field' => 'course_max_enrollment', 'icon' => 'accessibility',  'label' => 'MAX. ENROLLMENT'],
        ['field' => 'course_price',          'icon' => 'attach_money',   'label' => 'PRICE'],
    ];
@endphp

@component('partials.block', compact('block_name', 'class_name'))

<div class="container">
    {{-- Header --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-md md:gap-0 mb-[60px] md:mb-[80px]">
        @if($section_heading)
            <div class="md:col-span-5">
                <h2 class="headline-2 uppercase" data-word-animate data-animate-preset="wordUp" data-animate-stagger="0.1">{{ $section_heading }}</h2>
            </div>
        @endif

        @if($section_intro)
            <div class="md:col-span-6 md:col-start-7 flex items-end">
                <p class="body-xl">{!! nl2br(esc_html($section_intro)) !!}</p>
            </div>
        @endif
    </div>

    {{-- Courses --}}
    @if(!empty($courses))
        @foreach($courses as $index => $course)
            @php
                $image = $course['course_image'] ?? null;
                $image_id = $image['ID'] ?? $image['id'] ?? null;
                $title = $course['course_title'] ?? '';
                $description = $course['course_description'] ?? '';
                $cta = $course['course_cta'] ?? null;
            @endphp

            {{-- Divider --}}
            <div class="border-t border-{{ $text_color }}/20"></div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-md md:gap-0 py-[40px] md:py-[60px]">
                {{-- Left: Sticky image --}}
                <div class="md:col-span-5">
                    <div class="md:sticky md:top-28">
                        @if($image_id)
                            <div class="aspect-square overflow-hidden">
                                {!! wp_get_attachment_image($image_id, 'large', false, [
                                    'class' => 'w-full h-full object-cover',
                                    'loading' => 'lazy',
                                ]) !!}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right: Content --}}
                <div class="md:col-span-6 md:col-start-7 flex flex-col gap-6 md:gap-16">
                    {{-- Title --}}
                    @if($title)
                        <h3 class="headline-5 uppercase">{{ $title }}</h3>
                    @endif

                    {{-- Description --}}
                    @if($description)
                        <div data-rich-text class="body-md">
                            {!! $description !!}
                        </div>
                    @endif

                    {{-- Metadata grid --}}
                    <ul class="md:columns-2 gap-sm">
                        @foreach($metadata_icons as $meta)
                            @php
                                $value = $course[$meta['field']] ?? '';
                            @endphp
                            @if($value)
                                <li class="break-inside-avoid-column flex items-start gap-sm {{$loop->last ? '' : 'mb-4'}}">
                                    @include('partials.material-icon', [
                                        'name' => $meta['icon'],
                                    ])
                                    <div class="flex flex-col">
                                        <span class="headline-6 uppercase">{{ $meta['label'] }}</span>
                                        <span class="body-md uppercase text-{{ $text_color }}/60">{{ $value }}</span>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>

                    {{-- CTA --}}
                    @if($cta)
                        <div>
                            @include('components.button-block', [
                                'buttonLink' => $cta,
                                'variant' => 'secondary',
                                'size' => 'md',
                            ])
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        {{-- Bottom divider --}}
        <div class="border-t border-{{ $text_color }}/20"></div>
    @endif
</div>

@endcomponent
