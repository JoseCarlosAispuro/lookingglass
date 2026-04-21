@php
    $heading = get_field('heading') ?: 'Jobs';
    $supportingText = get_field('supporting_text');
    $loadMoreLabel = get_field('load_more_label') ?: 'Load more';
    $jobs = get_field('jobs') ?: [];

    $background_color = $background_color ?? 'black';
    $text_color = '(--app-fg-color)';

    $class_name = "w-full py-20 md:py-40 bg-{$background_color} text-{$text_color}";

    $batchSize = 4;
    $totalJobs = count($jobs);
    $hasMoreJobs = $totalJobs > $batchSize;
@endphp

@component('partials.block', compact('block_name', 'class_name'))

<div
    data-jobs-listing
    data-batch-size="{{ $batchSize }}"
    data-total-jobs="{{ $totalJobs }}"
>
    <div class="container-ultrawide flex flex-col gap-y-12 md:gap-y-30">
        {{-- Header section --}}
        <div class="grid grid-cols-12 gap-4">
            {{-- Heading --}}
            @if($heading)
                <div class="col-span-12 md:col-span-6">
                    <h2 class="headline-4 font-semibold md:headline-3! uppercase">
                        {{ $heading }}
                    </h2>
                </div>
            @endif

            {{-- Supporting text --}}
            @if($supportingText)
                <div class="col-span-12 md:col-span-6">
                    <p class="body-xl">
                        {{ $supportingText }}
                    </p>
                </div>
            @endif
        </div>

        {{-- Jobs list --}}
        @if(!empty($jobs))
            <div class="jobs-listing__list" role="list">
                @foreach($jobs as $index => $job)
                    @php
                        $title = $job['title'] ?? '';
                        $eyebrow = $job['eyebrow'] ?? '';
                        $url = $job['url'] ?? '#';
                        $linkType = $job['link_type'] ?? 'external';
                        $isHidden = $index >= $batchSize;
                    @endphp

                    <a
                        href="{{ esc_url($url) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="jobs-listing__item group
                               block border-t border-{{ $text_color }}/20
                               py-[20px] md:py-[24px]
                               {{ $isHidden ? 'hidden' : '' }}
                               opacity-60 md:opacity-50
                               transition-all duration-300 ease-out
                               hover:opacity-100 focus-visible:opacity-100
                               focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-{{ $text_color }}/50 focus-visible:ring-offset-2 focus-visible:ring-offset-{{ $background_color }}
                               motion-reduce:transition-none"
                        data-job-item
                        data-job-index="{{ $index }}"
                        role="listitem"
                    >
                        <div class="flex items-center justify-between">
                            {{-- Title and eyebrow --}}
                            <div class="flex items-start gap-2 md:gap-3
                                        transition-transform duration-300 ease-out
                                        group-hover:translate-x-2 group-focus-visible:translate-x-2
                                        motion-reduce:transform-none motion-reduce:transition-none">
                                <span class="font-cambon-condensed text-[48px] md:text-[72px] leading-[1.1] tracking-tight">
                                    {{ $title }}
                                </span>
                                @if($eyebrow)
                                    <span class="font-saans text-[18px] md:text-[20px] text-{{ $text_color }}/60 mt-[2px] md:mt-[4px]">
                                        {{ $eyebrow }}
                                    </span>
                                @endif
                            </div>

                            {{-- Arrow icon --}}
                            <span class="jobs-listing__arrow
                                         flex-shrink-0 w-[32px] h-[32px] md:w-[80px] md:h-[80px]
                                         md:opacity-0 md:translate-x-[-8px]
                                         transition-all duration-300 ease-out
                                         group-hover:opacity-100 group-hover:translate-x-0
                                         group-focus-visible:opacity-100 group-focus-visible:translate-x-0
                                         motion-reduce:opacity-100 motion-reduce:transform-none motion-reduce:transition-none"
                                  aria-hidden="true"
                            >
                                @include('partials.material-icon', ['name' => 'arrow_outward', 'class' => 'icon-opsz-32! md:icon-opsz-80!'])
                            </span>
                        </div>
                    </a>
                @endforeach

                {{-- Bottom border --}}
                <div class="border-t border-{{ $text_color }}/20"></div>
            </div>
        @else
            <p class="body-lg text-{{ $text_color }}/60 text-center py-[40px]">
                No open positions at this time.
            </p>
        @endif

        {{-- Load more button --}}
        @if($hasMoreJobs)
            <div class="mt-[40px] md:mt-[60px] flex justify-center">
                <button
                    type="button"
                    class="jobs-listing__load-more
                           w-full md:w-auto md:min-w-[200px]
                           px-[24px] py-[14px] md:px-[32px] md:py-[16px]
                           border border-{{ $text_color }} text-{{ $text_color }}
                           bg-transparent
                           font-saans text-[16px] md:text-[18px] leading-[1.2]
                           transition-all duration-200 ease-out
                           hover:bg-{{ $text_color }} hover:text-{{ $background_color }}
                           focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-{{ $text_color }}/50 focus-visible:ring-offset-2 focus-visible:ring-offset-{{ $background_color }}
                           motion-reduce:transition-none
                           disabled:opacity-50 disabled:cursor-not-allowed"
                    data-load-more-btn
                    aria-label="Load more job listings"
                >
                    {{ $loadMoreLabel }}
                </button>
            </div>
        @endif
    </div>
</div>

@endcomponent
