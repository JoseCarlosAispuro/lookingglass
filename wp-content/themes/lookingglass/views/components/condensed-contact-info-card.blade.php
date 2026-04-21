@php
    $marquee_text = get_field('marquee_text');
    $heading = get_field('heading') ?: 'CONTACT INFO';
    $address_label = get_field('address_label');
    $address_link = get_field('address_link');
    $phone = get_field('phone');
    $email = get_field('email');

    $background_color = $background_color ?? 'white';
    $class_name = "bg-{$background_color}";

    // Build maps URL from address if no explicit link provided
    if (!$address_link && $address_label) {
        $address_link = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($address_label);
    }
@endphp

@component('partials.block', compact('block_name', 'class_name'))

    {{-- MARQUEE RIBBON --}}
    @if($marquee_text)
        <div class="bg-black overflow-hidden whitespace-nowrap px-4" data-marquee data-marquee-speed="70">
            <div class="flex will-change-transform motion-reduce:transform-none motion-reduce:!animate-none" data-marquee-track>
                <span class="headline-6 uppercase text-white shrink-0 pr-8" data-marquee-text>
                    {{ $marquee_text }}
                </span>
            </div>
        </div>
    @endif

    {{-- CONTACT INFO CARD --}}
    <div class="bg-(--app-fg-color)/10 px-4 py-4 md:py-8">
        <div class="max-w-[605px]">
            <p class="headline-6 uppercase">{{ $heading }}</p>

            <div class="flex flex-col items-start mt-4 md:mt-8 gap-4 md:gap-0">
                {{-- Address row --}}
                @if($address_label)
                    <div class="inline-flex gap-3 items-center">
                        <a href="{{ $address_link }}" target="_blank" rel="noopener noreferrer" class="button-lg font-medium">
                            {{ $address_label }}
                        </a>
                        <a href="{{ $address_link }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="w-8 h-8 md:w-10 md:h-10 rounded-full border border-black/20 flex items-center justify-center shrink-0 transition-all ease-in-out hover:bg-black/10"
                           aria-label="Open address in maps">
                            @include('partials.material-icon', ['name' => 'location_on', 'class' => 'icon-opsz-24'])
                        </a>
                    </div>
                @endif

                {{-- Phone row --}}
                @if($phone)
                    <div class="inline-flex gap-3 items-center">
                        <p class="button-lg font-medium">{{ $phone }}</p>

                        {{-- Desktop: copy to clipboard --}}
                        <button class="hidden md:flex relative w-8 h-8 md:w-10 md:h-10 rounded-full border border-black/20 items-center justify-center shrink-0 transition-all ease-in-out hover:bg-black/10"
                                data-icon-cta
                                data-copy-this-text="{{ $phone }}"
                                aria-label="Copy phone number">
                            @include('partials.material-icon', ['name' => 'phone_enabled', 'class' => 'icon-opsz-24'])
                            <span role="tooltip" aria-live="polite" data-cta-tooltip>Copied!</span>
                        </button>

                        {{-- Mobile: open dialer --}}
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}"
                           class="flex md:hidden w-8 h-8 rounded-full border border-black/20 items-center justify-center shrink-0 transition-all ease-in-out hover:bg-black/10"
                           aria-label="Call phone number">
                            @include('partials.material-icon', ['name' => 'phone_enabled', 'class' => 'icon-opsz-24'])
                        </a>
                    </div>
                @endif

                {{-- Email row --}}
                @if($email)
                    <div class="inline-flex gap-3 items-center">
                        <p class="button-lg font-medium">{{ $email }}</p>
                        <button class="relative w-8 h-8 md:w-10 md:h-10 rounded-full border border-black/20 flex items-center justify-center shrink-0 transition-all ease-in-out hover:bg-black/10"
                                data-icon-cta
                                data-copy-this-text="{{ $email }}"
                                aria-label="Copy email address">
                            @include('partials.material-icon', ['name' => 'link', 'class' => 'icon-opsz-24'])
                            <span role="tooltip" aria-live="polite" data-cta-tooltip>Copied!</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endcomponent
