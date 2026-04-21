<div>
    <div class="py-md md:py-9 px-sm flex justify-between body-md border-b border-border-secondary items-center gap-7.5">
        <p class="text-disabled">No events this day</p>
        <p class="text-disabled">N/A</p>
    </div>
    @include('components.button-block', [
        'buttonLink' => [
            'url' => '#',
            'title' => 'Book Now'
        ],
        'variant' => 'primary',
        'size' => 'lg',
        'ariaLabel' => 'Book now',
        'disabled' => true,
        'additionalClasses' => 'text-white bg-black border-black hidden md:flex w-full mt-16 md:mt-0'
    ])
</div>
