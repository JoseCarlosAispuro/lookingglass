@php
    $background_color = $background_color ?? 'white';
    $class_name = "pb-20 md:pb-40 bg-{$background_color} text-(--app-fg-color)";
@endphp

@component('partials.block', compact('block_name', 'class_name'))
<div class="relative container-ultrawide flex flex-col gap-y-6 md:gap-y-12 md:gap-y-20">
    <div class="grid grid-cols-12 gap-x-4 gap-y-6 md:gap-4">
        <div class="col-span-12 md:col-span-4">
            <h2 class="headline-2 font-semibold uppercase">{{ $heading }}</h2>
        </div>
        <div class="col-span-12 md:col-span-8 md:col-start-5 flex items-end">
            <p class="body-xl">{{ $description }}</p>
        </div>

        <div class="relative col-span-12">
            @if($image_source === 'featured')
                @if (isset($featured_play) && !empty($featured_play))
                    @php
                        $thumbnail_id = get_post_thumbnail_id($featured_play->ID);
                        $thumbnail_url = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'full') : null;
                        $thumbnail_alt = $thumbnail_id ? get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true) : '';
                    @endphp

                    @if ($thumbnail_url)
                        <img src="{{ $thumbnail_url }}" alt="{{ $thumbnail_alt }}" class="w-full object-cover object-center aspect-2/3 md:aspect-16/9">
                    @endif
                @endif
            @elseif($image_source === 'override' && $image)
                <img src="{{$image['url']}}" alt="{{$image['alt']}}" class="w-full object-cover object-center aspect-2/3 md:aspect-16/9">
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
