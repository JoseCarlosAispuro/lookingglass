@php
$col1_title   = get_field('col1_title');
$col1_text    = get_field('col1_text');

$col2_title   = get_field('col2_title');
$col2_text    = get_field('col2_text');

$menu_visit    = get_field('menu_visit');
$menu_about    = get_field('menu_about');
$menu_involved = get_field('menu_involved');
$menu_connect  = get_field('menu_connect');

$copyright    = get_field('copyright');
$backtotop    = get_field('backtotop');

$logo_path    = get_theme_file_uri('/assets/images/logo.svg');
@endphp

@component('partials.block', compact('block_name', 'class_name'))

<footer class="bg-[#ff9217] text-black py-4 px-4 lg:px-4 relative">

    {{-- Top columns --}}
    <div class="grid grid-cols-1 md:grid-cols-6 gap-10 text-sm font-medium">

        {{-- Column 1 --}}
        <div>
            <h3 class="font-[570] text-base leading-[1.2] tracking-normal mb-2">{{ $col1_title }}</h3>
            <p class="font-[380] text-base leading-[1.2] tracking-normal">{!! nl2br($col1_text) !!}</p>
        </div>

        {{-- Column 2 --}}
        <div>
            <h3 class="font-[570] text-base leading-[1.2] tracking-normal mb-2">{{ $col2_title }}</h3>
            <p class="font-[380] text-base leading-[1.2] tracking-normal">{!! nl2br($col2_text) !!}</p>
        </div>

        {{-- Dynamic Menus --}}
        <div>
            <h3 class="font-[570] text-base leading-[1.2] tracking-normal mb-2">Plan Your Visit</h3>
            @if($menu_visit)
                {!! wp_nav_menu([
                    'menu' => $menu_visit,
                    'container' => false,
                    'echo' => false,
                    'menu_class' => 'font-[380] text-base leading-[1.2] tracking-normal'
                ]) !!}
            @endif
        </div>

        <div>
            <h3 class="font-[570] text-base leading-[1.2] tracking-normal mb-2">About Us</h3>
            @if($menu_about)
                {!! wp_nav_menu([
                    'menu' => $menu_about,
                    'container' => false,
                    'echo' => false,
                    'menu_class' => 'font-[380] text-base leading-[1.2] tracking-normal'
                ]) !!}
            @endif
        </div>

        <div>
            <h3 class="font-[570] text-base leading-[1.2] tracking-normal mb-2">Get Involved</h3>
            @if($menu_involved)
                {!! wp_nav_menu([
                    'menu' => $menu_involved,
                    'container' => false,
                    'echo' => false,
                    'menu_class' => 'font-[380] text-base leading-[1.2] tracking-normal'
                ]) !!}
            @endif
        </div>

        <div>
            <h3 class="font-[570] text-base leading-[1.2] tracking-normal mb-2">Connect</h3>
            @if($menu_connect)
                {!! wp_nav_menu([
                    'menu' => $menu_connect,
                    'container' => false,
                    'echo' => false,
                    'menu_class' => 'font-[380] text-base leading-[1.2] tracking-normal'
                ]) !!}
            @endif
        </div>

    </div>

    {{-- Big Logo --}}
    <div class="mt-12">
        <img src="{{ $logo_path }}" alt="Lookingglass Logo" class="w-full pointer-events-none select-none" loading="lazy">
    </div>

    {{-- Bottom Row --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 font-[380] text-base leading-[1.2] tracking-normal">
        <span>{{ $copyright }}</span>
        <a href="#" class="no-underline">{{ $backtotop }}</a>
    </div>

</footer>

@endcomponent
