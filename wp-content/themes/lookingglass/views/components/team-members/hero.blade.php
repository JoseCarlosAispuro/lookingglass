@php
    $block_name = 'team-member-hero';
    $anchor = $anchor ?? 'hero';
    $background_color = 'white';

    $class_name = 'pt-4 pb-12 md:pb-40';

    $fullName = $fullName ?? '';
    $firstName = $firstName ?? '';
    $lastName = $lastName ?? '';
    $headshotImage = $headshotImage ?? null;
    $roles = $roles ?? [];
@endphp

@component('partials.block', compact('block_name', 'class_name', 'background_color', 'anchor'))
<div class="relative container-ultrawide">
    <div class="grid grid-cols-12 pb-6 md:pb-4 border-b border-solid border-(--app-fg-color)/20 gap-y-6">
        <div class="col-span-12 md:col-span-7">
            <h1 data-word-animate data-animate-preset="wordUp" data-animate-delay="0" data-animate-duration="1.5" data-animate-stagger="0.1" class="headline-2 uppercase">
                @if(empty($firstName) && empty($lastName)) 
                    {{ $fullName }} 
                @else 
                    {{ $firstName }} 
                    {{ $lastName }} 
                @endif    
            </h1>
        </div>
        @if($headshotImage)
            <div class="relative group bg-black grayscale col-span-12 md:col-span-4 md:col-start-9 md:row-span-2">
                {!! wp_get_attachment_image($headshotImage, 'large', false, [
                    'class' => 'h-full w-full object-cover group-hover:contrast-[5] group-hover:brightness-[2] transition-all duration-10 ease-in-out',
                    'sizes' => '(min-width: 768px) 33vw, 100vw',
                ]) !!}
                <span
                class="mix-blend-multiply image-overlay opacity-0 group-hover:opacity-50 group-hover:brightness-[1] bg-black-400 absolute top-0 left-0 w-full h-full pointer-events-none transition-all duration-300 ease-in-out"></span>
            </div>
        @endif
        @if(count($roles) > 0)
            <div class="col-span-12 md:col-span-7 flex flex-col gap-6 md:gap-4 justify-end">
                @foreach($roles as $role)
                    <p class="headline-6 uppercase">{{$role['title']}} @if(!empty($role['since'])) ({{$role['since']}}) @endif</p>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endcomponent
