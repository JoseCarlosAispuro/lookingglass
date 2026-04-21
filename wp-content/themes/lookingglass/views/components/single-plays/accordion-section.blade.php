<div
    class="accordion-item border-b border-black-100 transition-all duration-300 ease-out overflow-hidden"
    data-accordion>
    <button class="group relative py-sm w-full text-left" data-action-item aria-expanded='false'>
        <span class="headline-5 font-semibold uppercase">{{$heading}}</span>
        <div class="absolute right-0 top-1/2 -translate-y-1/2 group-aria-expanded:hidden">
            @include('partials.material-icon', ['name' => 'add', 'class' => 'icon-opsz-32!'])
        </div>
        <div class="absolute right-0 top-1/2 -translate-y-1/2 hidden group-aria-expanded:block">
            @include('partials.material-icon', ['name' => 'remove', 'class' => 'icon-opsz-32!'])
        </div>
    </button>
    <div class="mt-0 md:mt-sm body-lg pb-sm flex-wrap flex gap-md md:gap-sm" data-content>
        {!! $slot !!}
    </div>
</div>
