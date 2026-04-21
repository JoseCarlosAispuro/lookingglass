@php
    $menu = get_nav_menu_object_by_location(NAV_MENUS_FOOTER);

    if (!$menu) {
        $menu = (object)['term_id' => ''];
    }

    // Get ACF fields attached to the footer menu (set up in Appearance > Menus)
    $contactDetails = get_field('contact_details', "menu_{$menu->term_id}") ?? [];
    $copyright = get_field('copyright', "menu_{$menu->term_id}") ?? date('Y') . ' Lookingglass Theatre Company. All Rights Reserved.';

    // Get menu items for custom rendering
    $menuItems = get_nav_menu_items_by_location(NAV_MENUS_FOOTER) ?? [];

    // Organize menu items into groups (top-level items with their children)
    $menuGroups = [];
    $parentMap = [];

    foreach ($menuItems as $item) {
        if ($item->menu_item_parent == 0) {
            $menuGroups[$item->ID] = [
                'title' => $item->title,
                'url' => $item->url,
                'children' => []
            ];
            $parentMap[$item->ID] = $item->ID;
        }
    }

    foreach ($menuItems as $item) {
        if ($item->menu_item_parent != 0 && isset($menuGroups[$item->menu_item_parent])) {
            $menuGroups[$item->menu_item_parent]['children'][] = [
                'title' => $item->title,
                'url' => $item->url
            ];
        }
    }
@endphp

<footer class="relative footer bg-orange text-black overflow-hidden z-50" data-footer>
    {{-- Main footer content --}}
    <div class="container-ultrawide pt-[24px] md:pt-[48px] relative z-10">

        {{-- Desktop: 6 column grid --}}
        <div class="hidden md:grid md:grid-cols-6 gap-8">
            {{-- Contact details (2 columns) --}}
            @foreach($contactDetails as $contact)
                <div class="footer-nav-group">
                    @if(!empty($contact['title']))
                        <h3 class="font-saans font-bold text-[16px] leading-[1.2] mb-4">
                            {{ $contact['title'] }}
                        </h3>
                    @endif
                    @if(!empty($contact['details']))
                        <p class="font-saans text-[16px] leading-[1.4]">{!! $contact['details'] !!}</p>
                    @endif
                </div>
            @endforeach

            {{-- Menu groups (4 columns) --}}
            @foreach($menuGroups as $groupId => $group)
                <div class="footer-nav-group">
                    <h3 class="font-saans font-bold text-[16px] leading-[1.2] mb-4">
                        {{ $group['title'] }}
                    </h3>
                    @if(!empty($group['children']))
                        <ul class="space-y-2">
                            @foreach($group['children'] as $child)
                                <li>
                                    <a href="{{ esc_url($child['url']) }}" class="font-saans text-[16px] leading-[1.4] hover:opacity-50 transition-opacity">
                                        {{ $child['title'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Mobile: Single column with accordions --}}
        <div class="md:hidden">
            {{-- Contact details (always visible) --}}
            @foreach($contactDetails as $index => $contact)
                <div class="{{($index + 1) === count($contactDetails) ? 'mb-2' : 'mb-6'}}">
                    @if(!empty($contact['title']))
                        <h3 class="button-md font-medium">
                            {{ $contact['title'] }}
                        </h3>
                    @endif
                    @if(!empty($contact['details']))
                        <p class="body-sm font-normal">{!! $contact['mobile_details'] !!}</p>
                    @endif
                </div>
            @endforeach

            {{-- Collapsible menu sections --}}
            <div>
                @foreach($menuGroups as $groupId => $group)
                    <div data-accordion-item>
                        {{-- Accordion header --}}
                        <button
                            type="button"
                            class="w-full flex items-center justify-between py-3 font-saans font-bold text-[16px] leading-[1.2] text-left"
                            data-accordion-trigger
                            aria-expanded="false"
                        >
                            <span class="button-md font-medium">{{ $group['title'] }}</span>
                            <span class="accordion-icon text-[24px] leading-none transition-transform duration-200" aria-hidden="true">+</span>
                        </button>

                        {{-- Accordion content --}}
                        <div class="accordion-content overflow-hidden max-h-0 transition-all duration-300" data-accordion-content>
                            @if(!empty($group['children']))
                                <ul class="pb-2 space-y-2">
                                    @foreach($group['children'] as $child)
                                        <li>
                                            <a href="{{ esc_url($child['url']) }}" class="body-md font-normal hover:opacity-70 transition-opacity">
                                                {{ $child['title'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Background Wordmark --}}
    <div class="pb-12 md:pb-0 relative w-full mt-6 pointer-events-none select-none" aria-hidden="true">
        <div class="w-full max-w-none md:mt-[80px]">
            @include('partials.icons', ['name' => 'logo', 'classes' => 'w-300 md:w-full h-auto text-black'])
        </div>
    </div>

    {{-- Bottom Bar --}}
    <div class="absolute bottom-0 container-ultrawide z-10 pb-[24px] md:pb-[32px]">
        <div class="flex flex-col items-center md:flex-row md:items-center md:justify-between gap-4 text-center md:text-left">
            {{-- Copyright --}}
            <p class="body-sm font-normal max-w-75 md:max-w-full">
                {{ $copyright }}
            </p>

            {{-- Back to Top --}}
            <button
                type="button"
                class="font-saans text-[14px] md:text-[16px] inline-flex items-center gap-1 hover:opacity-70 transition-opacity"
                data-back-to-top
                aria-label="Scroll back to top of page"
            >
                <span>&uarr;</span>
                <span>Back to top</span>
            </button>
        </div>
    </div>
</footer>
