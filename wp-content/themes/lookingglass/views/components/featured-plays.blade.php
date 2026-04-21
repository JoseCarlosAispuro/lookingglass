@php
    $heading = get_field('heading');
    $intro = get_field('intro');
    $plays = get_field('plays');

    $background_color = $background_color ?? 'white';
    $class_name = "py-lg md:py-[160px] bg-{$background_color} text-(--app-fg-color)";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
    <div class="container-ultrawide">
        <div class="grid grid-cols-12">
            <div class="col-span-12 md:col-span-10 md:col-start-2">
                {{-- Header --}}
                <div class="flex flex-col gap-sm md:gap-xl">
                    @if($heading)
                        <h2
                            class="headline-2 uppercase"
                            data-word-animate
                            data-animate-preset="wordUp"
                            data-animate-delay="0"
                            data-animate-duration="1.5"
                            data-animate-stagger="0.1"
                        >{{ $heading }}</h2>
                    @endif

                    @if($intro)
                        <div class="md:grid md:grid-cols-12">
                            <p class="body-lg md:col-span-6 md:col-start-7">{{ $intro }}</p>
                        </div>
                    @endif
                </div>

                {{-- Plays list --}}
                @if($plays && count($plays) > 0)
                    <div class="flex flex-col gap-sm mt-lg md:mt-xl">
                        @foreach($plays as $play)
                            @php
                                $playId = $play['play'][0] ?? null;
                                if (!$playId) continue;

                                $title = get_the_title($playId);
                                $permalink = get_the_permalink($playId);
                                $excerpt = get_the_excerpt($playId);

                                $imageOverride = $play['image_override'] ?? null;
                                $imageId = $imageOverride
                                    ? ($imageOverride['ID'] ?? $imageOverride['id'] ?? null)
                                    : get_post_thumbnail_id($playId);

                                $startDate = get_field('start_play_date', $playId);
                                $endDate = get_field('end_play_date', $playId);
                                $dateRange = '';

                                if ($startDate) {
                                    $start = \DateTime::createFromFormat('m/d/Y', $startDate);
                                    $end = $endDate ? \DateTime::createFromFormat('m/d/Y', $endDate) : null;

                                    if ($start && $end) {
                                        $startMonth = strtoupper($start->format('M'));
                                        $endMonth = strtoupper($end->format('M'));
                                        $year = $end->format('Y');
                                        $dateRange = $startMonth === $endMonth
                                            ? "{$startMonth}, {$year}"
                                            : "{$startMonth} – {$endMonth}, {$year}";
                                    } elseif ($start) {
                                        $dateRange = strtoupper($start->format('M')) . ', ' . $start->format('Y');
                                    }
                                }
                            @endphp

                            {{-- Divider --}}
                            <hr class="border-t border-current/20 m-0">

                            {{-- Play card --}}
                            <div class="grid md:grid-cols-12">
                                {{-- Image --}}
                                <div class="md:col-span-5">
                                    <div class="aspect-square overflow-hidden">
                                        @if($imageId)
                                            {!! wp_get_attachment_image($imageId, 'large', false, [
                                                'class' => 'w-full h-full object-cover',
                                                'loading' => 'lazy',
                                                'sizes' => '(min-width: 1024px) 40vw, 100vw',
                                            ]) !!}
                                        @endif
                                    </div>
                                </div>

                                {{-- Content --}}
                                <div class="md:col-span-6 md:col-start-7 flex flex-col gap-md mt-sm md:mt-0">
                                    <div class="flex flex-col gap-xs">
                                        <h3 class="headline-5 uppercase">{{ $title }}</h3>
                                        @if($dateRange)
                                            <p class="body-sm text-(--app-fg-color)/50">{{ $dateRange }}</p>
                                        @endif
                                    </div>

                                    @if($excerpt)
                                        <p class="body-md">{{ $excerpt }}</p>
                                    @endif

                                    <div class="md:mt-auto">
                                        @include('partials.link', [
                                            'label' => 'Learn more',
                                            'url' => $permalink,
                                            'target' => '_self',
                                            'isSmall' => false,
                                        ])
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Bottom divider --}}
                        <hr class="border-t border-current/20 m-0">
                    </div>
                @endif
            </div>
        </div>
    </div>
@endcomponent
