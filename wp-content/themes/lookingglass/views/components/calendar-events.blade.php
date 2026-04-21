@php
    $defaultSelected = check_default_time_selected($times);
@endphp

<form data-play-time-form>
    <fieldset>
        @foreach($times as $timeIndex => $time)
            <div data-radio-button-container
                 class="py-md md:py-9 px-sm flex justify-between body-md border-b border-border-secondary items-center gap-7.5 {{$time['default_selected'] ? 'bg-black-100' : ''}}">
                <div class="relative flex items-center gap-sm">
                    <input class="peer disabled:text-disabled appearance-none w-6 aspect-square opacity-0"
                           name="event-times" id="{{'event-time-option-' . $eventIndex . '-' . $timeIndex}}"
                           type="radio"
                           {{$time['default_selected'] ? 'checked' : ''}} {{$time['day_status'] === 'sold_out' || $time['day_status'] === 'na' ? 'disabled' : ''}}
                           data-option-url="{{$time['external_url']}}"
                           data-radio-button-label="{{$time['cta_type']['label']}}">
                    <label class="peer-disabled:text-disabled peer-checked:font-semibold"
                           for="{{'event-time-option-' . $eventIndex . '-' . $timeIndex}}">
                        <span class="block {{isset($time['time_label_support_text']) && $time['time_label_support_text'] ? 'font-semibold' : ''}}">{{$time['time_label']}}</span>
                        @if(isset($time['time_label_support_text']) && $time['time_label_support_text'])
                            <span class="block">{{$time['time_label_support_text']}}</span>
                        @endif
                    </label>
                    <span class="top-1/2 -translate-y-1/2 pointer-events-none peer-checked:flex items-center justify-center hidden bg-black absolute left-0 w-5 h-5 rounded-full border-[2.5px] border-black peer-disabled:border-black-100">
                        <span class="flex pointer-events-none">@include('partials.material-icon', ['name' => 'check_circle', 'class' => 'flex text-black-100 !text-[28px]'])</span>
                    </span>
                    <span class="top-1/2 -translate-y-1/2 pointer-events-none flex peer-checked:hidden bg-transparent absolute left-0 w-5 h-5 rounded-full border-[2.5px] border-black peer-disabled:border-black-100"></span>
                </div>
                <span class="text-right {{$time['day_status'] === 'sold_out' || $time['day_status'] === 'na' ? 'text-disabled' : ''}}">{!! $time['day_status'] === 'sold_out' || $time['day_status'] === 'na' ? get_status_label($time['day_status']) : $time['price'] !!}</span>
            </div>
        @endforeach
    </fieldset>
    @include('components.button-block', [
        'buttonLink' => [
            'url' => $defaultUrl,
            'title' => $defaultSelected[0]['cta_type']['label'] ?? 'Book Now'
        ],
        'variant' => 'primary',
        'size' => 'lg',
        'ariaLabel' => 'Book now',
        'disabled' => count($defaultSelected) <= 0,
        'additionalClasses' => 'text-white bg-black border-black hidden md:flex w-full mt-16 md:mt-0'
    ])
</form>
