@extends('layouts.master')

@php
    $pageTitle = get_the_title();
@endphp

@section('content')
    <div class="relative container-ultrawide">
        <div class="py-4 border-b border-border-secondary border-solid">
            <h1 
                data-word-animate
                data-animate-preset="wordUp"
                data-animate-delay="0"
                data-animate-duration="1.5"
                data-animate-stagger="0.1" 
                class="headline-1 font-semibold uppercase md:flex! md:justify-between!">
                {{$pageTitle}}
            </h1>
        </div>
    </div>

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

