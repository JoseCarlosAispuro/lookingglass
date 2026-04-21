@php $body_class = get_body_class() @endphp

<!doctype html>
<html class="scroll-smooth" lang="en">
<head>
    <title>{{ wp_title('') }}</title>

    <meta charset="{{ get_bloginfo('charset') }}">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0, maximum-scale='2.0', minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://www.youtube.com/iframe_api"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=accessibility,add,arrow_back,arrow_downward,arrow_forward,arrow_left,arrow_outward,arrow_right,attach_money,audio_description,calendar_today,cancel,chair,check_circle,chevron_left,chevron_right,child_care,close,comedy_mask,confirmation_number,custom_typography,ear_sound,event_available,expand_less,expand_more,face,fastfood,fullscreen,headphones,link,location_on,menu,mode_heat,pause,phone_enabled,play_arrow,remove,schedule,sound_detection_dog_barking,widget_small,zoom_in" rel="stylesheet" />

    @php wp_head() @endphp
</head>
<body class="{{ join(' ', $body_class) }} group/nav antialiased">

    @include('partials.loader')
    @include('partials.navigation')

    {{-- Nav is fixed-position; add top padding on non-homepage pages so content clears it --}}
    <main class="{{ !is_front_page() && !is_singular( 'plays' ) ? 'pt-24 md:pt-28' : '' }}">
        @yield('content')
    </main>

    @include('partials.footer')
    @php wp_footer() @endphp
</body>
</html>
