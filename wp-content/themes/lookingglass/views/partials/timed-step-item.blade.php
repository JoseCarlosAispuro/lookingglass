@php
    $descriptionClass = $descriptionClass ?? 'body-lg';
@endphp

<button class="timed-step flex items-start gap-x-sm text-left w-full cursor-pointer {{ $index === 0 ? 'is-active' : '' }}"
        data-step="{{ $index }}"
        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
        type="button">
    {{-- Progress bar (vertical) --}}
    <div class="relative w-[2px] self-stretch shrink-0">
        <div class="absolute inset-0 bg-white/30"></div>
        <div class="absolute top-0 left-0 w-full bg-white"
             data-step-progress
             style="height: 0%"></div>
    </div>

    {{-- Content --}}
    <div class="flex-1 min-w-0">
        <div class="flex items-start gap-x-sm">
            <span class="timed-step__number shrink-0 inline-flex items-center justify-center w-6 h-6 rounded-full text-[14px] font-saans font-bold leading-none"
                  data-step-number>
                {{ $index + 1 }}
            </span>
            <span class="headline-6 uppercase">{{ $step['title'] }}</span>
        </div>
        <div class="timed-step__description" data-step-description>
            <div class="{{ $descriptionClass }} mt-sm text-(--app-fg-color)">{!! $step['description'] !!}</div>
        </div>
    </div>
</button>
