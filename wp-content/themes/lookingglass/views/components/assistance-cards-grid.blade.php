<div class="col-span-12 col-start-1">
    <div class="w-full bg-black-100 rounded-2xl p-sm flex flex-col gap-y-lg md:gap-y-xl">
        <div class="grid grid-cols-12 gap-md md:gap-sm">
            <div class="col-span-12 md:col-span-4">
                @if(isset($heading) && $heading)
                    <h3 class="headline-5 uppercase">{{$heading}}</h3>
                @endif
            </div>
            <div class="col-span-12 md:col-span-6 col-start-1 md:col-start-7 flex items-end">
                @if(isset($description) && $description)
                    <p class="headline-6 uppercase">{{$description}}</p>
                @endif
            </div>
        </div>
        <div class="grid grid-cols-12 gap-sm">
            @foreach($hoursCards as $hoursCard)
                <div class="col-span-12 md:col-span-6">
                    <div
                        class="h-full w-full bg-white flex flex-col justify-between rounded-lg p-sm gap-10 md:gap-xl">
                        @if(isset($hoursCard['title']) && $hoursCard['title'])
                            <p class="headline-6 uppercase">{{$hoursCard['title']}}</p>
                        @endif
                        @if(isset($hoursCard['hours']) && $hoursCard['hours'] > 0)
                            <div class="flex flex-col gap-xs">
                                @include('partials.material-icon', ['name' => 'schedule', 'class' => 'inline-block md:!hidden'])
                                @foreach($hoursCard['hours'] as $hour)
                                    <div class="flex gap-x-xs">
                                        @include('partials.material-icon', ['name' => 'schedule', 'class' => '!hidden md:!inline-block'])
                                        <p class="headline-6 uppercase font-bold">{{$hour['hour']}}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @if(isset($hoursCard['notes']) && $hoursCard['notes'])
                            <div
                                class="body-md flex-grow-1 flex items-end">{!! $hoursCard['notes'] !!}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @if($address || $phone || $email)
            <div class="flex flex-col gap-y-sm md:gap-y-8">
                <h5 class="headline-6 uppercase">Contact Info</h5>
                <div class="flex flex-col gap-sm md:gap-0">
                    <a href="{{'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode($address) }}" target="_blank" class="flex gap-sm md:gap-[12px] items-center w-fit">
                        <span class="button-lg">{{$address}}</span>
                        <span class="p-[3px] md:p-xs rounded-full border border-black/20 flex hover:bg-(--app-fg-color)/10 transition-all ease-in-out">
                            @include('partials.material-icon', ['name' => 'location_on', 'class' => '!inline-block'])
                        </span>
                    </a>
                    <a href="{{'tel:' . $phone}}" target="_self" class="flex gap-sm md:gap-[12px] items-center w-fit">
                        <span class="button-lg">{{$phone}}</span>
                        <span class="p-[3px] md:p-xs rounded-full border border-black/20 flex hover:bg-(--app-fg-color)/10 transition-all ease-in-out">
                            @include('partials.material-icon', ['name' => 'phone_enabled', 'class' => '!inline-block'])
                        </span>
                    </a>
                    <button class="relative flex gap-sm md:gap-[12px] items-center w-fit" data-icon-cta data-copy-this-text="{{$email}}">
                        <span class="button-lg">{{$email}}</span>
                        <span class="p-[3px] md:p-xs rounded-full border border-black/20 flex hover:bg-(--app-fg-color)/10 transition-all ease-in-out">
                            @include('partials.material-icon', ['name' => 'link', 'class' => '!inline-block'])
                        </span>
                        <span id="tooltip-info" role="tooltip" data-cta-tooltip>Copied</span>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
