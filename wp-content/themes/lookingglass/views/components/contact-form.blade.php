@php
    $heading = get_field('heading');
    $description = get_field('description');
    $image = get_field('image');
    $cf7_form_id = (int) get_field('cf7_form');

    $background_color = $background_color ?? 'black';
    $class_name = "w-full bg-{$background_color} text-(--app-fg-color)";

    $image_id = $image['ID'] ?? ($image['id'] ?? null);
@endphp

@component('partials.block', compact('block_name', 'class_name'))
    <div class="container-ultrawide px-gutter pt-md pb-xl md:py-[160px]" data-contact-form>
        <div class="md:grid md:grid-cols-12 md:gap-x-sm">

            <div class="grid md:grid-cols-subgrid gap-x-sm md:col-span-10 md:col-start-2">
                {{-- Left Column: Content --}}
                <div class="md:col-span-4 flex flex-col gap-6 md:gap-8 mt-6 md:mt-0">
                    @if ($heading)
                        <h2
                            data-word-animate
                            data-animate-preset="wordUp"
                            data-animate-delay="0"
                            data-animate-duration="1.5"
                            data-animate-stagger="0.1"
                            class="headline-4 font-semibold uppercase"
                        >{{ $heading }}</h2>
                    @endif

                    @if ($description)
                        <p class="body-lg">{{ $description }}</p>
                    @endif
                </div>

                @if ($image_id)
                    <div class="mt-auto w-1/2 md:w-full md:col-span-2 row-start-1 md:row-start-2">
                        {!! wp_get_attachment_image($image_id, 'medium', false, [
                            'class' => 'aspect-4/5 object-cover',
                            'sizes' => '(min-width: 1024px) 20vw, 50vw',
                            'aria-hidden' => 'true',
                            'loading' => 'lazy',
                        ]) !!}
                    </div>
                @endif

                {{-- Right Column: Form --}}
                <div class="md:col-span-5 md:col-start-6 md:row-span-2 mt-12 md:mt-0">
                    @if ($cf7_form_id && shortcode_exists('contact-form-7'))
                        <div data-cf7-form>
                            {!! do_shortcode('[contact-form-7 id="' . $cf7_form_id . '"]') !!}
                        </div>
                    @elseif (current_user_can('manage_options'))
                        @if (!$cf7_form_id)
                            <p class="body-md text-(--app-fg-color)/60">No contact form selected. Please choose a form in the block settings.</p>
                        @elseif (!shortcode_exists('contact-form-7'))
                            <p class="body-md text-primary-red">Contact Form 7 plugin is not active. Please install and activate it to display this form.</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endcomponent

@unset($heading, $description, $image, $image_id, $cf7_form_id, $background_color, $class_name)
