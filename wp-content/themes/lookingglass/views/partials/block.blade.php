<section {{ isset($anchor) && $anchor ? "id={$anchor}" : '' }} class="section {{ join(' ', [$block_name, $class_name]) }}" data-component-id="{{ $block_name }}" data-block-background-color="{{$background_color}}" data-block-text-color="{{$text_color}}" data-bg-color="{{ get_palette_color_by_slug($background_color) ?? '#ffffff' }}" data-no-change="{{$no_bg_change}}">
    {!! $slot !!}
</section>

@unset($slot)
@unset($anchor)
@unset($block_name)
@unset($class_name)
