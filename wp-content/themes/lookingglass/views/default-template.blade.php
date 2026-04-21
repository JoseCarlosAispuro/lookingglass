@extends('layouts.master')

@section('content')

    @while(have_posts())
        @php
            the_post();
            $content = get_the_content();
            $blocks = parse_blocks($content) ?? [];
        @endphp

        @foreach($blocks as $block)
            @if(str_starts_with($block['blockName'] ?? '', 'core/shortcode'))
				{!! do_shortcode($block['innerHTML']) !!}
            @else
                {!! render_block($block) !!}
            @endif
        @endforeach

        @if(!has_blocks())
            {!! get_the_content() !!}
        @endif

    @endwhile

@endsection