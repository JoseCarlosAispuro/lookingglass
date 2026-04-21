<div class="flex flex-col gap-y-4">
    @foreach($memberGroups as $group)
        <div class="w-full flex">
            <a href="#{{$group->slug}}" class="animated-underline button-lg after:bottom-[2px] md:after:bottom-[3px]">
                {{$group->name}}
            </a>
            @include('partials.material-icon', ['name' => 'arrow_downward', 'class' => 'icon-opsz-24 h-fit'])
        </div>
    @endforeach
    @if($hasEmeritusSections)
        <div class="w-full flex">
            <a href="#{{$emeritusSlug}}" class="animated-underline button-lg after:bottom-[2px] md:after:bottom-[3px]">{{$emeritusTitle}}</a>

            @include('partials.material-icon', ['name' => 'arrow_downward', 'class' => 'icon-opsz-24 h-fit'])
        </div>
    @endif
    @if($hasArtisticPartners)
        <div class="w-full flex">
            <a href="#{{$artisticPartnersSlug}}" class="animated-underline button-lg after:bottom-[2px] md:after:bottom-[3px]">{{$artisticPartnersTitle}}</a>

            @include('partials.material-icon', ['name' => 'arrow_downward', 'class' => 'icon-opsz-24 h-fit'])
        </div>
    @endif
</div>