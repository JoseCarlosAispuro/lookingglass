@php
    $play = $play ?? null;
@endphp

@if($play)
    @php
        $startPlayDateRaw = get_field('start_play_date', $play->ID);
        $dateObj = date_create($startPlayDateRaw);
        $startPlayDate = $dateObj->format('Y');
        $title = $play->post_title;
        $url = get_permalink($play->ID);
    @endphp
    <li class="py-4 flex last-of-type:border-b border-t border-solid border-(--app-fg-color)/30">
        <div class="w-full grid grid-cols-4 gap-x-4">
            <div class="col-span-1">
                @if($startPlayDate)
                    <span class="button-lg font-medium">{{ $startPlayDate }}</span>
                @endif
            </div>
            <div class="col-span-3">
                @include('partials.link', [
                    'label' => $title,
                    'url' => $url,
                    'target' => "_self",
                    'link_size' => 'lg',
                    'containerClass' => 'w-fit! whitespace-nowrap'
                ])
            </div>
        </div>
    </li>
@endif