<section class="h-dvh" data-component-id="hero-block">
    <div class="relative container-ultrawide h-full flex items-end w-full">
        @if(isset($posterImage) && $posterImage)
            <div class="absolute top-0 left-0 w-full h-full">
                {!! wp_get_attachment_image($posterImage,'full', false, ['class' => !$posterMobileImage ? 'w-full h-full object-cover object-center' : 'hidden md:block w-full h-full object-cover object-center']) !!}
                @if($posterMobileImage)
                    {!! wp_get_attachment_image($posterMobileImage, 'large', false, ['class' => 'block md:hidden w-full h-full object-cover object-center', 'sizes' => '100vw']) !!}
                @endif
            </div>
        @endif
        @if($title || $credits || $playId)
            <div class="md:relative grid grid-cols-12 w-full z-1 py-sm gap-sm">
                <div
                    class="absolute top-0 -left-sm bg-gradient-to-b from-transparent from-50% md:from-0% to-black h-full w-[calc(100%+16px)] md:w-[calc(100%+32px)] -z-1"></div>
                <div class="flex md:hidden col-span-12 col-start-1 items-end">
                    @if($playId)
                        <p class="body-md text-white">{{get_play_dates($playId, true)}}</p>
                    @endif
                </div>
                <div class="col-span-12 md:col-span-6 col-start-1">
                    @if(isset($title) && $title)
                        <h1 class="display-md text-white">{{$title}}</h1>
                    @endif
                </div>
                <div class="col-span-12 md:col-span-3 col-start-1 md:col-start-7 flex flex-col justify-end gap-0 md:gap-1">
                    @if(isset($credits) && $credits && count($credits) > 0)
                        @foreach($credits as $credit)
                            <p class="body-md text-white">{{$credit['credit_copy']}}</p>
                        @endforeach
                    @endif
                </div>
                <div class="hidden md:flex col-span-2 col-start-11 items-end">
                    @if($playId)
                        <p class="body-md text-white">{{get_play_dates($playId, true)}}</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</section>
