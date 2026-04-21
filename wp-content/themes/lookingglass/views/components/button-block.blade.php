@php
    // ACF fields
    $buttonLink   = $buttonLink ?? get_field('button_link');
    $variant      = $variant ?? null;
    $size         = $size ?? null;
    $icon         = $icon ?? null;
    $iconPosition = $iconPosition ?? null;
    $disabled     = $disabled ?? false;
    $ariaLabel    = $arialabel ?? get_field('button_aria_label');
    $additionalClasses = $additionalClasses ?? '';

    // Required link field
    if (!$buttonLink || empty($buttonLink['url']) || empty($buttonLink['title'])) {
        return;
    }

    $url    = $buttonLink['url'];
    $label  = $buttonLink['title'];
    $target = $buttonLink['target'] ?? '_self';

    // Variant whitelist
    $validVariants = ['primary', 'secondary', 'tertiary'];
    if (!in_array($variant, $validVariants, true)) {
        $variant = 'primary';
    }

    // Size whitelist
    $validSizes = ['sm', 'md', 'lg', 'xl'];
    if (!in_array($size, $validSizes, true)) {
        $size = 'lg';
    }

    // Normalize icon position
    $validIconPositions = ['left', 'right', 'both'];
    if (!in_array($iconPosition, $validIconPositions, true)) {
        $iconPosition = 'right';
    }

    // Normalize icon value from ACF ("''" should behave as empty)
    if ($icon === "''") {
        $icon = '';
    }

    // Material Symbols name mapping for directional icons
    $materialIconMap = [
        'arrow-left' => 'arrow_back',
        'arrow-right' => 'arrow_forward',
        'chevron-left' => 'chevron_left',
        'chevron-right' => 'chevron_right',
    ];

    $isDirectionalIcon = function (?string $iconName): bool {
        return in_array($iconName, ['chevron', 'arrow'], true);
    };

    $resolveIconName = function (?string $iconName, string $side) use ($isDirectionalIcon): ?string {
        if (!$iconName) return null;
        if ($isDirectionalIcon($iconName)) {
            return $iconName . '-' . $side;
        }
        return $iconName;
    };

    $shouldRenderLeftIcon  = !empty($icon) && ($iconPosition === 'left' || $iconPosition === 'both');
    $shouldRenderRightIcon = !empty($icon) && ($iconPosition === 'right' || $iconPosition === 'both');

    $leftIconName  = $resolveIconName($icon, 'left');
    $rightIconName = $resolveIconName($icon, 'right');

    // Determine if icon uses Material Symbols
    $isMaterialIcon = function (?string $iconName) use ($materialIconMap): bool {
        return isset($materialIconMap[$iconName]);
    };

    // Material icon optical size per button size
    $materialIconSizeClass = match ($size) {
        'xl'   => 'icon-opsz-32',
        'md'   => 'icon-opsz-20',
        'sm'   => 'icon-opsz-16',
        default => 'icon-opsz-24',
    };

    // --- Class architecture ---

    $base = implode(' ', [
        'inline-flex items-center justify-center gap-2 p-sm',
        'whitespace-nowrap no-underline text-center',
        'select-none box-border',
        'transition-all duration-200 ease-in-out',
        'focus-visible:outline focus-visible:outline-[2px] focus-visible:outline-orange focus-visible:-outline-offset-[1px]',
        'max-md:w-full',
        'motion-reduce:transition-none motion-reduce:hover:transform-none',
    ]);

    // Size classes
    $sizeClasses = match ($size) {
        'xl'   => 'button-xl',
        'md'   => 'button-md',
        'sm'   => 'button-sm',
        default => 'button-lg',
    };

    // Icon size
    $iconSizeClass = match ($size) {
        'xl'   => 'w-8 h-8',
        'md'   => 'w-5 h-5',
        'sm'   => 'w-4 h-4',
        default => 'w-6 h-6',
    };

    // Variant classes
    $variantClasses = match ($variant) {
        'primary' => implode(' ', [
            'bg-(--app-fg-color) text-(--app-bg-color) border border-(--app-fg-color)',
            'hover:bg-orange hover:text-black hover:border-orange',
            'active:bg-orange-600 active:text-black active:border-orange-600',
        ]),
        'secondary' => implode(' ', [
            'bg-transparent text-(--app-fg-color) border border-(--app-fg-color)',
            'hover:bg-orange hover:text-black hover:border-orange',
            'active:bg-orange-600 active:text-black active:border-orange-600',
        ]),
        'tertiary' => implode(' ', [
            'bg-transparent text-(--app-fg-color) border border-transparent',
            'hover:bg-orange hover:text-black',
            'active:bg-orange-600 active:text-black',
        ]),
    };

    $disabledClasses = implode(' ', [
        'bg-black-100 text-disabled border-black-100',
        'cursor-not-allowed pointer-events-none !transform-none',
    ]);

    // Compose final classes
    $classes = trim(implode(' ', array_filter([
        'cta-button',
        'cta-button--' . $variant,
        $base,
        $sizeClasses,
        $variantClasses,
        $disabled ? $disabledClasses : null,
    ])));
@endphp

<a
    href="{{ $disabled ? '#' : esc_url($url) }}"
    class="{{ $classes }} {{ $additionalClasses }}"
    @if($disabled)
        aria-disabled="true"
        tabindex="-1"
    @endif
    @if($ariaLabel)
        aria-label="{{ esc_attr($ariaLabel) }}"
    @endif
    @if($target === '_blank')
        target="_blank"
        rel="noopener noreferrer"
    @endif
>
    {{-- Icon left --}}
    @if($shouldRenderLeftIcon)
        <span class="cta-button__icon inline-flex items-center justify-center shrink-0 {{ $iconSizeClass }}" aria-hidden="true">
            @if($isMaterialIcon($leftIconName))
                @include('partials.material-icon', ['name' => $materialIconMap[$leftIconName], 'class' => $materialIconSizeClass])
            @else
                @include('partials.icons', ['name' => $leftIconName, 'color' => 'currentColor'])
            @endif
        </span>
    @endif

    {{-- Label --}}
    <span class="cta-button__label inline-block">{{ $label }}</span>

    {{-- Icon right --}}
    @if($shouldRenderRightIcon)
        <span class="cta-button__icon inline-flex items-center justify-center shrink-0 {{ $iconSizeClass }}" aria-hidden="true">
            @if($isMaterialIcon($rightIconName))
                @include('partials.material-icon', ['name' => $materialIconMap[$rightIconName], 'class' => $materialIconSizeClass])
            @else
                @include('partials.icons', ['name' => $rightIconName, 'color' => 'currentColor'])
            @endif
        </span>
    @endif
</a>
