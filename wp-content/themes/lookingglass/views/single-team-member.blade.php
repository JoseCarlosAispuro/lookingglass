@php
    $ID = get_the_ID();
    $fullName = get_field('full_name') ?? get_the_title();
    $firstName = get_field('first_name') ?? '';
    $lastName = get_field('last_name') ?? '';
    $headshotImage = get_field('headshot_image');
    $roles = get_field('roles');
    $intro = get_field('intro');
    $about = get_field('about');
    $email = get_field('email');
    $addGallery = get_field('add_gallery') ?? false;
    $gallery = get_field('gallery');
    $lookingglassProductions = get_field('lookingglass_productions');
    $career = get_field('career');
@endphp

@extends('layouts.master')

@section('content')
    @include('components.team-members.hero', compact('fullName', 'firstName', 'lastName', 'headshotImage', 'roles'))

    @include('components.team-members.about', compact('intro', 'about', 'email'))

    @if($addGallery)
        @include('components.media-gallery', [
            'block_name' => 'team-member-media-gallery',
            'background_color' => 'black',
            'galleryItems' => $gallery['gallery_items']
        ])
    @endif
    
    @if(!empty($lookingglassProductions))
        @include('components.team-members.productions', compact('lookingglassProductions'))
    @endif

    @if($career)
        @include('components.team-members.career', compact('career'))
    @endif
@endsection

@unset($ID)
@unset($fullName)
@unset($headshotImage)
@unset($roles)
@unset($intro)
@unset($about)
@unset($email)
@unset($addGallery)
@unset($gallery)
@unset($lookingglassProductions)
@unset($career)