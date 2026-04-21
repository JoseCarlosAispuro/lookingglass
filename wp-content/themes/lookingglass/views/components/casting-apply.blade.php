@php
    $class_name = 'w-full py-20 md:py-40';
@endphp

@component('partials.block', compact('block_name', 'class_name'))
    <div class="container-ultrawide grid grid-cols-12 gap-y-12 md:gap-y-40" data-casting-apply>
        <div class="col-span-12 order-1">
            <h1 
                data-word-animate 
                data-animate-preset="wordUp" 
                data-animate-delay="0" 
                data-animate-duration="1.5"
                data-animate-stagger="0.1" class="headline-3 font-semibold uppercase md:flex! md:justify-between!">
                {{$heading_primary}}
            </h1>
        </div>
        
        <div class="col-span-12 md:col-span-5 order-3 md:order-2">
            <div class="flex flex-col gap-y-4 md:gap-y-8">
                <h3 class="headline-6 font-semibold uppercase">
                    {{ $application_heading }}
                </h3>

                <div class="body-lg text-(--app-fg-color)">
                    {!! $application_instructions !!}
                </div>

                <div class="flex items-center">
                    @include('components.icon-cta', [
                        'text' => $contact_email,
                        'icon' => 'link',
                        'action' => 'copy',
                        'link' => null,
                        'text_to_copy' => $contact_email,
                        'fontWeight' => 'font-medium'
                    ])
                </div>
            </div>
        </div>

        <div class="col-span-12 md:col-span-6 md:col-start-7 md:row-span-2 order-2 md:order-3">
            <div class="relative overflow-hidden shrink-0">
                {!! wp_get_attachment_image($feature_image, 'large', false, [
                    'class' => 'w-full h-full object-cover',
                    'alt' => esc_attr($feature_image_alt),
                    'loading' => 'lazy',
                ]) !!}
            </div>
        </div>

        @if ($notes__disclaimers)
            <div class="col-span-12 md:col-span-5 md:col-start-1 content-end order-4">
                <div class="pt-4 border-t border-solid border-(--app-fg-color)/20">
                    <div class="body-sm" data-rich-text>
                        {!! $notes__disclaimers !!}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endcomponent
