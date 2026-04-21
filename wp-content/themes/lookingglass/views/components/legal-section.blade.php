@php
    $background_color = $background_color ?? 'white';

    $class_name = "py-12 md:py-40 bg-{$background_color} text-(--app-fg-color)";
@endphp 

@component('partials.block', compact('block_name', 'class_name'))
<div class="relative container-ultrawide">
    <div class="grid grid-cols-12 gap-y-12">
        <div class="col-span-12 md:col-span-3 md:col-start-2">
            <h2 class="headline-4 uppercase sticky top-40">{{ $title }}</h2>
        </div>

        <div class="col-span-12 md:col-span-6 md:col-start-6">
            <div class="body-md flex flex-col gap-y-4 md:gap-y-6" data-rich-text data-legal-text>
                {!! $body !!}
            </div>
        </div>
    </div>
</div>
@endcomponent

@unset($background_color)
@unset($class_name)