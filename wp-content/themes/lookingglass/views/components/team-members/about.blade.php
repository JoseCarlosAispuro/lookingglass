@php
    $block_name = 'team-member-about';
    $anchor = $anchor ?? 'about';
    $background_color = 'gray';

    $class_name = 'py-12 md:pt-4 md:pb-40';

    $intro = $intro ?? '';
    $about = $about ?? '';
    $email = $email ?? '';
@endphp

@component('partials.block', compact('block_name', 'class_name', 'background_color', 'anchor'))
<div class="relative container-ultrawide">
    <div class="grid grid-cols-12 pb-4">
        <div class="col-span-12 md:col-span-4 md:col-start-2 gap-12">
            <h2 data-word-animate data-animate-preset="wordUp"
            data-animate-delay="0" data-animate-duration="1.5" data-animate-stagger="0.1" class="headline-3 uppercase sticky top-40">About</h2>
        </div>
        <div class="relative col-span-12 md:col-span-6 md:col-start-6 flex flex-col gap-8">
            @if($intro)
                <p class="body-xl">{{$intro}}</p>
            @endif

            @if($about)
                <div class="body-md flex flex-col gap-6 mb-8">
                    {!! $about !!}
                </div>
            @endif

            @if($email)
                <div class="border-t border-solid border-(--app-fg-color)/20 pt-4">
                    @include('components.icon-cta', [
                        "text" => $email,
                        "icon" => 'link',
                        "action" => 'copy',
                        "link" => null,
                        "text_to_copy" => $email
                    ])
                </div>
            @endif
        </div>
    </div>
</div>
@endcomponent
