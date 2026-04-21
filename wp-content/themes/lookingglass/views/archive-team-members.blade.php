@php 
    $hero = get_field('hero', 'option');
    $memberGroups = get_field('groups', 'option');
    $emeritusTitle = get_field('emeritus_title', 'option') ?? 'Emetirus Board';
    $emeritusSlug = sanitize_title($emeritusTitle);
    $emeritusSections = get_field('sections', 'option');
    $artisticPartnersTitle = get_field('artistic_partners_title', 'option') ?? 'Artistic Partners';
    $artisticPartnersSlug = sanitize_title($artisticPartnersTitle);
    $artisticPartners = get_field('artistic_partners', 'option');
@endphp

@extends('layouts.master')

@section('content')
    @if(!empty($hero))
        @include('components.split-hero', [
            ...$hero, 
            'block_name' => 'archive-tm-split-hero',
            'background_color' => 'white'
        ])
    @endif

    <section class="w-full md:py-30">
        <div class="container-ultrawide grid grid-cols-12">
            <div class="col-span-3 hidden md:block">
                <div class="sticky top-40">
                    @include('components.team-members.archive-anchors', [
                        'memberGroups' => $memberGroups,
                        'hasEmeritusSections' => !empty($emeritusSections),
                        'emeritusTitle' => $emeritusTitle,
                        'emeritusSlug' => $emeritusSlug,
                        'hasArtisticPartners' => !empty($artisticPartners),
                        'artisticPartnersTitle' => $artisticPartnersTitle,
                        'artisticPartnersSlug' => $artisticPartnersSlug,
                    ])
                </div>
            </div>

            <div class="col-span-12 md:col-span-8 md:col-start-5 py-12 md:py-0 flex flex-col gap-y-20 md:gap-y-0">
                @if(!empty($memberGroups))
                    @include('components.team-members.archive-members-groups', [
                        'memberGroups' => $memberGroups,
                        'background_color' => 'white'
                    ])
                @endif

                @if(!empty($emeritusSections))
                    @include('components.team-members.archive-emeritus-groups', [
                        'emeritusSlug' => $emeritusSlug,
                        'emeritusSections' => $emeritusSections,
                        'background_color' => 'gray'
                    ])
                @endif

                @if(!empty($artisticPartners))
                    @include('components.team-members.archive-artistic-partners', [
                        'artisticPartnersSlug' => $artisticPartnersSlug,
                        'artisticPartnersTitle' => $artisticPartnersTitle,
                        'artisticPartners' => $artisticPartners,
                        'background_color' => 'gray'
                    ])
                @endif
            </div>
        </div>
    </section>
@endsection