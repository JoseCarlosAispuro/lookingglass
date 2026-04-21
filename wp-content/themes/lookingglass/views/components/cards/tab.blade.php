<div class="text-(--app-fb-color) {{$index === 0 ? 'open' : '' }}" data-tab data-tab-index="{{$index}}">
    <div data-tab-preview>
        <span class="headline-4 font-semibold uppercase cursor-pointer">{{$tab['tab_title']}}</span>
        <div class="tab-icon-plus md:hidden absolute right-0 top-1/2 -translate-y-1/2">
            @include('partials.material-icon', ['name' => 'add', 'class' => 'icon-opsz-24'])
        </div>
        <div class="tab-icon-minus md:hidden absolute right-0 top-1/2 -translate-y-1/2">
            @include('partials.material-icon', ['name' => 'remove', 'class' => 'icon-opsz-24'])
        </div>
    </div>

    <button class="absolute inset-0 z-2" aria-label="Open {{$tab['tab_title']}} tab">
        <span class="sr-only">Open {{$tab['tab_title']}} tab</span>
    </button>

    <div class="{{$index === 0 ? 'opacity-100' : 'opacity-0' }}" data-tab-inner>
        <div class="grid grid-cols-9 gap-y-4" data-tab-inner-title>
            <div class="hidden md:block col-span-9 md:col-span-7">
                <p class="headline-4 font-semibold uppercase">{{$tab['tab_title']}}</p>
            </div>

            @if(isset($tab['header_image']) && !empty($tab['header_image']['ID']))
                {!! wp_get_attachment_image($tab['header_image']['ID'], 'medium', false, ['class' => 'mb-4 object-cover aspect-square col-span-9 md:col-span-2 w-full', 'sizes' => '(min-width: 768px) 22vw, 100vw', 'loading' => 'lazy']) !!}
            @endif
        </div>
        <div class="py-4 border-y border-solid border-black/20 flex flex-col gap-y-14" data-tab-inner-content>
            <div class="body-xl" data-rich-text>{!! $tab['content'] !!}</div>
            <div class="body-lg" data-rich-text>{!! $tab['extra_content'] !!}</div>
        </div>
        <div class="md:pt-16 md:mt-auto flex items-end justify-between" data-tab-inner-footer>
            <div class="flex flex-col gap-y-4">
                <p class="headline-6 font-bold uppercase">{{$tab['footer_title']}}</p>
                @include('components.icon-cta', ['cta' => $tab['cta']])
            </div>
            <div class="hidden md:flex h-fit">
                @include('partials.material-icon', ['name' => 'arrow_outward', 'class' => 'icon-opsz-56!'])
            </div>
        </div>
    </div>
</div>