@php
    $background_color = $background_color ?? 'black';
    $isGroup = count($ctas) > 1;

    $class_name = "bg-{$background_color} text-(--app-fg-color)";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
<div class="flex flex-col md:flex-row">
    @foreach ($ctas as $index => $cta)
        @include('components.button-block', [
            'buttonLink' => $cta['cta_button_link'],
            'variant' => $index === 0 ? 'primary' : 'secondary',
            'ariaLabel' => $cta['cta_button_aria_label'],
            'additionalClasses' => "headline-4 uppercase !whitespace-normal md:whitespace-nowrap !w-full".($isGroup ? ' !py-6' : ' !py-12 md:!py-18')
        ])
    @endforeach
</div>
@endcomponent

@unset($background_color, $isGroup, $class_name)
