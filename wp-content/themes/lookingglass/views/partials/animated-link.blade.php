{{--
    Animated Link Component

    Usage:
    @include('partials.animated-link', [
        'text' => 'Link text',
        'url' => 'https://example.com',
        'arrow' => true,           // Optional: show arrow (default: false)
        'target' => '_blank',      // Optional: link target (default: '_self')
        'class' => 'custom-class', // Optional: additional classes
    ])
--}}

@php
    $text = $text ?? '';
    $url = $url ?? '#';
    $arrow = $arrow ?? false;
    $target = $target ?? '_self';
    $class = $class ?? '';

    $baseClass = $arrow ? 'animated-link-arrow' : 'animated-link';
    $relAttr = $target === '_blank' ? 'noopener noreferrer' : '';
@endphp

<a
    href="{{ esc_url($url) }}"
    class="{{ $baseClass }} {{ $class }}"
    @if($target !== '_self') target="{{ $target }}" @endif
    @if($relAttr) rel="{{ $relAttr }}" @endif
>
    @if($arrow)
        <span class="animated-link-text">{{ $text }}</span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <line x1="7" y1="17" x2="17" y2="7"></line>
            <polyline points="7 7 17 7 17 17"></polyline>
        </svg>
    @else
        {{ $text }}
    @endif
</a>
