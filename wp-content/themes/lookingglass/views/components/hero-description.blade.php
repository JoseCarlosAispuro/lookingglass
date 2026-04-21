@php
    $background_color = $background_color ?? 'white';
    $class_name = "py-4 bg-{$background_color} text-(--app-fg-color)";
@endphp 

@component('partials.block', compact('block_name', 'class_name'))
<div class="relative container-ultrawide flex flex-col gap-y-6 md:gap-y-12 md:gap-y-20">
    <div class="grid grid-cols-12 gap-y-4">
        <div class="col-span-12 md:col-span-8">
            <h2 class="headline-2 md:text-[120px]! uppercase break-all text-balance">{{ $heading }}</h2>
        </div>
        <div class="col-span-12 md:col-span-4 md:col-start-9 flex items-end">
            <p class="body-xl">{{ $description }}</p>
        </div>

        <div class="relative col-span-12">
            @if (!empty($image) && !empty($image['ID']))
                {!! wp_get_attachment_image($image['ID'], 'full', false, [
                    'class' => 'w-full object-cover object-center aspect-[2/2.67] md:aspect-16/9',
                ]) !!}
            @endif

            <div class="pt-4 border-t border-solid border-white absolute bottom-4 right-4">
                <span class="headline-5 font-semibold uppercase text-white">{{ $featured_play->post_title }}</span>
            </div>
        </div>
    </div>
</div>
@endcomponent

@unset($background_color)
@unset($class_name)