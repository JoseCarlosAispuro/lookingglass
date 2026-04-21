@php
    $ID = get_the_ID();
    $playHeroImage = get_field('play_hero_poster_image');
    $playHeroImageMobile = get_field('play_hero_poster_cover_mobile_image');
    $playHeroTitle = get_field('play_hero_title');
    $playHeroCredits = get_field('play_hero_credits');
    $aboutHeading = get_field('about_heading');
    $aboutBody = get_field('about_body');
    $infoHeading = get_field('info_heading');
    $infoItems = get_field('info_items') ?: [];
    $locationsHeading = get_field('locations_heading');
    $locationsList = get_field('locations_list') ?: [];
    $castCrewHeading = get_field('cast_crew_heading');
    $castCrewGroups = get_field('cast_crew_groups') ?: [];
    $optionalSections = get_field('optional_sections');
    $calendarBannerIcon = get_field('banner_icon');
    $calendarBannerLabel = get_field('banner_label');
    $fromLabel = get_field('from_label');
    $fromPriceType = get_field('from_price_type');
    $fromPriceValue = get_field('from_price_value')
@endphp

@extends('layouts.master')

@section('content')
    @include('components.single-plays.single-hero', [
        'posterImage' => $playHeroImage,
        'posterMobileImage' => $playHeroImageMobile,
        'title' => $playHeroTitle,
        'credits' => $playHeroCredits,
        'playId' => $ID
    ])

    <section class="container-ultrawide mt-lg md:mt-30" data-component-id="post_detail" data-bg-color="#ffffff">
        <div class="grid grid-cols-12 gap-sm">
            <div class="col-span-12 md:col-span-6 col-start-1 md:col-start-2">
                @if($aboutHeading || $aboutBody)
                    <div class="flex flex-col gap-sm md:gap-y-8">
                        @if($aboutHeading)
                            <h2 class="headline-5 uppercase font-semibold">{{$aboutHeading}}</h2>
                        @endif
                        @if($aboutBody)
                            <div class="body-lg hb-spacing" data-rich-text data-legal-text>{!! $aboutBody !!}</div>
                        @endif
                    </div>
                @endif
                <div class="border-t border-black-100 mt-lg md:mt-xl">
                    @if(isset($infoHeading) && $infoHeading)
                        @component('components.single-plays.accordion-section', ['heading' => $infoHeading])
                            @if(isset($infoItems) && count($infoItems) > 0)
                                @foreach($infoItems as $item)
                                    <div class="w-full md:w-[calc(50%-8px)] flex flex-col md:flex-row gap-sm">
                                        <div class="w-md aspect-square flex-shrink-0">
                                            {!! wp_get_attachment_image($item['icon'],'thumbnail', false, []) !!}
                                        </div>
                                        <div>
                                            <p class="font-semibold">{{$item['label']}}</p>
                                            <p>{{$item['value']}}</p>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endcomponent
                    @endif
                    @if(isset($locationsHeading) && $locationsHeading)
                        @component('components.single-plays.accordion-section', ['heading' => $locationsHeading])
                            @if(isset($locationsList) && count($locationsList) > 0)
                                @foreach($locationsList as $index => $item)
                                    <div
                                        class="w-full flex flex-col gap-sm {{$index !== count($locationsList) - 1 ? 'border-b border-black-100 pb-sm' : ''}}">
                                        @if(isset($item['date_time_display']) && $item['date_time_display'])
                                            <div class="flex flex-col md:flex-row gap-sm">
                                                @include('partials.material-icon', ['name' => 'event_available'])
                                                <div class="flex flex-col gap-1">
                                                    <p class="body-lg font-semibold">Date/Time</p>
                                                    <p class="body-lg">{{$item['date_time_display']}}</p>
                                                </div>
                                            </div>
                                        @endif
                                        @if($item['location_name'] || $item['address_line'])
                                            <div class="flex flex-col md:flex-row gap-sm">
                                                @include('partials.material-icon', ['name' => 'location_on'])
                                                <div class="flex flex-col gap-1">
                                                    @if($item['location_name'])
                                                        <p class="body-lg font-semibold">{{$item['location_name']}}</p>
                                                    @endif
                                                    @if($item['address_line'])
                                                        <p class="body-lg">{{$item['address_line']}}</p>
                                                    @endif
                                                    @if(isset($item['directions_url']) && $item['directions_url']['url'])
                                                        @include('partials.link', [
                                                            'url' => $item['directions_url']['url'],
                                                            'label' => $item['directions_url']['title'],
                                                            'target' => $item['directions_url']['target'],
                                                            'linkSize' => 'lg'
                                                        ])
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        @endcomponent
                    @endif
                    @if(isset($castCrewHeading) && $castCrewHeading)
                        @component('components.single-plays.accordion-section', ['heading' => $castCrewHeading])
                            @if(isset($castCrewGroups) && count($castCrewGroups) > 0)
                                <div class="w-full gap-sm flex flex-col">
                                    @foreach($castCrewGroups as $index => $group)
                                        <div
                                            class="w-full gap-sm flex flex-col {{$index !== count($castCrewGroups) - 1 ? 'border-b border-black-100 pb-sm' : ''}}">
                                            <h3 class="headline-6 uppercase font-semibold">{{$group['cast_crew_group_title']}}</h3>
                                            <div class="flex flex-wrap gap-sm">
                                                @if($group['members_list'])
                                                    @foreach($group['members_list'] as $list)
                                                        @php
                                                            $writeName = $list['write_name'] ?? false;
                                                            $name = $list['name_input'] ?? '';

                                                            $memberId = null;
                                                            $imageID = null;
                                                            $positions = [];

                                                            if(!$writeName) {
                                                                $memberId = $list['member'][0];
                                                                $name = get_field('first_name', $memberId).' '.get_field('last_name', $memberId);
                                                                $imageID = get_field('headshot_image', $memberId);
                                                                $positions = get_field('roles', $memberId);
                                                            }
                                                            
                                                        @endphp
                                                        @include('components.team-member-card', [
                                                            'ID' => $memberId,
                                                            'name' => $name,
                                                            'imageID' => $imageID,
                                                            'positions' => $positions,
                                                            'position' => $list['play_role']
                                                        ])
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endcomponent
                    @endif
                </div>
            </div>
            <div class="hidden md:flex relative col-span-3 col-start-9">
                <div
                    class="opacity-0 sticky top-[130px] w-full h-fit bg-white transition-all duration-300 ease-in-out">
                    <div class="flex w-full bg-orange py-1 gap-1 items-center justify-center min-w-[272px]">
                        @include('partials.material-icon', ['name' => $calendarBannerIcon ?? '', 'class' => '!icon-opsz-16'])
                        <span class="body-xs font-medium">{{$calendarBannerLabel}}</span>
                    </div>
                    <div class="flex flex-col" data-plays-calendar data-play-id="{{$ID}}">
                        <div data-calendar></div>
                        <div data-calendar-times class="w-full min-w-[272px]"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="opacity-0 h-full flex md:hidden z-50 fixed bottom-16 w-full transition-all duration-300 ease-in-out"
         data-mobile-calendar>
        <div class="grid container-ultrawide grid-cols-12">
            <div
                class="w-screen max-w-full flex flex-col relative overflow-hidden col-span-12 col-start-1 bg-white p-sm rounded-lg border border-black-100 shadow-[0px_4px_20px_0px_#0000001F] transition-all duration-300 ease-in-out"
                data-mobile-calendar-wrapper>
                <div class="flex justify-between" data-mobile-calendar-heading>
                    <div class="flex flex-col gap-2">
                        <p class="body-xs font-thin">{{$fromLabel}}</p>
                        <p class="button-xl">{{$fromPriceValue}}</p>
                    </div>
                    <div class="flex w-fit h-fit bg-orange py-1 px-2 gap-1 items-center justify-center rounded-[2px]"
                         data-mobile-calenda-orange-ribbon>
                        @include('partials.material-icon', ['name' => $calendarBannerIcon ?? '', 'class' => '!icon-opsz-16'])
                        <span class="body-xs font-medium">{{$calendarBannerLabel}}</span>
                    </div>
                    <button class="hidden" data-mobile-calendar-close-button>
                        @include('partials.material-icon', ['name' => 'cancel', 'class' => '!icon-opsz-24'])
                    </button>
                </div>
                <div class="opacity-0 absolute flex w-full flex-col mt-md -z-1 transition-all duration-300 ease-in-out"
                     data-plays-calendar data-play-id="{{$ID}}">
                    <div data-calendar></div>
                    <div data-calendar-times class="w-full"></div>
                </div>
                <div class="mt-sm z-1 h-full flex items-end transition-all duration-300 ease-in-out">
                    <button data-mobile-calendar-button
                            class="cta-button cta-button--primary inline-flex items-center justify-center gap-2 p-sm whitespace-nowrap no-underline text-center select-none box-border transition-all duration-200 ease-in-out focus-visible:outline focus-visible:outline-[2px] focus-visible:outline-orange focus-visible:-outline-offset-[1px] max-md:w-full motion-reduce:transition-none motion-reduce:hover:transform-none button-lg bg-black text-white border border-black hover:bg-orange hover:text-black hover:border-orange active:bg-orange-600 active:text-black active:border-orange-600 w-full">
                        Get tickets
                    </button>
                </div>
            </div>
        </div>
    </div>
    @include('components.single-plays.optional-components')
@endsection

@php
    unset($ID);
    unset($playHeroImage);
    unset($playHeroImageMobile);
    unset($playHeroTitle);
    unset($playHeroCredits);
    unset($aboutHeading);
    unset($aboutBody);
    unset($infoHeading);
    unset($infoItems);
    unset($locationsHeading);
    unset($locationsList);
    unset($castCrewHeading);
    unset($castCrewGroups);
    unset($optionalSections);
    unset($calendarBannerIcon);
    unset($calendarBannerLabel);
    unset($fromLabel);
    unset($fromPriceType);
    unset($fromPriceValue);
@endphp
