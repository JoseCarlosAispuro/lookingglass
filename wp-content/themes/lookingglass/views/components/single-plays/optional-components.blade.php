@if(have_rows('optional_sections'))
    @while(have_rows('optional_sections'))
        @php
            the_row();
        @endphp
        @if(get_row_layout() === 'media_gallery')
            @include('components.media-gallery', [
                'block_name' => 'media_gallery_play',
                'background_color' => 'white',
                'galleryItems' => get_sub_field('gallery_items')
            ])
        @elseif(get_row_layout() === 'quotes_slider')
            @include('components.quotes-slider', [
                'block_name' => 'quotes-slider_play',
                'heading' => '',
                'hideHeading' => true,
                'quotes' => get_sub_field('quotes')
            ])
        @elseif(get_row_layout() === 'partner_logos_marquee')
            @include('components.partner-logos-marquee', [
                'background_color' => 'white',
                'block_name' => 'partner_logos_marquee_play',
                'heading' => get_sub_field('heading'),
                'image' => get_sub_field('image'),
                'supportingText' => get_sub_field('supporting_text'),
                'logos' => get_sub_field('logos')
            ])
        @elseif(get_row_layout() === 'cta_standalone')
            @include('components.cta-standalone', [
                'block_name' => 'cta-standalone_play',
                'ctas' => get_sub_field('ctas'),
            ])
        @endif
    @endwhile
@endif
